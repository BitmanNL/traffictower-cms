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
 * @package   CMS\Core\Models
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * For interfacing with the log-table.
 */
class Log_model extends CI_Model
{

	/**
	 * Add a log entry into the database.
	 *
	 * @param String $action Short key for the action to log e.g. 'login_atempt', max 30 characters
	 * @param String $message Longer explaination of event (optional)
	 * @param Integer $user_id Id of the user causing this event, if a user is logged in, their id is used default
	 */
	public function log($action, $message = NULL, $user_id = NULL)
	{

		$data['action'] = $action;
		$data['message'] = empty($message) ? NULL : $message;
		$data['ip_hash'] = $this->input->ip_address();

		if (!is_null($user_id))
		{
			$data['user_id'] = $user_id;
		}
		else if (class_exists('Auth') && isset($this->auth) && $this->auth->check_login())
		{
			$data['user_id'] = $this->auth->get_user_data('id');
		}
		else
		{
			$data['user_id'] = NULL;
		}

		$this->db->insert('log', $data);
	}

	/**
	 * Get log entries
	 *
	 * @param array $params Array of parameters, uses:
	 * @uses Integer $since number of seconds to look back in the past
	 * @uses String $action Short key for the action to log e.g. 'login_atempt' (optional)
	 * @uses Integer $user_id Id of the user to get events for (optional)
	 * @uses String $ip_address Ip address hash to get events for (optional)
	 * @uses Bool $get_number_of_records Get only the number of records (optional)
	 * @uses Mixed $order_field Field to order by or NULL not to order (optional)
	 * @uses String $order 'asc' | 'desc' default: asc (optional)
	 * @uses Mixed $limit Max number of records (optional)
	 *
	 * @return [mixed] Array of log entries
	 */
	public function get_logs($params = array())
	{
		if (isset($params['since']))
		{
			$since_date = !is_null($params['since']) ? date("Y-m-d H:i:s", time() - $params['since']) : NULL;

			if (!is_null($since_date))
			{
				$this->db->where('date_created >=', $since_date);
			}
		}

		if (isset($params['action']) && !is_null($params['action']))
		{
			$this->db->where('action', $params['action']);
		}

		if (isset($params['ip_address']) && !is_null($params['ip_address']))
		{
			$this->db->where('ip_hash', $params['ip_address']);
		}

		if (isset($params['user_id']))
		{
			if (!is_null($params['user_id']))
			{
				$this->db->where('user_id', $params['user_id']);
			}
			else
			{
				$this->db->where('user_id IS NULL', NULL, FALSE);
			}
		}

		if (isset($params['order_field']) && !is_null($params['order_field']))
		{
			$order = (isset($params['order']) && $params['order'] === 'desc') ? 'desc' : 'asc';
			$this->db->order_by($params['order_field'], $order);
		}

		if (isset($params['limit']) && !is_null($params['limit']))
		{
			$this->db->limit($params['limit']);
		}

		if (isset($params['get_number_of_records']) && $params['get_number_of_records'])
		{
			return $this->db->count_all_results('log');
		}
		else
		{
			$query = $this->db->get('log');
			return $query->result_array();
		}
	}

	/**
	 * Get all known log types (distinct table).
	 *
	 * @return array Log types
	 */
	public function get_log_types()
	{
		$this->load->helper('array');

		$this->db->select('DISTINCT(action)');
		$this->db->order_by('action');

		$results = $this->db->get('log')->result_array();

		return pluck($results, 'action');
	}

}
