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
 * @package   CMS\Core\Admin
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles all user authentication for admin.
 */
class TT_login extends Admin_controller
{

	/** Set for whole controller layout to login. */
	protected $layout = 'login';

	/**
	 * Log in form
	 */
	public function index($failed = FALSE)
	{
		// login form is always logged out state
		$this->auth->logout();

		$data = array();

		$data['failed'] = $failed;
		$data['login_method'] = $this->session->flashdata('login_method') === FALSE ? 'classic' : $this->session->flashdata('login_method');

		if ($failed)
		{
			$data['email'] = $this->session->flashdata('email');
			if ($this->session->flashdata('MAX_LOGIN_ATTEMPT_EXCEEDED') !== FALSE)
			{
				$data['error_message'] = 'U heeft te vaak achter elkaar geprobeerd in te loggen. Probeer het over enkele minuten opnieuw.';
			}
			elseif ($this->session->flashdata('PERSONA_INVALID_USER') !== FALSE)
			{
				$data['error_message'] = 'De Persona gebruiker waarmee u bent ingelogd heeft geen toestemming tot dit systeem!';
			}
			elseif ($this->session->flashdata('PERSONA_VERIFICATION_FAILURE') !== FALSE)
			{
				$data['error_message'] = 'Er ging iets fout bij de verificatie van uw Persona sessie. Probeer het a.u.b. opnieuw.';
			}
			else
			{
				$data['error_message'] = 'Gebruikersnaam of wachtwoord niet bekend.';
			}
		}

		// load the persona JS library
		$this->javascript_files[] = 'https://login.persona.org/include.js';

		// load our persona JS bindings
		$this->javascript[] = $this->load->view('admin/login/js/login.js', $data, TRUE);

		$this->views['content'] = $this->load->view('admin/login/login',$data,true);

		// create the layout-HTML and show it to the user
		$this->_layout();
	}

	/**
	 * Show the user the login failed
	 */
	public function failed()
	{
		$this->index(TRUE);
	}

	/**
	 * Authenticate the user and if successful set a login session
	 */
	public function authenticate()
	{
		$email = $this->input->post('email', TRUE);
		$password = $this->input->post('password');

		$location_hash = $this->_secure_location_hash($this->input->post('location_hash'));

		try
		{
			if (!$this->auth->login($email, $password))
			{
				$this->session->set_flashdata('email',$email);

				redirect(site_url('admin/login/failed' . $location_hash));
			}
			else
			{
				// LOGGED IN

				// set logged in session
				$this->auth->set_native_session('auth_logged_in', TRUE);

				redirect($this->auth->get_prelogin_url('admin') . $location_hash);
			}
		}
		catch(Exception $e)
		{
			if($e->getMessage() === 'MAX_LOGIN_ATTEMPT_EXCEEDED')
			{
				$this->session->set_flashdata('MAX_LOGIN_ATTEMPT_EXCEEDED', TRUE);

				redirect(site_url('admin/login/failed' . $location_hash));
			}
			if($e->getMessage() === 'DEFAULT_USER_NOT_PERMITTED')
			{
				$this->session->set_flashdata('DEFAULT_USER_NOT_PERMITTED', TRUE);

				redirect(site_url('admin/login/failed' . $location_hash));
			}

			// this shouldn't happen
			show_error('Login error: ' . $e->getMessage());
		}
	}

	/**
	 * Authenticate the user with persona and if successful set a login session
	 */
	public function authenticate_persona()
	{
		$location_hash = $this->_secure_location_hash($this->input->post('location_hash'));

		if ($this->input->post('assertion') === FALSE)
		{
			show_error('No assertion given! Cannot login with Persona!');
		}

		try
		{
			if(!$this->auth->persona_login($this->input->post('assertion')))
			{
				$this->session->set_flashdata('PERSONA_INVALID_USER', TRUE);
				$this->session->set_flashdata('login_method','persona');

				redirect(site_url('admin/login/failed' . $location_hash));
			}
			else
			{
				// LOGGED IN

				// set logged in session
				$this->auth->set_native_session('auth_logged_in', TRUE);
				redirect($this->auth->get_prelogin_url('admin') . $location_hash);
			}
		}
		catch(Exception $e)
		{
			if($e->getMessage() === 'PERSONA_VERIFICATION_FAILURE')
			{
				$this->session->set_flashdata('PERSONA_VERIFICATION_FAILURE', TRUE);

				redirect(site_url('admin/login/failed' . $location_hash));
			}

			// this shouldn't happen
			show_error('Persona login error: ' . $e->getMessage());
		}

		die($assertion);
	}

	/**
	 * Logout
	 *
	 * Log the user out and redirect to the login form
	 */
	public function logout()
	{
		$this->auth->logout();

		redirect(site_url('admin/login'));
	}

	/**
	 * Geef een hash terug die altijd begint met #,
	 * of een lege string in het geval van een lege hash.
	 *
	 * @param  string $location_hash
	 *
	 * @return string
	 */
	protected function _secure_location_hash($location_hash)
	{
		$location_hash = trim($location_hash, '#');
		if (empty($location_hash))
		{
			return '';
		}
		else
		{
			return '#' . $location_hash;
		}
	}

	/**
	 * Password forgotten form and submit
	 */
	public function forgot_password()
	{
		$this->load->model('shared/user_model');

		$data['failed'] = FALSE;
		if ($this->input->post('email'))
		{
			$email = $this->input->post('email', TRUE);
			$user = $this->user_model->get_user_by_email($email);

			if (!empty($user) && $user['is_active'] == 'yes')
			{
				// User known > send email
				$email_sent = $this->_send_reset_password_mail($user);

				if ($email_sent == FALSE)
				{
					$data['failed'] = TRUE;
					$data['error_message'] = 'Reset wachtwoord e-mail niet verzonden. Probeer nogmaals.';
				}
				else
				{
					redirect(site_url('admin/login/forgot_password_sent'));
				}
			}
			else
			{
				// Send email to user
				$this->_send_unknown_user_reset_password_mail($email);
				redirect(site_url('admin/login/forgot_password_sent'));
			}
		}

		$this->views['content'] = $this->load->view('admin/login/forgot_password', $data, TRUE);
		$this->_layout();
	}

	/**
	 * Forget password success.
	 */
	public function forgot_password_sent()
	{
		$this->views['content'] = $this->load->view('admin/login/forgot_password_sent', NULL, TRUE);
		$this->_layout();
	}

	/**
	 * Reset password form and submit.
	 *
	 * @param string $code Reset password code
	 */
	public function reset_password($code)
	{
		$this->load->model('shared/user_model');
		$this->load->library('form_validation');
		$this->load->library('Bcrypt', 9);

		$data['failed'] = FALSE;
		$data['code'] = $code;

		// Check code
		$cookie = $this->input->cookie('ttcms_reset_password');
		$user = $this->user_model->get_user_by_admin_reset_code($code);
		if ($cookie !== $code || empty($user))
		{
			$data['failed'] = TRUE;
			$data['error_message'] = 'Verlopen of onbekende reset wachtwoord code.';
		}
		else
		{
			$this->form_validation->set_rules('password', 'Wachtwoord', 'required|min_length[10]|matches[password_confirm]');
			if($this->form_validation->run())
			{
				// form submit success
				$this->user_model->update_user($user['id'], array(
					'password' => $this->bcrypt->hash($this->input->post('password', TRUE)),
					'admin_forgot_password_code' => NULL
				));

				redirect(site_url('admin/login/reset_password_success'));
			}
			else
			{
				// form submit error
				$data['failed'] = TRUE;
				$data['error_message'] = form_error('password');
			}
		}

		$this->views['content'] = $this->load->view('admin/login/reset_password', $data, TRUE);
		$this->_layout();
	}

	/**
	 * Reset password success.
	 */
	public function reset_password_success()
	{
		$this->views['content'] = $this->load->view('admin/login/reset_password_success', NULL, TRUE);
		$this->_layout();
	}

	/**
	 * Send password reset email to given user.
	 *
	 * @param array $user User data
	 */
	protected function _send_reset_password_mail($user)
	{
		$this->load->library(array('email', 'app_email'));

		$data['user'] = $user;
		$data['site_name'] = app_settings_get('site_name');
		$data['reset_link'] = site_url('admin/login/reset_password/' . $this->_set_reset_password_code($user));

		$this->app_email->to($user['email']);
		$this->app_email->to_name($user['screen_name']);
		$this->app_email->subject('Reset wachtwoord voor TrafficTower CMS');
		$this->app_email->message($this->load->view('admin/login/mail/reset_password', $data, TRUE));

		if($this->app_email->send())
		{
			// email sent
			$this->app_email->clear();
			return TRUE;
		}
		else
		{
			// email not sent
			return FALSE;
		}
	}

	/**
	 * Send email to given address for unknown user in CMS.
	 *
	 * @param string $email Email address
	 */
	protected function _send_unknown_user_reset_password_mail($email)
	{
		$this->load->library(array('email', 'app_email'));

		if (!empty($email))
		{
			$data['email'] = $email;
			$data['site_name'] = app_settings_get('site_name');

			$this->app_email->to($email);
			$this->app_email->subject('Onbekende gebruiker voor TrafficTower CMS');
			$this->app_email->message($this->load->view('admin/login/mail/unknown_user_reset_password', $data, TRUE));

			$this->app_email->send();
			$this->app_email->clear();
		}
	}

	/**
	 * Create and set reset password code in db and cookie.
	 *
	 * @param array $user $user
	 * @return string Reset password code
	 */
	protected function _set_reset_password_code($user)
	{
		$this->load->helper('string');
		$this->load->model('shared/user_model');

		$code = random_string('unique');

		// Set 15 min cookie
		$cookie = array(
			'expire' => 900,
			'name' => 'ttcms_reset_password',
			'value' => $code
		);
		$this->input->set_cookie($cookie);

		// Save to database
		$this->user_model->update_user($user['id'], array('admin_forgot_password_code' => $code));

		return $code;
	}

}
