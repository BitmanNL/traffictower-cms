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
 * @package   CMS\Core\Helpers
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * check_login
 * 
 * Check if the user is logged in and retreive user info
 *
 * @access public
 * @return boolean TRUE if the user is logged in
 */
if(!function_exists('check_login'))
{
	function check_login()
	{
		$ci =& get_instance();

		return $ci->auth->check_login();
	}
}

/**
 * is_super_user
 * Simple check if current user is super user
 *
 * @access public
 * @return boolean TRUE if the user is super user
 */
if(!function_exists('is_super_user'))
{
	function is_super_user()
	{
		$ci =& get_instance();
		
		return $ci->auth->is_super_user();
	}
}

/**
 * auth_user_groups
 *
 * Check if user has one or more of the allowed user groups given
 *
 * @access public
 * @param mixed $allowed_groups Allowed groups in array or multiple strings
 * @return boolean TRUE if the user has one or more of the allowed user groups
 */
if(!function_exists('auth_user_groups'))
{
	function auth_user_groups($allowed_groups = array())
	{
		$ci =& get_instance();

		if(!is_array($allowed_groups))
		{
			$allowed_groups = func_get_args();
		}

		return $ci->auth->auth_user_groups($allowed_groups);
	}
}

/**
 * get_user_data
 * 
 * Get data of the logged in user. Accepts an item name or when empty
 *
 * @access public
 * @param string $item Name of the item to retreive or empty for all
 * @return mixed[] Requested item or array of all items
 */
if(!function_exists('get_user_data'))
{
	function get_user_data($item = NULL)
	{
		$ci =& get_instance();

		return $ci->auth->get_user_data($item);
	}
}
