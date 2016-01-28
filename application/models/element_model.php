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
 * Get elements model.
 *
 * Model to get general element information.
 */
class Element_model extends CI_Model
{
	/**
	 * Get elements assigned to a certain page
	 *
	 * @param Integer $page_id ID for the page
	 * @param string $preview Preview modus is on ('true')
	 *
	 * @return mixed[] list of elements
	 */
	public function get_elements_by_page_id($page_id, $preview = FALSE)
	{
		$this->db->select('element.*, element_x_page.is_visible');
		$this->db->join('element_x_page', 'element_x_page.element_id = element.id');
		$this->db->where('page_id', $page_id);

		if ($preview !== TRUE)
		{
			$this->db->where('element_x_page.is_visible', 'yes');
		}

		$this->db->order_by('element_x_page.order');

		$query = $this->db->get('element');

		return $query->result_array();
	}
}
