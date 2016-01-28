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
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * User model.
 */
class User_group_model extends CI_Model
{

	protected $cached_user_groups = array();

	/**
	 * _Make Slug
	 *
	 * Generate the slug, check if this slug doesn't exist
	 * if so, add a number and check again
	 *
	 * @param String $title Title of the page
	 * @param Integer $count Nmerb of failed trials
	 *
	 * @return String The generated slug
	 */
	public function make_slug($title, $count = 0, $id = 0)
	{
		// generate the slug
		$slug = url_title($title, '-', true);

		// if this is not the first try, add a number behind the slug
		if ($count > 0)
		{
			$slug .= '-'.$count;
		}

		// return the slug or try again
		if (!$this->slug_exists($slug, $id))
		{
			return $slug;
		}
		else
		{
			return $this->make_slug($title, $count + 1, $id);
		}
	}

	/**
	 * Slug Exists
	 *
	 * Check if a certain slug exists
	 *
	 * @param String $slug Title of the page
	 * @param Integer $exlcude_page_id Page to exclude
	 *
	 * @return Bool True if the slug exists
	 */
	public function slug_exists($slug, $exclude_page_id = 0)
	{
		// check if the slug exists
		$query = $this->db->where('id !=', $exclude_page_id);
		$query = $this->db->get_where('user_group', array('key' => $slug), 1);

		return ($query->num_rows()) ? TRUE : FALSE;
	}

	/**
	 * Cache given user groups
	 *
	 * @param  integer $user_id User id
	 * @param  array $user_groups User groups
	 * @return array       User
	 */
	protected function _cache_user_groups($user_id, $user_groups)
	{
		if (!empty($user_groups))
		{
			$this->cached_user_groups[$user_id] = $user_groups;
		}

		return $user_groups;
	}

	/**
	 * Get user groups by user id
	 *
	 * @param  integer $user_id User id
	 * @return array          List of user groups user has
	 */
	public function get_groups_by_user($user_id, $use_cache = FALSE)
	{
		if ($use_cache && isset($this->cached_user_groups[$user_id]))
		{
			$user_groups = $this->cached_user_groups[$user_id];
		}
		else
		{
			$this->db->select('user_group.*');
			$this->db->order_by('name');
			$this->db->join('user_x_user_group', 'user_x_user_group.user_group_id = user_group.id');
			$this->db->where('user_id', $user_id);
			$results = $this->db->get('user_group')->result_array();

			$user_groups = array();
			foreach($results as $result)
			{
				$user_groups[$result['key']] = $result['name'];
			}

			$this->_cache_user_groups($user_id, $user_groups);
		}

		return $user_groups;
	}

}
