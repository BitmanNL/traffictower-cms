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

class Element_model extends CI_Model
{

	/**
	 * Get Elements By Page Id
	 *
	 * Get elements assigned to a certain page
	 *
	 * @param Integer $page_id ID for the page
	 *
	 * @return mixed[] list of elements
	 */
	public function get_elements_by_page_id($page_id)
	{
		$this->db->select('element.*, element_x_page.is_visible, element_x_page.page_id, element_x_page.order');
		$this->db->join('element_x_page', 'element_x_page.element_id = element.id');
		$this->db->where('page_id', $page_id);

		$this->db->order_by('element_x_page.order');

		$elements = $this->db->get('element')->result_array();

		foreach ($elements as $key => $element)
		{
			$this->db->where('element_id', $element['id']);
			$elements[$key]['is_global'] = ($this->db->count_all_results('element_x_page') > 1);
		}

		return $elements;
	}

	/**
	 * Update Element Order
	 *
	 * Update the order and position of the elements on a page
	 *
	 * @param mixed[] $element_order Array with per content-position the order of the elements
	 */
	public function update_element_order($element_order, $page_id)
	{
		foreach ($element_order as $position => $elements)
		{
			foreach ( (array) $elements as $order => $element_id)
			{
				$this->db->where('id', $element_id);
				$this->db->set('position', $position);
				$this->db->update('element');

				// update order
				$this->db->where('element_id', $element_id);
				$this->db->where('page_id', $page_id);
				$this->db->set('order', $order);
				$this->db->update('element_x_page');
			}

		}

		return true;
	}

	/**
	 * Create Element
	 *
	 * Create a new element in the element DB
	 *
	 * @param Integer $page_id Id of the page to put the element on
	 * @param String $position Position on the page to put the element in
	 * @param String $type Type of the element
	 * @param Integer $element_content_id Id of the content part of the element
	 */
	public function create_element($page_id, $position, $type, $element_content_id)
	{
		$this->load->model('admin/page_model');
		$page = $this->page_model->get_page_by_id($page_id);

		// get the order this element will have in it's position
		$this->db->select('element.*, element_x_page.order');
		$this->db->join('element_x_page', 'element_x_page.element_id = element.id');
		$this->db->where('element_x_page.page_id', $page_id);

		$this->db->where('position', $position);

		$this->db->order_by('element_x_page.order', 'desc');
		$this->db->limit(1);
		$query = $this->db->get('element');

		if ($query->num_rows())
		{
			$row = $query->row_array();
			$order = $row['order'] + 1;
		}
		else
		{
			$order = 0;
		}

		// insert this element in the page
		$this->db->set('position', $position);
		$this->db->set('content_id', $element_content_id);
		$this->db->set('type', $type);
		$this->db->insert('element');

		$element_id = $this->db->insert_id();

		// set order
		$this->db->set('order', $order);
		$this->db->set('element_id', $element_id);
		$this->db->set('page_id', $page_id);
		$this->db->set('is_visible', 'no');
		$this->db->insert('element_x_page');

		return $element_id;
	}

	public function add_global_elements_to_page($page_id, $layout)
	{
		// Get order start
		$existing_page_elements = $this->get_elements_by_page_id($page_id);
		if (!empty($existing_page_elements))
		{
			$last_element = end($existing_page_elements);
			$order = intval($last_element['order']) + 1;
		}
		else
		{
			$order = 1;
		}

		// Add elements
		$global_elements = $this->get_global_elements_for_layout($layout);
		foreach ($global_elements as $element)
		{
			$this->db->set('element_id', $element['element_id']);
			$this->db->set('page_id', $page_id);
			$this->db->set('order', $order);
			$this->db->insert('element_x_page');

			$order++;
		}
	}

	public function delete_elements_for_page($page_id)
	{
		$this->db->where('page_id', $page_id);
		$this->db->delete('element_x_page');
	}

	public function delete_global_elements_for_page($page_id, $layout)
	{
		$this->load->helper('array');

		$global_elements = $this->get_global_elements_for_layout($layout);
		if (!empty($global_elements))
		{
			$global_element_ids = pluck($global_elements, 'element_id');

			$this->db->where_in('element_id', $global_element_ids);
			$this->db->where('page_id', $page_id);
			$this->db->delete('element_x_page');
		}
	}

	public function get_global_elements_for_layout($layout)
	{
		$this->db->select('element_x_page.element_id');
		$this->db->join('page', 'element_x_page.page_id = page.id');
		$this->db->where('page.layout', $layout);
		$this->db->group_by('element_x_page.element_id');
		$this->db->having('COUNT(*) > 1', NULL, FALSE);

		return $this->db->get('element_x_page')->result_array();
	}

	/**
	 * Get Element
	 *
	 * Get the element with an id
	 *
	 * @param Integer $element_id ID for the element
	 *
	 * @return mixed[] The db info of the element
	 */
	public function get_element($element_id)
	{
		$query = $this->db->get_where('element', array('id' => $element_id));

		$result = $query->row_array();

		return $result;
	}

	public function get_element_for_page($element_id, $page_id)
	{
		$this->db->select('element.*, element_x_page.is_visible');
		$this->db->where('element.id', $element_id);
		$this->db->where('element_x_page.page_id', $page_id);
		$this->db->join('element_x_page', 'element.id = element_x_page.element_id');

		return $this->db->get('element')->row_array();
	}

	/**
	 * Delete Element
	 *
	 * Delete an element
	 *
	 * @param Integer $element_id ID for the element
	 */
	public function delete_element($element_id)
	{
		$this->db->where(array('id' => $element_id));
		$this->db->delete('element');

		// delete order
		$this->db->where(array('element_id' => $element_id));
		$this->db->delete('element_x_page');
	}

	/**
	 * Show Element
	 *
	 * Set an element to visible
	 *
	 * @param Integer $element_id ID for the element
	 */
	public function show_element($element_id, $page_id)
	{
		$this->db->where('element_id', $element_id);
		$this->db->where('page_id', $page_id);
		$this->db->set('is_visible', 'yes');
		$this->db->update('element_x_page');
	}

	/**
	 * Hide Element
	 *
	 * Set an element to invisible
	 *
	 * @param Integer $element_id ID for the element
	 */
	public function hide_element($element_id, $page_id)
	{
		$this->db->where('element_id', $element_id);
		$this->db->where('page_id', $page_id);
		$this->db->set('is_visible', 'no');
		$this->db->update('element_x_page');
	}

	public function make_element_global($element_id, $page_id)
	{
		$this->load->model('admin/page_model');
		$page = $this->page_model->get_page_by_id($page_id);

		$element = $this->get_element_for_page($element_id, $page_id);

		// get all pages with layout
		$this->db->select('id');
		$this->db->where('layout', $page['layout']);
		$this->db->where('language', $this->config->item('language'));
		$pages = $this->db->get('page')->result_array();

		// insert
		$elements_created_count = 0;
		foreach ($pages as $item)
		{
			if ($item['id'] != $page_id)
			{
				$this->db->where('element_id', $element_id);
				$this->db->where('page_id', $item['id']);
				if ($this->db->count_all_results('element_x_page') < 1)
				{
					$this->db->set('element_id', $element_id);
					$this->db->set('page_id', $item['id']);
					$this->db->set('is_visible', $element['is_visible']);
					$this->db->insert('element_x_page');
					$elements_created_count++;
				}
			}
		}

		return $elements_created_count;
	}

	public function make_element_local($element_id, $page_id)
	{
		$this->db->where('element_id', $element_id);
		$this->db->where('page_id <>', $page_id);
		$this->db->delete('element_x_page');
	}

}
