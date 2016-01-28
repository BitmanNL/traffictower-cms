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

class Page_model extends CI_Model
{

	/**
	 * Get Page Tree
	 *
	 * Get the pages in tree formation
	 *
	 * @param String $language language to create the tree for
	 *
	 * @return mixed[] list of pages with for each page their children pages
	 */
	public function get_page_tree($language, $secondary_navigation = NULL)
	{
		$this->db->select('id, parent_id, is_visible, in_menu, order, title, slug, controller, layout, language, relative_page_id, secondary_navigation, is_system_page');
		$this->db->order_by('parent_id');
		$this->db->order_by('order');
		$this->db->where('language', $language);
		if (is_null($secondary_navigation))
		{
			$this->db->where('secondary_navigation IS NULL', NULL, FALSE);
		}
		else
		{
			$this->db->where('secondary_navigation', $secondary_navigation);
		}
		$query = $this->db->get('page');

		$query_results = $query->result_array();

		// create a dictionary with page-id -> page
		$pages = array();
		if(is_array($query_results))
		{
			foreach($query_results as $page)
			{
				$pages[$page['parent_id']][] = $page;
			}
		}

		//create tree
		$tree = $this->_get_tree_items_by_parent_id($pages, 0);

		return $tree;
	}

	/**
	 * _Get Tree Items By Parent Id
	 *
	 * Get the children pages for a certain parent id
	 *
	 * @param Integer $parent_id id for the parent page
	 *
	 * @return mixed[] list of pages
	 */
	private function _get_tree_items_by_parent_id($pages, $parent_id)
	{
		$tree = array();
		if(isset($pages[$parent_id]) && is_array($pages[$parent_id]))
		{
			foreach($pages[$parent_id] as $page)
			{
				$page['children'] = $this->_get_tree_items_by_parent_id($pages, $page['id']);

				$tree[] = $page;
			}
		}
		return $tree;
	}


	/**
	 * Get Page By Id
	 *
	 * Get page information by it's id
	 *
	 * @param integer $id Identifier of the page
	 *
	 * @return object The row-data of the page
	 */
	public function get_page_by_id($id = 0)
	{
		$query = $this->db->get_where('page', array('id' => $id));
		return $query->row_array();
	}


	/**
	 * Create Page
	 *
	 * Create a page under the given parent_id
	 *
	 * @param String $title The title for the new page
	 * @param Integer $parent_id Id of the parent for this page
	 * @param String $language Language of the page
	 * @param String $secondary_navigation Name of the secondary navigation this page will belong to (optional)
	 *
	 * @return Integer Id of the created page
	 */
	public function create_page($title, $parent_id = 0, $language, $secondary_navigation = NULL)
	{
		$this->load->model('admin/element_model');

		// get the order number of the page we're going the create
		$this->db->order_by('order', 'desc');
		$query = $this->db->get_where('page', array('parent_id' => $parent_id), 1);

		if ($query->num_rows()){
			$result = $query->row_array();
			$order = intval($result['order']) + 1;
		}else{
			$order = 0;
		}

		// generate the slug
		$slug = $this->_make_slug($title, $language);

		// create the page
		$this->db->set('order', $order);
		$this->db->set('parent_id', $parent_id);

		if ($this->auth->check_login())
		{
			$this->db->set('user_id', $this->auth->get_user_data('id'));
		}

		$this->db->set('title', $title);
		$this->db->set('slug', $slug);
		$this->db->set('date_created', date('Y-m-d H:i:s'));
		$this->db->set('is_visible', 'no');
		$this->db->set('in_menu', 'yes');
		$this->db->set('language', $language);
		$this->db->set('secondary_navigation', $secondary_navigation);
		$this->db->insert('page');

		$page_id = $this->db->insert_id();

		$this->element_model->add_global_elements_to_page($page_id, 'default');

		return $page_id;
	}

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
	private function _make_slug($title, $language, $count = 0)
	{
		// generate the slug
		$slug = url_title($title, '-', true);

		// if this is not the first try, add a number behind the slug
		if ($count > 0)
		{
			$slug .= '-'.$count;
		}

		// return the slug or try again
		if (!$this->slug_exists($slug, $language))
		{
			return $slug;
		}
		else
		{
			return $this->_make_slug($title, $language, $count + 1);
		}
	}

	/**
	 * Slug Exists
	 *
	 * Check if a certain slug exists
	 *
	 * @param String $slug Title of the page
	 * @param Integer $exclude_page_id Page to exclude
	 *
	 * @return Bool True if the slug exists
	 */
	public function slug_exists($slug, $language, $exclude_page_id = 0)
	{
		// check if the slug exists
		$this->db->where('id !=', $exclude_page_id);
		$this->db->where('language', $language);
		$query = $this->db->get_where('page', array('slug' => $slug), 1);

		if ($query->num_rows())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Delete Page
	 *
	 * Delete a page
	 *
	 * @param Integer $page_id Id of the page to delete
	 *
	 * @return Bool True if the page was successfully created
	 */
	public function delete_page($page_id)
	{
		$this->load->model('admin/element_model');

		// Check if this page has subpages
		$this->db->from('page');
		$this->db->where('parent_id', $page_id);

		if ($this->db->count_all_results()){
			throw new Exception('Deze pagina heeft subpagina\'s en kan dus niet verwijderd worden.');
		}

		$page = $this->get_page_by_id($page_id);

		if ($page['is_system_page'] === 'yes')
		{
			throw new Exception('Dit is een systeem pagina en kan dus niet verwijderd worden.');
		}

		$this->db->delete('page', array('id'=>$page_id));

		// Delete all elements
		$this->element_model->delete_elements_for_page($page['id']);

		return true;
	}

	/**
	 * Update Page
	 *
	 * Update a page
	 *
	 * @param Integer $page_id Id of the page to update
	 * @param mixed $page Data to update
	 */
	public function update_page($page_id, $page)
	{
		$this->load->model('admin/element_model');

		$old_page_data = $this->get_page_by_id($page_id);

		// Update global elements
		if (isset($page['layout']) && $old_page_data['layout'] !== $page['layout'])
		{
			$this->element_model->delete_global_elements_for_page($page_id, $old_page_data['layout']);
			$this->element_model->add_global_elements_to_page($page_id, $page['layout']);
		}

		// Update page
		$this->db->where('id', $page_id);
		$this->db->update('page', $page);
	}

	/**
	 * Move Page
	 *
	 * Move a page
	 *
	 * @param Integer $page_id The page that will be moved
	 * @param Integer $target_id The page to reference the move to
	 * @param String $hit_mode The type of reference (over (in), before, after)
	 * @param String $secondary_navigation name of the secondary menu or NULL
	 *
	 * @return Bool True if the page was successfully moved
	 */
	public function move_page($page_id, $target_id, $hit_mode, $secondary_navigation)
	{
		if ($hit_mode == 'over')
		{
			// get the order number of the page to drop behind
			$this->db->order_by('order', 'desc');
			if (is_null($secondary_navigation))
			{
				$this->db->where('secondary_navigation IS NULL', NULL, FALSE);
			}
			else
			{
				$this->db->where('secondary_navigation', $secondary_navigation);
			}
			$query = $this->db->get_where('page', array('parent_id' => $target_id), 1);

			if ($query->num_rows()){
				$result = $query->row_array();
				$order = intval($result['order']) + 1;
			}else{
				$order = 0;
			}

			// move the page
			$this->db->where('id', $page_id);
			$this->db->update('page', array('parent_id'=>$target_id, 'order'=>$order, 'secondary_navigation'=>$secondary_navigation));
		}

		if ($hit_mode == 'before' OR $hit_mode == 'after')
		{
			// get the target page
			$query = $this->db->get_where('page', array('id'=>$target_id));
			$target = $query->row_array();

			// Move all the pages with the parent id of the target by one
			$sql = "UPDATE page
					SET `order` = `order` + 1
					WHERE `parent_id` = {$target['parent_id']}
					AND `secondary_navigation` " . (is_null($secondary_navigation) ? 'IS NULL' : '= ' . $this->db->escape($secondary_navigation));

			// depending on the hit_mode the target page itself has to be moved aswell
			if ($hit_mode == 'before')
			{
				$sql .= " AND `order` >= {$target['order']};";
				$order = $target['order'];
			}
			elseif ($hit_mode == 'after')
			{
				$sql .= " AND `order` > {$target['order']};";
				$order = $target['order'] + 1;
			}

			$this->db->query($sql);

			// move the page
			$this->db->where('id', $page_id);
			$this->db->update('page', array('parent_id'=>$target['parent_id'], 'order'=>$order));
		}

		// Update secondary navigation, based on target page. Moved page and all subpages update.
		$this->_update_pages_secondary_navigation($page_id, $target_id, $secondary_navigation);

		return true;
	}

	/**
	 * Update secondary navigation, based on target page. Moved page and all subpages update.
	 *
	 * @param Integer $page_id The page that will be moved
	 * @param Integer $target_id The page to reference the move to
	 * @param String $secondary_navigation name of the secondary menu or NULL
	 */
	protected function _update_pages_secondary_navigation($page_id, $target_id, $secondary_navigation)
	{
		// get target secondary navigation
		$target_page = $this->get_page_by_id($target_id);
		if (!empty($target_page))
		{
			$secondary_navigation = $target_page['secondary_navigation'];
		}

		$this->update_page_secondary_navigation($page_id, $secondary_navigation);
	}

	/**
	 * Update secondary navigation of page and all its children.
	 *
	 * @param Integer $page_id The page that will be updated
	 * @param String $secondary_navigation name of the secondary menu or NULL
	 */
	public function update_page_secondary_navigation($page_id, $secondary_navigation)
	{
		$this->db->where('id', $page_id);
		$this->db->update('page', array('secondary_navigation' => $secondary_navigation));

		// children
		$this->db->where('parent_id', $page_id);
		$children = $this->db->get('page')->result_array();
		foreach ($children as $child)
		{
			$this->update_page_secondary_navigation($child['id'], $secondary_navigation);
		}
	}

	/**
	 * Show In Navigation
	 *
	 * Show or hide a page in the navigation
	 *
	 * @param Integer $page_id Id of the page to show or hide in the navigation
	 * @param Bool $show Show the page if true or hide if false
	 */
	public function show_in_navigation($page_id, $show)
	{
		$in_menu = ($show) ? 'yes' : 'no';

		$this->db->where('id',$page_id);
		$this->db->update('page', array('in_menu'=>$in_menu));

		return true;
	}

	/**
	 * Make visible
	 *
	 * Make a page (in)accessible
	 *
	 * @param Integer $page_id Id of the page to show or hide
	 * @param Bool $show Show the page if true or hide if false
	 */
	public function make_visible($page_id, $show)
	{
		$page = $this->get_page_by_id($page_id);

		if (!$show && !is_super_user() && $page['is_system_page'] === 'yes')
		{
			throw new Exception('Dit is een systeem pagina en kan dus niet verborgen worden.');
		}

		$is_visible = ($show) ? 'yes' : 'no';

		$this->db->where('id',$page_id);
		$this->db->update('page', array('is_visible'=>$is_visible));

		return true;
	}

	/**
	 * Get the top page (with parent_id=0) in the ancestry of this page.
	 *
	 * @param  integer $page_id
	 *
	 * @return mixed   page-record
	 */
	public function get_ancestor($page_id)
	{
		$page = $this->get_page_by_id($page_id);

		return ($page['parent_id'] == 0 ? $page : $this->get_ancestor($page['parent_id']));
	}

	/**
	 * Get page tree in one single depth array, flattened with arrows
	 *
	 * @param  string $language Language
	 * @param  array $secondary_navigations List of secondary navigations
	 * @return array Navigation tree
	 */
	public function get_page_tree_flattened($language, $secondary_navigations = array())
	{
		$pages = array();

		// secondary menus
		foreach ($secondary_navigations as $menu => $name)
    	{
    		$pages[] = array(
    			'id' => NULL,
    			'title' => $name,
    			'children' => $this->get_page_tree($language, $menu)
    		);
    	}

		// general pages
		$pages = array_merge($pages, $this->get_page_tree($language));

		return $this->_map_page_tree($pages);
	}

	/**
	 * Map page tree to single depth array
	 *
	 * @param  array $pages Pages to map
	 * @param  array $mapped_pages Mapped pages array
	 * @param  integer $level Level op page depth
	 * @return Mapped pages
	 */
	protected function _map_page_tree($pages, $mapped_pages = array(), $level = 0)
	{
		$prefix = '';
		for ($i = 0; $i < $level; $i++) {
			$prefix .= '&nbsp;&nbsp;&nbsp;&nbsp;';
		}
		if ($level != 0) {
			$prefix .= '&#8627;&nbsp;';
		}

		foreach ($pages as $page)
		{
			$mapped_pages[] = array(
				'id' => $page['id'],
				'title' => $prefix . $page['title']
			);

			if (!empty($page['children']))
			{
				$mapped_pages = $this->_map_page_tree($page['children'], $mapped_pages, $level+1);
			}
		}

		return $mapped_pages;
	}


}
