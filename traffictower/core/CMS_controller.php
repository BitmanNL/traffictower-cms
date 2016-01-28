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
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extends CI_Controller.
 * Used as basic controller for Frontend_controlelr and Admin_controller.
 * Can be used for general methods accessed by all frontend and admin controllers.
 */
class CMS_controller extends CI_Controller
{

	/**
	 * @var array $views Holds the view to be outputted in the layout
	 */
	protected $views = array();

	/**
	 * @var array $data General data array to be outputted in the layout. Equivalent to views.
	 */
	protected $data = array();

	/**
	 * @var mixed $css Holds the css to insert in the layout
	 */
	protected $css = array();

	/**
	 * @var mixed $css_files Holds the list of css files to insert in the layout
	 */
	protected $css_files = array();

	/**
	 * @var mixed $javascript Holds the javascript to insert in the layout
	 */
	protected $javascript = array();

	/**
	 * @var mixed $javascript_files Holds the list of javascript files to insert in the layout
	 */
	protected $javascript_files = array();

	/**
	 * @var array $javascript_params List of custom javascript parameters used in Javascript-files in assets folder.
	 */
	protected $javascript_params = array();

    /**
     * @var array $preloaded_javascript_files Holds a list of JavaScript files
     * to insert in the layout before $javascript_files and $javascript_params.
     */
    protected $preloaded_javascript_files = array();

	/**
	 * Constructor, used to implement the settings in config/cms.php
	 */
	public function __construct()
	{
		parent::__construct();

		// load the cms configuration
		$this->config->load('cms');

		// enable or disable firephp based on the settings in the config
		$this->firephp->setEnabled($this->config->item('cms_firephp_enabled'));
	}

	/**
	 * Set custom CSS content in css array.
	 *
	 * @param string $css Custom CSS content
	 */
	public function set_css($css)
	{
		$this->css[] = $css;
	}

	/**
	 * Set custom CSS file in css_files array.
	 *
	 * @param array $css_files Custom CSS files
	 */
	public function set_css_files($css_files = array())
	{
		if (!is_array($css_files))
		{
			$css_files = func_get_args();
		}
		$this->css_files = array_merge($this->css_files, $css_files);
	}

	/**
	 * Set custom Javascript content in JavaScript array.
	 *
	 * @param string $javascript Custom Javascript content
	 */
	public function set_javascript($javascript)
	{
		$this->javascript[] = $javascript;
	}

	/**
	 * Set custom Javascript file in javascript_files array.
	 *
	 * @param array $javascript_files Custom Javascript files
	 */
	public function set_javascript_files($javascript_files = array())
	{
		if (!is_array($javascript_files))
		{
			$javascript_files = func_get_args();
		}
		$this->javascript_files = array_merge($this->javascript_files, $javascript_files);
	}

	/**
	 * Set custom javascript parameter(s).
	 *
	 * @param mixed $key Javascript parameter key or Array of parameter key values
	 * @param mixed $value Javascript parameter value in case of key scenario
	 */
	public function set_javascript_param($key, $value = NULL)
	{
		if (is_array($key))
		{
			$this->javascript_params = array_merge($this->javascript_params, $key);
		}
		else
		{
			$this->javascript_params[$key] = $value;
		}
	}

    /**
     * Set custom preloaded JavaScript file in the $preloaded_javascript_files array.
     *
     * @param array|string ...$preloaded_javascript_files An array of files to
     *     preload or multiple strings for each file.
     *
     * @return void
     */
    public function set_preloaded_javascript_files($preloaded_javascript_files)
    {
        if (!is_array($preloaded_javascript_files))
        {
            $preloaded_javascript_files = func_get_args();
        }

        $this->preloaded_javascript_files = array_merge(
            $this->preloaded_javascript_files,
            $preloaded_javascript_files
        );
    }

	/**
	 * Get custom javascript parameters.
	 *
	 * @param mixed $key Optional parameter key
	 * @param boolean $convert_to_js Convert all values to js equivalent type
	 * @return mixed Parameter value
	 */
	public function get_javascript_param($key = NULL, $convert_to_js = FALSE)
	{
		if (is_null($key))
		{
			$params = $this->javascript_params;

			if ($convert_to_js)
			{
				foreach ($params as $key => $value)
				{
					$params[$key] = $this->_convert_to_javascript($value);
				}
			}

			return $params;
		}
		else if (isset($this->javascript_params[$key]))
		{
			$param = $this->javascript_params[$key];

			if ($convert_to_js)
			{
				$param = $this->_convert_to_javascript($param);
			}

			return $param;
		}
		else
		{
			$param = NULL;

			if ($convert_to_js)
			{
				$param = $this->_convert_to_javascript($param);
			}

			return $param;
		}
	}

	/**
	 * Convert PHP value types to javascript value types.
	 *
	 * @param mixed $value PHP value
	 * @return mixed Javascript equivalent value
	 */
	protected function _convert_to_javascript($value)
	{
		if (is_null($value))
		{
			return 'null';
		}
		else if (is_string($value))
		{
			return "'" . $value . "'";
		}
		else if (is_bool($value))
		{
			return ($value) ? 'true' : 'false';
		}
		else if (is_array($value))
		{
			return json_encode($value);
		}
		else
		{
			return $value;
		}
	}

	/**
	 * Set custom view. If already present, added to the existing view key value.
	 *
	 * @param string $key View key
	 * @param string $value View value
	 * @param boolean $append Append view to existing view data
	 */
	public function set_views($key, $value, $append = TRUE)
	{
		if (isset($this->views[$key]) && $append)
		{
			$this->views[$key] .= $value;
		}
		else
		{
			$this->views[$key] = $value;
		}
	}

	/**
	 * Set custom data key-value. Will overwrite all existing data key value.
	 *
	 * @param string $key   Data key
	 * @param mixed $value Data value
	 */
	public function set_data($key, $value)
	{
		$this->data[$key] = $value;
	}

	protected function _404_on_empty($data)
	{
		if (empty($data))
		{
			show_404();
		}
	}


	/**
	 * Makes creates a json data object and returns it to the client
	 * Also settings the headers correct to prevent caching by the browser
	 *
	 * @param mixed[] $data The array to create a json from
	 * @param integer $http_code Optional HTTP status code (default 200)
	 * @param  string $http_code_message Optional HTTP status message (needed if status code is not present in CI's set_status_header list)
	 */
	protected function _json_response($data = array(), $http_code = 200, $http_code_message = '')
	{
		// set http code
		set_status_header($http_code, $http_code_message);

		// set ajax headers
		$this->output->set_header("Expires: 0");
		$this->output->set_header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
		$this->output->set_header("Pragma: no-cache");
		$this->output->set_header('Content-type: application/json; charset=utf-8');

		if ($this->input->get('callback') !== FALSE)
		{
			$callback = $this->input->get('callback', TRUE);
			if (empty($callback) || $callback == '?')
			{
				$callback = 'response';
			}

			// output the jsonp
			$this->load->view('layouts/jsonp_response', array('json' => json_encode($data), 'callback' => $callback));
		}
		else
		{
			// output the json
			$this->load->view('layouts/json_response', array('json' => json_encode($data)));
		}
	}

}
