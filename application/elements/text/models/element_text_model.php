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
 * element Text Model
 *
 * Model to get the DB info for the text element
 */
class element_text_model extends CI_Model
{
	/**
	 * Get Text
	 *
	 * Get the html for the given element from the DB
	 *
	 * @param integer $element_content_id ID of the element content to get the DB-info for
	 *
	 * @return mixed[] The row of the element content requested
	 */
	public function get_text($element_content_id)
	{
		$this->db->order_by('element_text.date_modified', 'desc');

		$this->db->join('user', 'user.id = element_text.user_id', 'left');
		$this->db->select('element_text.*');
		$this->db->select("CONCAT(user.screen_name, ' (', user.email, ')') AS user", FALSE);

		$parameters = array(
				'element_text.id' => $element_content_id,
				'element_text.status' => 'published'
			);

		$query = $this->db->get_where('element_text', $parameters);

		$result = $query->row_array();

		if (!empty($result))
		{
			$result['user'] = $this->_set_username($result['user']);
		}

		return $result;
	}

	public function get_text_concept($element_content_id)
	{
		$this->db->order_by('date_modified', 'desc');

		$parameters = array(
				'id' => $element_content_id,
				'status' => 'concept'
			);

		$query = $this->db->get_where('element_text', $parameters);

		return $query->row_array();
	}

	public function get_text_revision($element_content_id, $revision_id)
	{
		$this->db->join('user', 'user.id = element_text.user_id', 'left');
		$this->db->select('element_text.*');
		$this->db->select("CONCAT(user.screen_name, ' (', user.email, ')') AS user", FALSE);


		$parameters = array(
				'element_text.id' => $element_content_id,
				'element_text.status' => 'revision',
				'element_text.revision_id' => $revision_id
			);

		$query = $this->db->get_where('element_text', $parameters);

		$result = $query->row_array();

		if (!empty($result))
		{
			$result['user'] = $this->_set_username($result['user']);
		}

		return $result;
	}

	public function get_revisions($element_content_id)
	{
		$this->db->order_by('element_text.revision_id', 'desc');

		$this->db->join('user', 'user.id = element_text.user_id', 'left');
		$this->db->select('element_text.*');
		$this->db->select("CONCAT(user.screen_name, ' (', user.email, ')') AS user", FALSE);


		$parameters = array(
			'element_text.id' => $element_content_id
		);

		$query = $this->db->get_where('element_text', $parameters);

		$results = $query->result_array();

		foreach ($results as $key => $result)
		{
			$results[$key]['user'] = $this->_set_username($result['user']);
		}

		return $results;
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
		$set = array(
			'title' => $content_data['element_title'],
			'content'=> $content_data['element_content'],
			'date_created' => date('Y-m-d H:i:s'),
			'status' => 'concept',
			'user_id' => $content_data['user_id']
		);

		if (isset($content_data['element_text_type']))
		{
			$set['type'] = $content_data['element_text_type'];
		}

		// indien nieuw concept van een published rev
		if(isset($content_data['element_content_id']))
		{
			$set['id'] = $content_data['element_content_id'];

			// oude concepts eruit
			$this->delete_element_content($content_data['element_content_id'], TRUE);
		}

		$this->db->set($set);
		$this->db->insert('element_text');
		$id = $this->db->insert_id();

		// update id
		if(!isset($content_data['element_content_id']))
		{
			$this->db->where('revision_id', $id);
			$this->db->update('element_text', array('id' => $id));
		}

		return $id;
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
		$this->db->where('revision_id', $content_data['element_revision_id']);
		$this->db->set('title', $content_data['element_title']);
		$this->db->set('content', $content_data['element_content']);

		if (isset($content_data['element_text_type']))
		{
			$this->db->set('type', $content_data['element_text_type']);
		}

		$this->db->update('element_text');

		return $this->db->insert_id();
	}
	/**
	 * Delete Element Content
	 *
	 * Delete the content row for an element
	 *
	 * @param integer $element_content_id ID of the element content to delete
	 * @param boolean $concept_ony Whether or not delete only concept versions
	 *
	 */
	public function delete_element_content($element_content_id, $concept_only = FALSE)
	{
		$this->db->where('id', $element_content_id);

		if($concept_only)
		{
			$this->db->where('status', 'concept');
		}

		$this->db->delete('element_text');
	}

	public function delete_element_revision($revision_id)
	{
		$this->db->where('revision_id', $revision_id);
		$this->db->delete('element_text');
	}

	public function publish_element_content($element_content_id)
	{
		$this->db->where('revision_id', $element_content_id);
		$this->db->update('element_text', array('status' => 'published'));

		// get current published item
		$this->db->where('revision_id', $element_content_id);
		$item = $this->db->get('element_text')->row_array();

		if(!empty($item))
		{
			// update all old published to revision
			$this->db->where('revision_id <>', $element_content_id);
			$this->db->where('id', $item['id']);
			$this->db->update('element_text', array('status' => 'revision'));
		}
	}

	protected function _set_username($user)
	{
		if (is_null($user))
		{
			$user = 'verwijderde gebruiker';
		}

		return $user;
	}
}
