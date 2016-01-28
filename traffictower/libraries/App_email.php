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
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library: Handles all app email, extended from CI_Email.
 * Replaces some CI_Email methods and adds some new ones.
 * Also optional to use other CI_Email methods (as initialize()).
 */
class App_email extends CI_Email
{

	/**
	 * @var object $ci CodeIgniter object instance
	 */
	private $ci;

	/**
 	 * @var array $_data Set of email data (email, name, message, etc)
	 */
	private $_data = array();

	private $content = array();

	/**
	 * @var array $_config Default app email settings
	 */
	private $_config = array();

	/**
	 * Replacement of CI_Email construct method.
	 * Added: merge config variables with default app_email config variables.
	 *
	 * @param array $config Config settings (same as the CI_Email config) [ optional ]
	 */
	public function __construct($config = array())
	{
		$this->ci =& get_instance();
		$this->ci->load->language('email');

		// load default config from config folder and merge with construct input config
		$this->ci->load->config('app_email', TRUE);

		// if config is null
		$config = !is_array($config) ? array() : $config;

		$this->_config = array_merge($this->ci->config->item('app_email'), $config);

		parent::__construct($this->_config);
	}

	/**
	 * Replacement of CI_Email from method.
	 * In order to: if not added, then use the database app settings email and sitename.
	 *
	 * @param string $email From email address
	 * @param string $name From name
	 */
	public function from($email, $name = '')
	{
		$this->_data['from_email'] = $email;
		if(!empty($name))
		{
			$this->_data['from_name'] = $name;
		}
	}

	/**
 	 * Replacement of CI_Email to method.
 	 * In order to: in combination with to_name you can personalize email with to_email and to_name.
 	 *
 	 * @param mixed $to One or more email addresses to send to
	 */
	public function to($to = array())
	{
		if(!is_array($to))
		{
			$to = func_get_args();
		}

		$this->_data['to'] = $to;
	}

	/**
	 * New function to_name to set the to_name for in email template (personalizable in case of one to email address).
	 *
	 * @param string $to_name To name
	 */
	public function to_name($to_name = '')
	{
		$this->_data['to_name'] = $to_name;
	}

	/**
	 * Replacement of CI_Email subject method.
	 * In order to: Add a environment specific pre-tag to the subject.
	 *
	 * @param string $subject Email subject
	 */
	public function subject($subject)
	{
		$this->_data['subject'] = $subject;
	}

	/**
	 * Replacement of CI_Email message method.
	 * In order to: Use an email template around the given message.
	 *
	 * @param string $message Email body message
	 */
	public function message($message)
	{
		$this->_data['body_html'] = $message;
	}

	/**
	 * Replacement of set_alt_message method.
	 * In order to: Use an email template around the given text message.
	 * If not given, CI_Email will use the html body.
	 *
	 * @param string $alt_message Email body text message
	 */
	public function set_alt_message($alt_message)
	{
		$this->_data['body_text'] = $alt_message;
	}

	/**
	 * Use database template instead of own.
	 *
	 * @param  string $load_email Email load from database name
	 */
	public function load_email($load_email)
	{
		$this->ci->load->model('app_email_model');
		$email = $this->ci->app_email_model->get_template($load_email, $this->ci->config->item('language'));

		if (!empty($email))
		{
			$this->subject($email['subject']);
			$this->message($email['message']);

			if($email['from_available'] == 'yes')
			{
				$this->from($email['from_email'], $email['from_name']);
			}
			if($email['to_available'] == 'yes')
			{
				$this->to($email['to_email']);
				$this->to_name($email['to_name']);
			}

			$this->_config['email_template'] = $email['template'];
		}
	}

	public function params(array $content = array())
	{
		$this->content = array_merge($this->content, $content);
	}

	/**
	 * New function to print the email instead of actual send (debug).
	 * Will die after echo-ing the mail.
	 */
	public function preview()
	{
		if($this->_prepare_send())
		{
			echo $this->_data['message'];

			// debug info
			$output_data = $this->_data;
			unset($output_data['body_html']);
			unset($output_data['message']);
			unset($output_data['alt_message']);

			echo "<pre>";
			print_r($output_data);
			echo "</pre>";
		}
		else
		{
			echo $this->print_debugger();
		}
		die();
	}

	/**
	 * New function to prepare (convert) and check data for sending.
	 *
	 * @return boolean Whether all data is valid or not
	 */
	private function _prepare_send()
	{
		$valid = TRUE;

		if(empty($this->_data['body_html']))
		{
			$this->_set_error_message('Message is missing.');
			$valid = FALSE;
		}

		if(empty($this->_data['subject']))
		{
			$this->_set_error_message('Subject is missing.');
			$valid = FALSE;
		}

		if($valid)
		{
			$this->ci->load->model('shared/app_settings_model');
			$app_settings = $this->ci->app_settings_model->get_app_settings();

			// set from
			if(empty($this->_data['from_email']))
			{
				// retrieve from database if not present
				$this->_data['from_email'] = $app_settings['email'];
				$this->_data['from_name'] = $app_settings['site_name'];
			}

			// set to
			if(empty($this->_data['to']))
			{
				// retrieve from database if not present
				$this->_data['to'] = array($app_settings['email']);
				$this->_data['to_name'] = $app_settings['site_name'];
			}

			// set to_email (for view)
			$this->_data['to_email'] = '';
			if(count($this->_data['to']) == 1)
			{
				// persoonlijke mail want maar 1 adres
				$this->_data['to_email'] = current($this->_data['to']);
				if (empty($this->_data['to_name']))
				{
					$this->_data['to_name'] = current($this->_data['to']);
				}
			}

			// set subject
			if ($this->_config['subject_prefix_site_name'])
			{
				$this->_data['subject'] = $app_settings['site_name'] . ': ' . $this->_data['subject'];
			}

			if(ENVIRONMENT != 'production')
			{
				$this->_data['subject'] = '['.ENVIRONMENT.'] ' . $this->_data['subject'];
			}

			// insert custom content
			$prefixed_content = array(
				'__FROM_NAME__' => $this->_data['from_name'],
				'__FROM_EMAIL__' => $this->_data['from_email'],
				'__TO_NAME__' => $this->_data['to_name'],
				'__TO_EMAIL__' => $this->_data['to_email']
			);
			$this->content = array_merge($prefixed_content, $this->content);
			foreach ($this->content as $key => $value)
			{
				$this->_data['body_html'] = str_replace('/'.$key, $value, $this->_data['body_html']);
				$this->_data['body_html'] = str_replace($key, $value, $this->_data['body_html']);
				if (isset($this->_data['body_text']))
				{
					$this->_data['body_text'] = str_replace('/'.$key, $value, $this->_data['body_text']);
					$this->_data['body_text'] = str_replace($key, $value, $this->_data['body_text']);
				}
			}

			// set message
			$this->_data['message'] = $this->ci->load->view('app_email/templates/' . $this->_config['email_template'] . '_html', $this->_data, TRUE);
			$this->_data['alt_message'] = '';
			if(!empty($this->_data['body_text']))
			{
				$this->_data['alt_message'] = $this->ci->load->view('app_email/templates/' . $this->_config['email_template'] . '_text', $this->_data, TRUE);
			}
		}

		return $valid;
	}

	/**
	 * Replacement of CI_Email send function
	 * Added: App email data added to CI_Email (from, to, etc) before actual send.
	 *
	 * @return boolean Whether or not the email is sent
	 */
	public function send()
	{
		$valid = $this->_prepare_send();

		if($valid)
		{
			// set CI_Email
			parent::from($this->_data['from_email'], $this->_data['from_name']);
			parent::to($this->_data['to']);
			parent::subject($this->_data['subject']);
			parent::message($this->_data['message']);

			if(!empty($this->_data['alt_message']))
			{
				parent::set_alt_message($this->_data['alt_message']);
			}

			$send = parent::send();

			if($send)
			{
				// log email
				$this->ci->load->library('app_log');
				$this->ci->app_log->log('app_email', $this->_data['subject'].' (to: '.$this->_data['to'][0].')');
			}

			return $send;
		}
		else
		{
			// mail not send, see error for details
			return FALSE;
		}
	}

	/**
	 * Extends basic CI_Email clear function with app_email clear data.
	 */
	public function clear()
	{
		parent::clear();

		// empty app_email specifics
		$this->_data = array();
	}

}
