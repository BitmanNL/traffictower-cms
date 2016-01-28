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
 * @package   CMS\Elements
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Element video model.
 *
 * Model to get the DB info for the video element.
 */
class Element_video_model extends CI_Model
{

	/**
	 * get_video
	 *
	 * Get video from database
	 *
	 * @param integer $id Element id
	 * @return array Video settings
	 */
	public function get_video($id)
	{
		$query = $this->db->get_where('element_video', array('id' => $id));
		return $query->row_array();
	}

	/**
	 * Create Element Content
	 *
	 * Create the content row for a new element
	 *
	 * @param mixed[] $content_data Data for the new content
	 *
	 * @return Integer The id of the content row created
	 */
	public function create_element_content($content_data)
	{
		$this->db->set('type', $content_data['type']);
		$this->db->set('key', $content_data['key']);
		$this->db->set('autoplay', $content_data['autoplay']);
		$this->db->set('thumbnail', $content_data['thumbnail']);
		$this->db->set('format_type', $content_data['format_type']);
		$this->db->set('width', $content_data['width']);
		$this->db->set('height', $content_data['height']);
		$this->db->set('title', $content_data['title']);

		$this->db->insert('element_video');

		return $this->db->insert_id();
	}

	/**
	 * Update Element Content
	 *
	 * Update the content row for an element
	 *
	 * @param mixed[] $content_data Data for the new content
	 */
	public function update_element_content($content_data)
	{
		$this->db->where('id', $content_data['element_content_id']);

		$this->db->set('type', $content_data['type']);
		$this->db->set('key', $content_data['key']);
		$this->db->set('autoplay', $content_data['autoplay']);
		$this->db->set('thumbnail', $content_data['thumbnail']);
		$this->db->set('format_type', $content_data['format_type']);
		$this->db->set('width', $content_data['width']);
		$this->db->set('height', $content_data['height']);
		$this->db->set('title', $content_data['title']);

		$this->db->update('element_video');

		return $this->db->insert_id();
	}

	/**
	 * Delete Element Content
	 *
	 * Delete the content row for an element
	 *
	 * @param integer $element_content_id ID of the element content to delete
	 *
	 */
	public function delete_element_content($element_content_id)
	{
		$this->db->where('id', $element_content_id);
		$this->db->delete('element_video');
	}

}
