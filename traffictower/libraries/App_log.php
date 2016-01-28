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
 * Library: Handles log entries into the database.
 */
class App_log
{
	/**
	 * @var Object $ci Holds the instance of the CodeIgniter methods
	 */
	protected $ci;

	/**
	 * Set CI instance.
	 */
	public function __construct()
	{
		// set the CI instance so we can access the CI-libraries
		$this->ci =& get_instance();
	}

	/**
	 * Add a log entry into the database. (DEPRECATED, use the log model)
	 *
	 * @param String $action Short key for the action to log e.g. 'login_atempt', max 30 characters
	 * @param String $message Longer explaination of event (optional)
	 * @param Integer $user_id Id of the user causing this event, if a user is logged in, their id is used default
	 */
	public function log($action, $message = NULL, $user_id = NULL)
	{
		$this->ci->load->model('shared/log_model');
		$this->ci->log_model->log($action, $message, $user_id);
	}

	/**
	 * Get log entries.
	 *
	 * @param Integer $since number of seconds to look back in the past (optional)
	 * @param String $action Short key for the action to log e.g. 'login_attempt' (optional)
	 * @param String $ip_address Ip address to get events for (optional)
	 * @param Integer $user_id Id of the user to get events for (optional)
	 * @param Boolean $number_of_records Get only the number of records (optional)
	 * @param Mixed $order_field Field to order by or NULL not to order (optional)
	 * @param String $order 'asc' | 'desc' default: asc (optional)
	 * @param Mixed $limit Max number of records (optional)
	 * @return mixed Array of log entries OR Integer number of records
	 */
	public function get_logs($since = NULL, $action = NULL, $ip_address = NULL, $user_id = NULL, $get_number_of_records = FALSE, $order_field, $order = 'asc', $limit = NULL)
	{
		$this->ci->load->model('shared/log_model');

		$params = array(
			'since' => $since,
			'action' => $action,
			'ip_address' => $ip_address,
			'get_number_of_records' => $get_number_of_records,
			'order_field' => $order_field,
			'order' => $order,
			'limit' => $limit
		);

		if (!is_null($user_id))
		{
			$params['user_id'] = $user_id;
		}

		return $this->ci->log_model->get_logs($params);
	}

	/**
	 * Get number of log entries.
	 *
	 * @param Integer $since number of seconds to look back in the past
	 * @param String $action Short key for the action to log e.g. 'login_attempt' (optional)
	 * @param String $ip_address Ip address to get events for (optional)
	 * @param Integer $user_id Id of the user to get events for (optional)
	 * @return mixed Array of log entries
	 */
	public function get_log_number($since = NULL, $action = NULL, $ip_address = NULL, $user_id = NULL)
	{
		return $this->get_logs($since, $action, $ip_address, $user_id, TRUE);
	}
}
