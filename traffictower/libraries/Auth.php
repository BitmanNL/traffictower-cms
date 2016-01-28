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
 * @package   CMS\Core\Libraries
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library: Handles user authentication for front and admin.
 */
class Auth
{
	/**
	 * @param Object $ci Holds the instance of the CodeIgniter methods
	 */
	protected $ci;

	protected $user_id = NULL;

	/**
	 * @param Integer $_log_history_minutes number of minutes to look back in the log
	 */
	protected $_log_history_minutes = 5;

	/**
	 * @param Integer $_max_number_failed_attempts the max number of allowed failed attemps within $_log_history_minutes
	 */
	protected $_max_number_failed_attempts = 5;

	/**
	 * Construct
	 *
	 * Set the CI instance.
	 */
	public function __construct()
	{
		// set the CI instance so we can access the CI-libraries
		$this->ci =& get_instance();

		// session library is no longer authomatically loaded
		$this->ci->load->library('session');

		$this->ci->load->helper('auth');
	}

	/**
	 * Get current logged in user id
	 * @return integer User id
	 */
	protected function _get_user_id()
	{
		return $this->user_id;
	}

	/**
	 * Set current logged in user id
	 * @param integer $user_id User id
	 */
	protected function _set_user_id($user_id)
	{
		$this->user_id = intval($user_id);
	}

	/**
	 * Check if the user is logged in and retrieve user info if present
	 *
	 * @return bool TRUE if the user is logged in
	 */
	public function check_login()
	{
		$user_id = $this->ci->session->userdata('auth_user_id');

		if ($user_id !== FALSE)
		{
			$this->ci->load->model('shared/user_model');
			$user = $this->ci->user_model->get_user($user_id, TRUE);

			if (empty($user))
			{
				$this->logout();
				return FALSE;
			}

			$this->_set_user_id($user['id']);

			return TRUE;
		}

		return FALSE;
	}



	/**
	 * Checks if the current controller with method is allowed for the current user
	 *
	 * @param array $auth_user_groups Auth user group rules for current controller/method
	 * @return boolean TRUE if the user is allowed to access the controller/method
	 */
	public function check_auth_user_groups_for_controller($auth_user_groups)
	{
		$current_method = $this->ci->router->fetch_method();

		if(isset($auth_user_groups[$current_method]))
		{
			// method specifieke rechten
			return $this->auth_user_groups($auth_user_groups[$current_method]);
		}
		else if(isset($auth_user_groups['*']))
		{
			// controller algemene rechten
			return $this->auth_user_groups($auth_user_groups['*']);
		}
		else
		{
			// geen rechten ingesteld = openbaar toegankelijk
			return TRUE;
		}
	}

	/**
	 * Authenticate a user using email and password.
	 * Log the user in if authentication is valid
	 *
	 * @param string $email Email address of the user
	 * @param string $password Password of the user
	 *
	 * @return bool True is user is authenticated
	 */
	public function login($email, $password)
	{
		$this->ci->load->model('shared/user_model');
		$this->ci->load->model('shared/log_model');

		$user = $this->ci->user_model->get_user_by_email($email);
		$user_id = isset($user['id']) ? $user['id'] : NULL;

		// check number of login attempts
		if (!is_null($user_id))
		{
			$this->_check_login_attempts_user($user_id, 'login_attempt_failed');
		}
		$this->_check_login_attempts_ip_address('login_attempt_failed');

		// actual check
		if (!empty($user) AND isset($user['is_active']) AND $user['is_active'] === 'yes')
		{
			// match the hashes
			$this->ci->load->library('bcrypt', 9);

			if ($this->ci->bcrypt->verify($password, $user['password']))
			{
				$this->set_logged_in($user, 'Successful classic login');
				return TRUE;
			}
			else
			{
				$this->ci->log_model->log('login_attempt_failed', 'Classic login attempt failed, wrong password', $user_id);
				return FALSE;
			}
		}
		else if (!empty($user) AND isset($user['is_active']) AND $user['is_active'] === 'no')
		{
			$this->ci->log_model->log('login_attempt_failed', 'Classic login attempt failed, inactive user', $user_id);
			return FALSE;
		}

		$this->ci->log_model->log('login_attempt_failed', 'Classic login attempt failed, unknown user, username: '.$email);
		return FALSE;
	}

	/**
	 * Authenticate a user using Persona.
	 * Log the user in if authentication is valid
	 *
	 * @param string $assertion Persona assertion
	 *
	 * @return bool True is user is authenticated
	 */
	public function persona_login($assertion)
	{
		$this->ci->load->model('shared/log_model');
		$this->ci->load->model('shared/user_model');

		$email = $this->_get_verified_persona_user($assertion);

		// check if we know this user
		$user = $this->ci->user_model->get_user_by_email($email);

		$this->ci->config->load('auth', TRUE);
		$superuser_domain = $this->ci->config->item('superuser_domain', 'auth');

		// Gebruiker bestaat niet en heeft een superuser adres,
		// dus aanmaken als superuser.
		if (!empty($superuser_domain))
		{
			if (empty($user) && strpos($email, '@' . $superuser_domain) !== FALSE)
			{
				$user = $this->_create_superuser($email);
			}
		}

		// check that is user exists and is active
		if (!empty($user) AND isset($user['is_active']) AND $user['is_active'] === 'yes')
		{
			// log this user in
			$this->set_logged_in($user, 'Successful Persona login');
			return TRUE;
		}
		else if (!empty($user) AND isset($user['is_active']) AND $user['is_active'] === 'no')
		{
			$this->ci->log_model->log('login_attempt_failed', 'Persona login attempt failed, inactive user', $user['id']);
			return FALSE;
		}

		$this->ci->log_model->log('login_attempt_failed', 'Persona login attempt failed, unknown user, username: ' . $email);
		return FALSE;
	}

	/**
	 * Set a session for the user
	 *
	 * @param string $user User-record
	 */
	public function set_logged_in($user, $log_message)
	{
		// set user_id
		$this->ci->session->set_userdata('auth_user_id', $user['id']);

		// log succesful login
		$this->ci->load->model('shared/log_model');
		$this->ci->log_model->log('login_success', $log_message, $user['id']);
	}

	/**
	 * Check the server HTTP_HOST against de expexted host base and get host name from server
	 *
	 * @return string Verified HTTP HOST
	 */
	protected function _get_host_name()
	{
		$this->ci->load->model('shared/log_model');

		$this->ci->config->load('persona', TRUE);
		$host_base = $this->ci->config->item('host_base', 'persona');

		if (empty($host_base))
		{
			show_error("Er is geen host_base opgegeven in de persona config file. Dit is verplicht!");
		}

		if (strpos($this->ci->input->server('HTTP_HOST'), $host_base) === FALSE)
		{
			$this->ci->log_model->log('persona_login_hostbase_error', "De HTTP_HOST: {$this->ci->input->server('HTTP_HOST')} past niet binnen de opgegeven host_base: {$host_base}!", NULL);
			show_error("De HTTP_HOST: {$this->ci->input->server('HTTP_HOST')} past niet binnen de opgegeven host_base: {$host_base}!");
		}

		return $this->ci->input->server('HTTP_HOST');
	}

	/**
	 * Get verified Persona user by post curl call
	 *
	 * @param  string $assertion Persona assertion
	 * @return string Verified Persona email
	 */
	protected function _get_verified_persona_user($assertion)
	{
		$this->ci->load->library('curl');
		$this->ci->load->model('shared/log_model');

		// check the server HTTP_HOST against de expexted host base and get host name from server
		$host_name = $this->_get_host_name();

		$params = array(
			'assertion' => $assertion,
			'audience' => $host_name
		);

		$result = $this->ci->curl->simple_post('https://verifier.login.persona.org/verify', $params);

		if ($result === FALSE)
		{
			$this->ci->log_model->log('persona_connection_error', 'cURL error: '.$this->ci->curl->error_string);
			throw new Exception('PERSONA_VERIFICATION_FAILURE');
		}
		else
		{
			$persona_data = json_decode($result, TRUE);
			if (isset($persona_data['status']) && $persona_data['status'] === 'okay')
			{
				if (isset($persona_data['email']) && !empty($persona_data['email']))
				{
					return $persona_data['email'];
				}
				else
				{
					$this->ci->log_model->log('persona_verification_failure', 'Received status okay but no email address');
					throw new Exception('PERSONA_VERIFICATION_FAILURE');
				}
			}
			else
			{
				$reason = isset($persona_data['reason']) ? $persona_data['reason'] : 'Unreadable response from verify call';
				$this->ci->log_model->log('persona_verification_failure', $reason);
				throw new Exception('PERSONA_VERIFICATION_FAILURE');
			}
		}
	}

	/**
	 * Gebruiker aanmaken als superuser
	 *
	 * @param  string $email E-mailadres
	 * @return  array User
	 */
	protected function _create_superuser($email)
	{
		$this->ci->load->model('shared/log_model');
		$this->ci->load->model('shared/user_model');

		$screen_name = ucfirst(preg_replace('/(.*)@.*/', '$1', $email));
		$user_id = $this->ci->user_model->create_user($email, $screen_name, 'yes', 'yes');
		$user = $this->ci->user_model->get_user($user_id);

		$this->ci->log_model->log('superuser_created', 'New superuser account created', $user_id);

		return $user;
	}

	/**
	 * Log the user out
	 */
	public function logout()
	{
		$this->ci->session->unset_userdata('auth_user_id');
		$this->_unset_native_session('auth_logged_in');
	}

	/**
	 * Start native php session if not started yet
	 *
	 * @param string $key Session key
	 * @param string $value Session value
	 */
	public function set_native_session($key, $value)
	{
		if (!isset($_SESSION)){
            session_start();
        }
        $_SESSION[$key] = $value;
	}

	/**
	 * End native php session
	 *
	 * @param string $key Session key
	 */
	protected function _unset_native_session($key)
	{
		if (!isset($_SESSION)){
            session_start();
        }
        unset($_SESSION[$key]);
	}

	/**
	 * Check the number of login attempts by user, with an exception when exceeded
	 *
	 * @param integer $user_id User id
	 * @param string $action Log action
	 */
	protected function _check_login_attempts_user($user_id, $action)
	{
		$this->ci->load->model('shared/log_model');

		$params = array(
			'since' => ($this->_log_history_minutes * 60),
			'action' => $action,
			'user_id' => $user_id,
			'get_number_of_records' => TRUE
		);

		$number_user_login_attempts = $this->ci->log_model->get_logs($params);

		if ($number_user_login_attempts > $this->_max_number_failed_attempts)
		{
			throw new Exception('MAX_LOGIN_ATTEMPT_EXCEEDED');
		}

	}

	/**
	 * Check the number of login attempts by ip address, with an exception when exceeded
	 *
	 * @param string $action Log action
	 */
	protected function _check_login_attempts_ip_address($action)
	{
		$this->ci->load->model('shared/log_model');

		$params = array(
			'since' => ($this->_log_history_minutes * 60),
			'action' => $action,
			'ip_address' => $this->ci->input->ip_address(),
			'get_number_of_records' => TRUE
		);

		$number_ip_login_attempts = $this->ci->log_model->get_logs($params);

		if ($number_ip_login_attempts > $this->_max_number_failed_attempts)
		{
			throw new Exception('MAX_LOGIN_ATTEMPT_EXCEEDED');
		}
	}

	/**
	 * Get data of the logged in user. Accepts a field name or when empty returns full data array
	 *
	 * @param string $field Name of the field to retreive or empty for all
	 * @return mixed[] Requested item or array of all items
	 */
	public function get_user_data($field = NULL)
	{
		$user_id = $this->_get_user_id();

		if (is_null($user_id))
		{
			throw new Exception('User not logged in, use check_login() first');
		}

		$this->ci->load->model(array('shared/user_model', 'shared/user_group_model'));
		$user = $this->ci->user_model->get_user($user_id, TRUE);

		if (empty($user))
		{
			throw new Exception('User logged in but missing in database');
		}

		// remove password hash
		unset($user['password']);

		// get user groups
		$user['user_groups'] = $this->ci->user_group_model->get_groups_by_user($user_id, TRUE);

		if (!is_null($field))
		{
			if (isset($user[$field]))
			{
				return $user[$field];
			}
			else
			{
				return NULL;
			}
		}

		return $user;
	}

	/**
	 * Save the url the user came in on, before the log in screen.
	 *
	 * @param string $url Optional URL to save as prelogin URL
	 */
	public function save_prelogin_url($url = NULL)
	{
		$this->ci->load->helper('url');

		// Get prelogin url if not empty
		$prelogin_url = !empty($url) ? $url : full_current_url();

		// Set prelogin url
		$this->ci->session->set_userdata('login_user_url', $prelogin_url);
	}

	/**
	 * Get the saved url the user came in on, before the log in
	 * screen. The saved session variable will be destroyed.
	 */
	public function get_prelogin_url($default_url = '')
	{
		$url = $this->ci->session->userdata('login_user_url');

		$this->ci->session->unset_userdata('login_user_url');

		if (empty($url))
		{
			$url = site_url($default_url);
		}

		return $url;

	}

	/**
	 * Simple check if current user is super user
	 *
	 * @return boolean TRUE if the user is super user
	 */
	public function is_super_user()
	{
		return ($this->get_user_data('is_super_user') == 'yes') ? TRUE : FALSE;
	}

	/**
	 * Check if user has one or more of the allowed user groups given
	 *
	 * @param mixed $allowed_groups Allowed groups in array or multiple strings
	 * @return boolean TRUE if the user has one or more of the allowed user groups
	 */
	public function auth_user_groups($allowed_groups)
	{
		if(!is_array($allowed_groups))
		{
			$allowed_groups = func_get_args();
		}

		// get current logged in user groups
		$user_groups = array_keys($this->get_user_data('user_groups'));

		// get similar groups
		$intersect_groups = array_intersect($allowed_groups, $user_groups);

		return (count($intersect_groups) > 0 || $this->is_super_user());
	}
}
