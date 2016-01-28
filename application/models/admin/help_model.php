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

class Help_model extends CI_Model
{

	public function get_pages($parent_id, $preview_modus = 'on', $allowed_modules = array())
	{
		$this->db->where('parent_id', $parent_id);

		if(!is_super_user() || $preview_modus == 'on')
		{
			$this->db->where('is_visible', 'yes');
		}

		if(ENVIRONMENT !== 'development' && count($allowed_modules) > 0)
		{
			$this->db->where("module IN ('".implode("','", $allowed_modules)."')", NULL, FALSE);
		}

		$this->db->order_by('title');

		return $this->db->get('help_page')->result_array();
	}

	public function get_page($id, $preview_modus = 'on')
	{
		$this->db->where('id', $id);

		if(!is_super_user() || $preview_modus == 'on')
		{
			$this->db->where('is_visible', 'yes');
		}

		return $this->db->get('help_page')->row_array();
	}

	public function get_paragraphs($page_id, $preview_modus = 'on')
	{
		$this->db->where('page_id', $page_id);

		if(!is_super_user() || $preview_modus == 'on')
		{
			$this->db->where('is_visible', 'yes');
		}

		$this->db->order_by('order');

		return $this->db->get('help_paragraph')->result_array();
	}

	public function get_paragraph($id, $preview_modus = 'on')
	{
		$this->db->where('id', $id);

		if(!is_super_user() || $preview_modus == 'on')
		{
			$this->db->where('is_visible', 'yes');
		}

		return $this->db->get('help_paragraph')->row_array();
	}

	public function delete_paragraphs($page_id = 0)
	{
		$this->db->where('page_id', $page_id);
		$this->db->delete('help_paragraph');
	}

	public function get_page_by_controller($controller)
	{
		$this->db->where('controller', $controller);

		if(!is_super_user())
		{
			$this->db->where('is_visible', 'yes');
		}

		return $this->db->get('help_page')->row_array();
	}

	public function get_paragraph_by_key($key, $page_id)
	{
		$this->db->where('key', strtolower($key));

		if(!is_super_user())
		{
			$this->db->where('is_visible', 'yes');
		}

		return $this->db->get('help_paragraph')->row_array();
	}

}

/* End of file help_model.php */
/* Location: ./application/models/admin/help_model.php */
