<?php
/**
 * LICENSE: Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @package   CMS\Core
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Basic REST interface controller for all REST controllers to extend from.
 * For POST request, global CodeIgniter CSRF must be disabled!
 */
class REST_controller extends CI_Controller
{

	/**
	 * Current request type (get, post, put, delete)
	 * @var string
	 */
	private $_request_type = 'get';

	/**
	 * Current query variables send with request
	 * @var array
	 */
	private $_request_vars = FALSE;
	private $_get_vars = FALSE;
	private $_post_vars = FALSE;
	private $_put_vars = FALSE;
	private $_delete_vars = FALSE;

	/**
	 * Current method variables send with request
	 * @var array
	 */
	private $_method_vars = array();

	/**
	 * Current method name, including request type
	 * @var string
	 */
	private $_method = '';


	/**
	 * Constructor.
	 * Gets all query variables, method variables and initializes custom methods with prefix [request_type]_[method_name]().
	 * Has some base checks on existing methods.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->benchmark->mark('REST_start');

		// Load hook
		$this->_construct_hook();

		$this->_detect_request_method();
		$this->_method = $this->router->method.'_'.$this->_request_type;

		if(!$this->_method_exists($this->_method))
		{
			$this->error('Method does not exist', 100, 404);
		}
		else
		{
			// get object method variables (and check for requirements)
			$this->_get_method_vars();

			$this->_get_request_vars();

			// do actual controller method call
			call_user_func_array(array($this, $this->_method), $this->_method_vars);
		}

		// no need to continue
		exit;
	}

	/**
	 * Hook to enable __construct use in extended REST controllers.
	 */
	protected function _construct_hook()
	{
		// Do not add directly in this method. Copy to custom controller
	}

	/**
	 * Get query variables, based on request type.
	 *
	 * @return array Query variables
	 */
	public function request($key = NULL)
	{
		if (!is_null($key))
		{
			return isset($this->_request_vars[$key]) ? $this->_request_vars[$key] : FALSE;
		}
		else
		{
			return $this->_request_vars;
		}
	}

	/**
	 * Get GET variables, based on request type.
	 *
	 * @return array GET variables
	 */
	public function get($key = NULL)
	{
		if (!is_null($key))
		{
			return isset($this->_get_vars[$key]) ? $this->_get_vars[$key] : FALSE;
		}
		else
		{
			return $this->_get_vars;
		}
	}

	/**
	 * Get POST variables, based on request type.
	 *
	 * @return array POST variables
	 */
	public function post($key = NULL)
	{
		if (!is_null($key))
		{
			return isset($this->_post_vars[$key]) ? $this->_post_vars[$key] : FALSE;
		}
		else
		{
			return $this->_post_vars;
		}
	}

	/**
	 * Get PUT variables, based on request type.
	 *
	 * @return array PUT variables
	 */
	public function put($key = NULL)
	{
		if (!is_null($key))
		{
			return isset($this->_put_vars[$key]) ? $this->_put_vars[$key] : FALSE;
		}
		else
		{
			return $this->_put_vars;
		}
	}

	/**
	 * Get DELETE variables, based on request type.
	 *
	 * @return array DELETE variables
	 */
	public function delete($key = NULL)
	{
		if (!is_null($key))
		{
			return isset($this->_delete_vars[$key]) ? $this->_delete_vars[$key] : FALSE;
		}
		else
		{
			return $this->_delete_vars;
		}
	}

	/**
	 * Set and check request for required query variables.
	 *
	 * @param  mixed  $fields Query variables which are required (string or array)
	 */
	public function required($fields = array())
	{
		if(!is_array($fields))
		{
			$fields = func_get_args();
		}

		$missing_fields = is_array($this->_request_vars) ? array_diff($fields, array_keys($this->_request_vars)) : $fields;

		if(!empty($missing_fields))
		{
			$this->error('Parameters required: '.implode(", ", $missing_fields), 102);
		}
	}

	/**
	 * Output error.
	 *
	 * @param string $message Error message
	 * @param mixed $error_code Error code (optional)
	 * @param integer $http_code Optional HTTP status code (default 200)
	 * @param string $http_code_message Optional HTTP status message (needed if status code is not present in CI's set_status_header list)
	 */
	public function error($message, $error_code = NULL, $http_code = 200, $http_code_message = '')
	{
		$data['error']['message'] = $message;
		if(!is_null($error_code))
		{
			$data['error']['code'] = $error_code;
		}

		$this->_json_output($data, $http_code, $http_code_message);
	}

	/**
	 * Output data to (succesfull) response.
	 *
	 * @param array $data Result data to return
	 * @param integer $http_code Optional HTTP status code (default 200)
	 * @param string $http_code_message Optional HTTP status message (needed if status code is not present in CI's set_status_header list)
	 */
	public function response($data, $http_code = 200, $http_code_message = '')
	{
		$output['results'] = $data;

		// timer
		$this->benchmark->mark('REST_stop');
		$output['execution_time'] = floatval($this->benchmark->elapsed_time('REST_start', 'REST_stop'));

		$this->_json_output($output, $http_code, $http_code_message);
	}

	/**
	 * Get current request type.
	 * GET, POST, PUT, DELETE.
	 *
	 * @return string Request type
	 */
	public function request_type()
	{
		return $this->_request_type;
	}

	/**
	 * Detect current request method (get, post, put delete).
	 */
	private function _detect_request_method()
	{
		$this->_request_type = strtolower($this->input->server('REQUEST_METHOD'));
	}

	/**
	 * Check if given method exists and is public
	 * @param  string $method Method name
	 * @return boolean Method exists
	 */
	private function _method_exists($method = NULL)
	{
		if (!method_exists($this->router->class, $method))
		{
			return FALSE;
		}

		// check if method is public
		$reflection = new ReflectionMethod($this->router->class, $method);
		if (!$reflection->isPublic())
		{
			return FALSE;
		}

		return TRUE;
	}

	/**
	 * Retrieve query variables based on request type.
	 */
	private function _get_request_vars()
	{
		if($this->_request_type == 'post')
		{
			$this->_request_vars = $this->input->post();
		}
		else if($this->_request_type == 'put' || $this->_request_type == 'delete')
		{
			parse_str(file_get_contents('php://input'), $this->_request_vars);
		}
		else
		{
			$this->_request_vars = $this->input->get();
		}

		// if there is one var present but not in an array
		if($this->_request_vars !== FALSE && !is_array($this->_request_vars))
		{
			$this->_request_vars = array($this->_request_vars);
		}

		// add vars to request type param
		$this->{'_'.$this->_request_type.'_vars'} = $this->_request_vars;
	}

	/**
	 * Retrieve method variables by CI's segments.
	 * Check on requirements of variables in current method.
	 */
	private function _get_method_vars()
	{
		// get optional method variables
		$this->_method_vars = array();
		foreach($this->uri->ruri_to_assoc() as $key => $val)
		{
			if(trim($key) != '')
			{
				$this->_method_vars[] = $key;
			}
			if(trim($val) != '')
			{
				$this->_method_vars[] = $val;
			}
		}

		// check number of object method parameters
		$reflection = new ReflectionClass($this->router->class);
		$number_of_needed_params = $reflection->getMethod($this->_method)->getNumberOfRequiredParameters();

		if(count($this->_method_vars) < $number_of_needed_params)
		{
			// not all parameters are present
			$object_params = array();
			foreach($reflection->getMethod($this->_method)->getParameters() as $param)
			{
				$object_params[] = $param->name;
			}

			$this->error('Object parameters required: '.implode(', ', $object_params), 101);
		}
	}

	/**
	 * JSON output format with suitable headers.
	 *
	 * @param array $data Data to output
	 * @param integer $http_code Optional HTTP status code (default 200)
	 * @param string $http_code_message Optional HTTP status message (needed if status code is not present in CI's set_status_header list)
	 */
	private function _json_output($data, $http_code = 200, $http_code_message = '')
	{
		// set http code
		set_status_header($http_code, $http_code_message);

		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
		header("Pragma: no-cache");
		header('Content-type: application/json; charset=utf-8');

		if ($this->input->get('callback') !== FALSE)
		{
			$callback = $this->input->get('callback', TRUE);
			if (empty($callback) || $callback == '?')
			{
				$callback = 'response';
			}
			echo $callback . '(' . json_encode($data) . ');';
		}
		else
		{
			echo json_encode($data);
		}

		exit;
	}

}

/* End of file REST_controller.php */
/* Location: ./application/controllers/REST_controller.php */
