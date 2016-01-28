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
 * @package   CMS\Core\Libraries
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library: Customizable Navigation library.
 * Put your custom navigation code here.
 */
class Navigation
{
	/**
	 * @var Object $ci Holds the instance of the CodeIgniter methods
	 */
	protected $ci;

	/**
	 * __Construct
	 *
	 * Loading of libraries and models
	 */
	public function __construct()
	{
		// set the CI instance so we can access the CI-libraries
		$this->ci =& get_instance();

		// load the Page model
		$this->ci->load->model('page_model');
	}

	public function get_navigation_by_parent_id($parent_id, $language, $secondary_navigation = NULL, $preview = FALSE)
	{
		$pages = $this->ci->page_model->get_navigation_by_parent_id($parent_id, $language, $secondary_navigation, $preview);
		foreach ($pages as $key => $page)
		{
			$pages[$key] = $this->_retrieve_replace_by_page($page);
		}
		return $pages;
	}

	protected function _retrieve_replace_by_page($page)
	{
		if (!empty($page['replace_by']))
		{
			if ($page['replace_by'] == 'internal' && is_numeric($page['replace_value']))
			{
				$replace_by_page = $this->ci->page_model->get_page_by_id($page['replace_value']);
				if (!empty($replace_by_page))
				{
					$replace_by_page = $this->_retrieve_replace_by_page($replace_by_page);
					$page['slug'] = $replace_by_page['slug'];
				}
			}
			if ($page['replace_by'] == 'first_sub')
			{
				// get first sub
				$secondary_navigation = !empty($page['secondary_navigation']) ? $page['secondary_navigation'] : NULL;
				$sub_navigation = $this->ci->page_model->get_navigation_by_parent_id($page['id'], $this->ci->config->item('language'), $secondary_navigation, app_preview_mode());
				if (!empty($sub_navigation))
				{
					$replace_by_page = current($sub_navigation);
					$replace_by_page = $this->_retrieve_replace_by_page($replace_by_page);
					$page['slug'] = $replace_by_page['slug'];
				}
			}
		}
		return $page;
	}

	/**
	 */
	public function get_main_navigation()
	{
		// get the main navigation items
		return $this->get_navigation_by_parent_id(0, $this->ci->config->item('language'), NULL, app_preview_mode());
	}

	/**
	 */
	public function get_sub_navigation($page_id)
	{
		$trace = $this->get_trace($page_id);

		// get the sub navigation items
		return $this->get_navigation_by_parent_id($trace[0], $this->ci->config->item('language'), NULL, app_preview_mode());
	}

	/**
	 * get_secondary_navigation
	 *
	 * Get secondary navigation items by secondary key and optional parent_id
	 *
	 * @param string $secondary_navigation Secondary navigation key, optional, default NULL
	 * @param integer $parent_id Parent id, optional, default 0
	 * @return array Navigation item list
	 */
	public function get_secondary_navigation($secondary_navigation = NULL, $parent_id = 0)
	{
		// get the secondary navigation items
		return $this->get_navigation_by_parent_id($parent_id, $this->ci->config->item('language'), $secondary_navigation, app_preview_mode());
	}

	/**
	 * get_navigation
	 *
	 * Get recursive navigation by parent_id and/or secondary navigation key, with depth
	 *
	 * @param integer $depth Depth of recursion, default 1 (no recursion)
	 * @param integer $parent_id Id of the parent in first level, default 0 (0 is infinite)
	 * @param string $secondary_navigation Secondary navigation key
	 * @param integer
	 * @return array Recursive navigation list
	 */
	public function get_navigation($depth = 0, $parent_id = 0, $secondary_navigation = NULL, $level = 1)
	{
		$navigation_items = $this->get_navigation_by_parent_id($parent_id, $this->ci->config->item('language'), $secondary_navigation, app_preview_mode());

		if($depth === 0 || $level < $depth)
		{
			// get sub items
			foreach($navigation_items as $key => $navigation_item)
			{
				$navigation_items[$key]['sub_items'] = $this->get_navigation($depth, $navigation_item['id'], $secondary_navigation, $level+1);
			}
		}

		return $navigation_items;
	}

	/**
	 */
	public function get_trace($page_id)
	{
		// generate the pages trace
		return $this->ci->page_model->get_trace($page_id);
	}

	public function get_breadcrumb($trace = array())
	{
		$breadcrumb = array();

		// get home if not present
		$home_page = $this->ci->page_model->get_home($this->ci->config->item('language'), app_preview_mode());
		if($home_page['id'] != current($trace))
		{
			$breadcrumb[] = $this->_retrieve_replace_by_page($home_page);
		}

		if(!empty($trace))
		{
			foreach($trace as $page_id)
			{
				$page = $this->ci->page_model->get_page_by_id($page_id, app_preview_mode());
				if(!empty($page))
				{
					$breadcrumb[] = $this->_retrieve_replace_by_page($page);
				}
			}
		}

		// set last key active
		$breadcrumb[end(array_keys($breadcrumb))]['active'] = TRUE;

		return $breadcrumb;
	}
}
