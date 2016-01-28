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
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User model.
 */
class User_model extends CI_Model
{

	protected $cached_users = array();

	/**
	 * Get one user row
	 *
	 * @param Integer $user_id ID for the user
	 * @param boolean $use_cache Use cached user
	 * @return array row of the user
	 */
	public function get_user($user_id, $use_cache = FALSE)
	{
		if ($use_cache && isset($this->cached_users[$user_id]))
		{
			$user = $this->cached_users[$user_id];
		}
		else
		{
			$user = $this->db->get_where('user', array('id' => $user_id))->row_array();
			$this->_cache_user($user);
		}

		return $user;
	}

	/**
	 * Cache given user
	 *
	 * @param  array $user User
	 * @return array       User
	 */
	protected function _cache_user($user)
	{
		if (!empty($user) && isset($user['id']))
		{
			$this->cached_users[$user['id']] = $user;
		}

		return $user;
	}

	/**
	 * Get one user row by its email address
	 *
	 * @param string $email
	 * @return array row of the user
	 */
	public function get_user_by_email($email)
	{
		$user = $this->db->get_where('user', array('email' => $email))->row_array();
		return $this->_cache_user($user);
	}

	/**
	 * Create a user, used by the Auth library
	 *
	 * @param String $email The users email address
	 * @param String $screen_name The users screen name
	 * @param String $is_super_user User is super user, can do everything
	 * @param String $is_active User is active
	 * @return integer ID of inserted user
	 */
	public function create_user($email, $screen_name, $is_super_user, $is_active)
	{
		$this->db->set('email', $email);
		$this->db->set('screen_name', $screen_name);
		$this->db->set('is_super_user', $is_super_user);
		$this->db->set('is_active', $is_active);

		$this->db->insert('user');

		return $this->db->insert_id();
	}

	/**
	 * Check whether the email is unique
	 *
	 * @param  string  $email   Email address
	 * @param  integer  $user_id User id to exclude
	 * @return boolean  Email address is unique
	 */
	public function is_email_unique($email, $user_id = NULL)
	{
		if(!is_null($user_id))
		{
			$this->db->where(array('id !=' => $user_id));
		}
		$this->db->where(array('email' => $email));
		$this->db->from('user');

		return !($this->db->count_all_results() > 0);
	}

	/**
	 * Check if a SSO user exists
	 *
	 * @param String $sso_id SSO providers' id for the user
	 * @return boolean SSO user exists
	 */
	public function sso_user_exists($sso_id)
	{
		$this->db->where('sso_id', $sso_id);
		$this->db->from('user');

		return $this->db->count_all_results() > 0;
	}

	/**
	 * Get a sso user with the providers' user id
	 *
	 * @param String $sso_id SSO providers' id for the user
	 * @return array User row
	 */
	public function get_sso_user($sso_id)
	{
	 	$this->db->where('sso_id', $sso_id);
		$user = $this->db->get('user')->row_array();

		return $this->_cache_user($user);
	}

	/**
	 * Create a user based on the data from an SSO authentication
	 *
	 * @param String $provider Name of the sso-provider
	 * @param String $sso_id Providers' ID for the user
	 * @param String $email The users email address
	 * @param String $screen_name The users screen name
	 * @return integer ID of inserted user
	 */
	public function create_sso_user($provider, $sso_id, $email, $screen_name)
	{
		$this->db->set('sso_provider', $provider);
		$this->db->set('sso_id', $sso_id);
		$this->db->set('email', $email);
		$this->db->set('screen_name', $screen_name);
		$this->db->set('is_active', 'yes');

		$this->db->insert('user');

		return $this->db->insert_id();
	}

	/**
	 * Update a user based on the data from an SSO authentication
	 *
	 * @param String $sso_id Providers' ID for the user
	 * @param String $email The users email address
	 * @param String $screen_name The users screen name
	 */
	public function update_sso_user($sso_id, $email, $screen_name)
	{
		$this->db->set('email', $email);
		$this->db->set('screen_name', $screen_name);
		$this->db->set('is_active', 'yes');

		$this->db->where('sso_id', $sso_id);
		$this->db->update('user');
	}

	/**
	 * Update user.
	 *
	 * @param integer $user_id Id of user to update
	 * @param array $data User data
	 */
	public function update_user($user_id, $data)
	{
		$this->db->where('id', $user_id);
		$this->db->update('user', $data);
	}

	/**
	 * Get user by reset password code.
	 *
	 * @param string $code Reset password code
	 * @return array User
	 */
	public function get_user_by_admin_reset_code($code)
	{
		$this->db->where('admin_forgot_password_code', $code);
		$this->db->where('is_active', 'yes');
		return $this->db->get('user')->row_array();
	}

}
