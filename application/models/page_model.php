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
 * Page model.
 *
 * This model is responsible for loading any
 * page related information from the database.
 */
class Page_model extends CI_Model
{
	/**
	 * @var trace
	 */
	protected $trace = array();

	/**
	 * @var pages_by_id
	 */
	protected $pages_by_id = array();

	/**
	 * Get Home
	 *
	 * Loads the default home page
	 * This is the first page in the DB
	 * that is visible and has no parent
	 *
	 * @param String $language
	 *
	 * @return object The row-data of the page
	 */
	public function get_home($language, $preview = FALSE)
	{
		$this->db->order_by('order', 'asc');

		if ($preview !== TRUE)
		{
			$this->db->where('is_visible', 'yes');
		}

		$this->db->where('secondary_navigation IS NULL', NULL, FALSE);
		$this->db->where('parent_id', 0);
		$this->db->where('language', $language);
		$this->db->limit(1);
		$query = $this->db->get('page');

		return $this->_cache_page($query->row_array());
	}

	/**
	 * Get page informatio by it's id
	 * The page has to be visible
	 *
	 * @param integer $id Identifier of the page
	 *
	 * @return object The row-data of the page
	 */
	public function get_page_by_id($id = 0, $preview = FALSE)
	{
		// check if this page already exists in cache
		if (isset($this->pages_by_id[$id]) && !$preview)
		{
			$page = $this->pages_by_id[$id];
		}
		else
		{
			$page = $this->get_page_by_id_no_cache($id, $preview);
		}

		return $page;
	}

	/**
	 * Get page informatio by it's id
	 * without using the cache
	 *
	 * @param integer $id Identifier of the page
	 *
	 * @return object The row-data of the page
	 */
	public function get_page_by_id_no_cache($id = 0, $preview = FALSE)
	{
		$this->db->where('id', $id);

		if ($preview !== TRUE)
		{
			$this->db->where('is_visible', 'yes');
		}

		$this->db->limit(1);
		$query = $this->db->get('page');

		$page = $query->row_array();

		if (!$preview)
		{
			return $this->_cache_page($page);
		}
		else
		{
			// preview pages should not be cached
			return $page;
		}
	}

	/**
	 * Get Page By Slug
	 *
	 * Get page information by it's slug
	 * The page has to be visible
	 *
	 * @param string $slug The slug of the page
	 * @param String $language
	 *
	 * @return object The row-data of the page
	 */
	public function get_page_by_slug($slug = '', $language, $preview = FALSE)
	{
		$page = array();

		// slug can not be empty
		if ($slug !== '')
		{
			$this->db->where('slug', $slug);

			if ($preview !== TRUE)
			{
				$this->db->where('is_visible', 'yes');
			}

			$this->db->where('language', $language);
			$this->db->limit(1);
			$query = $this->db->get('page');

			$page = $query->row_array();
		}

		return $this->_cache_page($page);
	}

	/**
	 * Get page information corresponding to a controller/method.
	 *
	 * @param string $language Current language
	 * @param boolean $preview Preview modus
	 * @param string $class Class name
	 * @param string $method Method name
	 * @param array $params Optional array of extra controller parameters
	 * @return object The row-data of the page
	 */
	public function get_page_by_controller($language, $preview = FALSE, $class = NULL, $method = NULL, array $params = array())
	{
		// if not given as input - use current router class and method
		$class = is_null($class) ? $this->router->class : $class;
		$method = is_null($method) ? $this->router->method : $method;

		$page = array();

		if (!empty($params))
		{
			// Check on class/method + params (only when params are filled in)
			$page = $this->_get_page_by_controller_and_parameters($language, $preview, $class . '/' . $method, $params);
		}

		if (empty($page))
		{
			// Check on class/method without params
			$page = $this->_get_page_by_controller($language, $preview, $class . '/' . $method);
		}

		if (!empty($params) && empty($page))
		{
			// Check on class + params (only when params are filled in)
			$page = $this->_get_page_by_controller_and_parameters($language, $preview, $class, $params);
		}

		if (empty($page))
		{
			// Check on class only without params
			$page = $this->_get_page_by_controller($language, $preview, $class);
		}

		return $this->_cache_page($page);
	}

	protected function _get_page_by_controller_and_parameters($language, $preview, $controller, $params)
	{
		$page = array();

		$this->db->where('controller', $controller);
		$this->db->where('controller_params <>', '');
		$this->db->where('language', $language);

		if (!$preview)
		{
			$this->db->where('is_visible', 'yes');
		}

		$this->db->order_by('order');
		$pages = $this->db->get('page')->result_array();

		// Get page with all parameters present
		foreach ($pages as $item)
		{
			parse_str($item['controller_params'], $item_params);

			$equals = 0;
			foreach ($params as $key => $value)
			{
				if (isset($item_params[$key]) && $item_params[$key] == $value)
				{
					$equals++;
				}
			}

			if (count($params) === $equals)
			{
				$page = $item;
				break;
			}
		}

		return $page;
	}

	protected function _get_page_by_controller($language, $preview, $controller)
	{
		$this->db->where('controller', $controller);
		$this->db->where('controller_params', '');
		$this->db->where('language', $language);

		if (!$preview)
		{
			$this->db->where('is_visible', 'yes');
		}

		$this->db->order_by('order');
		$this->db->limit(1);

		return $this->db->get('page')->row_array();
	}

	/**
	 * Get Navigation By Parent Id
	 *
	 * Get a (sub)navigation.
	 *
	 * @param Integer $parent_id The parent_id of the navigation to return (0 is main menu)
	 * @return mixed[] List of navigation items
	 */
	public function get_navigation_by_parent_id($parent_id, $language, $secondary_navigation = NULL, $preview = FALSE)
	{
		$this->db->order_by('order');

		if (!$preview)
		{
			$this->db->where('is_visible', 'yes');
		}

		$this->db->where('in_menu', 'yes');

		if(is_null($secondary_navigation))
		{
			$this->db->where('secondary_navigation IS NULL', NULL, FALSE);
		}
		else
		{
			$this->db->where('secondary_navigation', $secondary_navigation);
		}

		$this->db->where('parent_id', $parent_id);
		$this->db->where('language', $language);
		$query = $this->db->get('page');

		return $query->result_array();
	}

	/**
	 * Get Relative Pages
	 *
	 * Get pages in other languages relative to this page
	 *
	 * @param mixed[] $page
	 */
	public function get_relative_pages($page)
	{
		$relative_pages = array();

		$search_id = ($page['relative_page_id'] > 0) ? $page['relative_page_id'] : $page['id'];

		$query = $this->db->query("SELECT * FROM page
			WHERE is_visible = 'yes'
			AND
			(id = ? OR relative_page_id = ?)"
			, array($search_id, $search_id));

		foreach ($query->result_array() as $page)
		{
			$relative_pages[$page['language']] = $page['slug'];
		}

		return $relative_pages;
	}

	/**
	 * Get Trace
	 *
	 * Get the trace of the page requested
	 * meaning the path from the main parent
	 * the requested page
	 *
	 * @param Integer $page_id The requested page
	 *
	 * @return mixed[] List of pages in order from main parent to requested page
	 */
	public function get_trace($page_id)
	{
		// cache trace
		if(!empty($this->trace))
		{
			return $this->trace;
		}

		$trace = array();

		$current_page = $page_id;

		// build the trace
		while ($current_page != 0)
		{
			$trace[] = $current_page;
			$current_page = $this->_get_parent_id($current_page);
		}

		//reverse the trace array to show the main parent first
		$this->trace = array_reverse($trace);

		return $this->trace;
	}

	protected function _get_parent_id($page_id)
	{
		$this->db->select('parent_id');
		$this->db->where('id', $page_id);
		$this->db->limit(1);
		$query = $this->db->get('page');

		$parent = $query->row_array();

		return $parent['parent_id'];
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
		$trace = $this->get_trace($page_id);

		return $this->get_page_by_id($trace[0]);
	}

	/**
	 * Store a page in the $this->pages_by_id array
	 * so we can easily access for get_page_by_id()
	 *
	 * @param  mixed $page Page-record
	 * @return mixed       Page-record
	 */
	protected function _cache_page($page)
	{
		if (isset($page['id']))
		{
			$this->pages_by_id[$page['id']] = $page;
		}
		return $page;
	}
}
