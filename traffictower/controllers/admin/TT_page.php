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
 * @package   CMS\Core\Admin
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles page CRUD (with elements) for admin.
 */
class TT_page extends Admin_controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->load->model('admin/page_model');

		$data['languages'] = $this->config->item('languages');
		$data['language_data'] = $this->config->item('language_data');
		if (!is_array($data['languages']) || empty($data['languages']))
		{
			show_error('No languages set! Set at least one language in the languages array config/config.php');
		}

		// secondary menu's
		$this->config->load('admin');
    	$data['secondary_navigations'] = $this->config->item('secondary_navigations');
    	$data['secondary_navigations_encoded'] = str_replace(array("'", '\"'), array("\'",'\\\"'), json_encode($data['secondary_navigations']));

    	// create trees
		$data['tree'] = array();
    	$data['secondary_navigation_tree'] = array();

		foreach ($data['languages'] as $language)
		{
			$data['tree'][$language] = $this->page_model->get_page_tree($language);
			foreach ($data['secondary_navigations'] as $menu => $name)
	    	{
	    		$data['secondary_navigation_tree'][$language][$menu] = $this->page_model->get_page_tree($language, $menu);
	    	}
		}

		$data['tree_encoded'] = str_replace(array("'", '\"'), array("\'",'\\\"'), json_encode($data['tree']));
    	$data['secondary_navigation_tree_encoded'] = str_replace(array("'", '\"'), array("\'",'\\\"'), json_encode($data['secondary_navigation_tree']));
    	$data['languages_encoded'] = str_replace(array("'", '\"'), array("\'",'\\\"'), json_encode($data['languages']));
    	$data['language_data_encoded'] = str_replace(array("'", '\"'), array("\'",'\\\"'), json_encode($data['language_data']));

		// load library javascript and css files
		$this->javascript_files[] = '//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js';//asset_url('assets/js/jquery-ui-1.10.3.custom.min.js');
		$this->javascript_files[] = asset_url('assets/admin/js/jquery.cookie.js');
		$this->javascript_files[] = asset_url('assets/admin/js/jquery.contextMenu.js');
		$this->javascript_files[] = asset_url('assets/admin/js/jquery.ba-bbq.js');
		$this->javascript_files[] = asset_url('assets/admin/dynatree/jquery.dynatree.js');
		$this->css_files[] = asset_url('assets/admin/dynatree/skin-lion/ui.dynatree.css');

		// load javascript and css files
		$this->javascript[] = $this->load->view('admin/page/js/tree.js', $data, true);
		$this->javascript[] = $this->load->view('admin/page/js/page.js', $data, true);
		$this->javascript[] = $this->load->view('admin/page/js/edit_page.js', $data, true);
		$this->javascript[] = $this->load->view('admin/page/js/edit_page_replace_by.js', null, true);
		$this->javascript[] = $this->load->view('admin/page/js/edit_page_tab.js', null, true);
		$this->css[] = $this->load->view('admin/page/css/tree.css', null, true);
		$this->css[] = $this->load->view('admin/page/css/edit_page.css', null, true);

		// load the tree view
		$this->views['tree'] = $this->load->view('admin/page/tree', $data, true);

		// set the layout we want to use
		$this->layout = 'page';

		// create the layout-HTML and show it to the user
		$this->_layout();
	}

	/**
	 * Edit
	 *
	 * Show the view to edit a page. This method is called by ajax
	 *
	 * @param Integer $page_id The id of the page.
	 *
	 * returns HTML
	 */
	public function edit($page_id)
	{
		$page_id = intval($page_id);
		$data = array();

		// load the models
		$this->load->model('admin/page_model');

		// load element library
		$this->load->library('Element');

		// check if user is super user
		$data['is_super_user'] = $this->auth->get_user_data('is_super_user') === 'yes' ? TRUE : FALSE;

		// create the page
		$data['page'] = $this->page_model->get_page_by_id($page_id);

		// check if this page is in the default language
		$languages = $this->config->item('languages');
		$default_language = $languages[0];

		$data['is_default_language'] = $data['page']['language'] === $default_language;
		if (!$data['is_default_language'])
		{
			// get the page tree for the default language
			$default_page_tree = $this->page_model->get_page_tree($default_language, $data['page']['secondary_navigation']);
			$data['related_pages'] = array('Kies een pagina...');

			function parse_tree($tree, $related_pages, $level = 0)
			{
				$spacer = str_repeat('-', $level);
				foreach ($tree as $page)
				{
					$related_pages[$page['id']] = $spacer . $page['title'];
					if (count($page['children']) > 0)
					{
						$related_pages = parse_tree($page['children'], $related_pages, $level+1);
					}
				}

				return $related_pages;
			}

			$data['related_pages'] = parse_tree($default_page_tree, $data['related_pages']);
		}

		if (!$data['is_default_language'])
		{
			$data['page_preview_url'] = $data['page']['language'] . '/' . $data['page']['slug'] . '?preview=true';
			$data['language_append_url'] = $data['page']['language'] . '/';
		}
		else
		{
			$data['page_preview_url'] = $data['page']['slug'] . '?preview=true';
			$data['language_append_url'] = '';
		}

		// get the module active for this page
		$ancestor = $this->page_model->get_ancestor($data['page']['id']);

		// load the layout configuration
		$layout_config = $this->_get_layout_config($ancestor['module'], $data['page']);

		// get the layouts from config
		$data['layouts'] = $layout_config['layouts'];

		// get the element positions for this layout from the config
		$config_element_positions = $layout_config['admin_element_positions'];
		if (isset($config_element_positions[$data['page']['layout']]) AND is_array($config_element_positions[$data['page']['layout']]))
		{
			$data['element_positions'] = $config_element_positions[$data['page']['layout']];
		}
		else
		{
			$data['element_positions'] = array();
		}

		// get the elements for this page
		$data['elements'] = $this->element->genenerate_admin_elements($data['page']['id']);

		// get lost elements
		$unfilled_positions = array_diff_key($data['elements'], $data['element_positions']);
		foreach ($unfilled_positions as $position)
		{
			foreach ($position as $element)
			{
				$data['lost_elements'][] = $element;
			}
		}

		$this->load->helper('admin');

		// get all loaded elements
		$data['loaded_elements'] = $this->_get_loaded_elements();
		// get all loaded modules
		$data['loaded_modules'] = get_dir_folders(APPPATH . 'modules');

		// elements in specific container
		$elements_container_data = array();
		$element_position_data = $data;
		foreach($data['element_positions'] as $position => $position_title)
		{
			$element_position_data['position'] = $position;
			$element_position_data['position_title'] = $position_title;
			$elements_container_data[$position] = $this->load->view('admin/page/element_position.php', $element_position_data, TRUE);
		}
		$data['elements_container'] = $this->_get_elements_container($ancestor['module'], $data['page']['layout'], $elements_container_data);

		// load the form helper
		$this->load->helper('form');

		// get navigation for internal link, put pages in single depth array
		$this->config->load('admin');
    	$secondary_navigations = $this->config->item('secondary_navigations');
    	$data['internal_pages'] =  $this->page_model->get_page_tree_flattened($data['page']['language'], $secondary_navigations);

		// replacement value prepping
		$data['page']['replace_value_external'] = '';
		$data['page']['replace_value_internal'] = '';
		$data['page']['replace_value_'.$data['page']['replace_by']] = $data['page']['replace_value'];

		// return the view
		$this->load->view('admin/page/edit', $data);
	}

	protected function _get_loaded_elements()
	{
		$dir_elements = get_dir_folders(APPPATH . 'elements');
		$elements_friendly_names = $this->config->item('elements_friendly_names');

		$loaded_elements = array();
		foreach ($dir_elements as $dir_element)
		{
			$loaded_elements[$dir_element] = isset($elements_friendly_names[$dir_element]) ? $elements_friendly_names[$dir_element] : ucfirst($dir_element);
		}

		return $loaded_elements;
	}

	protected function _get_elements_container($module, $layout, $elements_container_data)
	{
		if (!empty($module) && @file_exists(APPPATH.'modules/' . $module . '/views/admin/page/layout_templates/'.$layout.'.php'))
		{
			$elements_container = $this->load->view('../modules/' . $module . '/views/admin/page/layout_templates/'.$layout.'.php', $elements_container_data, TRUE);
		}
		elseif (@file_exists(APPPATH.'views/admin/page/layout_templates/'.$layout.'.php'))
		{
			$elements_container = $this->load->view('admin/page/layout_templates/'.$layout.'.php', $elements_container_data, TRUE);
		}
		else{
			$elements_container = $this->load->view('admin/page/element_default_layout.php', array('elements_container_data' => $elements_container_data), TRUE);
		}

		return $elements_container;
	}

	protected function _get_layout_config($module, $page)
	{
		// load the admin configuration

		$layout_config = array();

		$admin_layout_library = APPPATH . 'modules/' . $module . '/libraries/Admin_layout.php';

		if (empty($module) || !file_exists($admin_layout_library))
		{
			$this->config->load('admin');
	 		$layout_config['layouts'] = $this->config->item('admin_layouts');
			$layout_config['admin_element_positions'] = $this->config->item('admin_element_positions');
 		}
 		else
 		{
 			require_once($admin_layout_library);

			$admin_layout = new Admin_layout();
			$config = $admin_layout->get_layout_config($page);

	 		$layout_config['layouts'] = $config['admin_layouts'];
			$layout_config['admin_element_positions'] = $config['admin_element_positions'];

 		}


 		return $layout_config;
	}

	/**
	 * Create Page
	 *
	 * Create a page. The new page will always be the last page.
	 *
	 * @param Integer $parent_id The id of the parent for this page.
	 *
	 * returns data (id, title) in an ajax response
	 */
	public function create_page($parent_id)
	{
		$parent_id = intval($parent_id);
		$language = $this->input->get('language');
		$data = array();

		if ($this->input->get('secondary_navigation'))
		{
			$secondary_navigation = $this->input->get('secondary_navigation');
		}
		else
		{
			$secondary_navigation = NULL;
		}
		// set the title
		$data['title'] = 'Nieuwe pagina';

		// load the page model
		$this->load->model('admin/page_model');

		// create the page
		$data['id'] = $this->page_model->create_page($data['title'], $parent_id, $language, $secondary_navigation);

		$data['success'] = true;

		// return as ajax
		$this->_json_response($data);
	}

	/**
	 * Delete Page
	 *
	 * Delete a page.
	 *
	 * @param Integer $parent_id The id for this page.
	 *
	 * returns data (success) in an ajax response
	 */
	public function delete_page($page_id)
	{
		$page_id = intval($page_id);
		$data = array();

		// load the page model
		$this->load->model('admin/page_model');

		// delete the page
		try
		{
			$data['success'] = $this->page_model->delete_page($page_id);
		}
		catch (Exception $e)
		{
			$data['success'] = FALSE;
			$data['error'] = $e->getMessage();
		}

		// return as ajax
		$this->_json_response($data);
	}

	/**
	 * Move Page
	 *
	 * Move a page to an other position.
	 *
	 * @param Integer $page_id The page that will be moved
	 * @param Integer $target_id The page to reference the move to
	 * @param String $hit_mode The type of reference (over (in), before, after)
	 * @param String $secondary_navigation name of the secondary menu or NULL
	 *
	 * returns data (success) in an ajax response
	 */
	public function move_page($page_id, $target_id, $hit_mode, $secondary_navigation)
	{
		$page_id = intval($page_id);
		$target_id = intval($target_id);
		$secondary_navigation = ($secondary_navigation == 'null' || $secondary_navigation == 'undefined') ? NULL : $secondary_navigation;
		$data = array();

		// load the page model
		$this->load->model('admin/page_model');

		// move the page
		$data['success'] = $this->page_model->move_page($page_id, $target_id, $hit_mode, $secondary_navigation);

		// return as ajax
		$this->_json_response($data);

		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * Show Page
	 *
	 * Show a page. Make it accessible and visible in the navigation
	 *
	 * @param Integer $page_id The page that will be set to visible
	 *
	 * returns data (success) in an ajax response
	 */
	public function show_page($page_id)
	{
		$page_id = intval($page_id);

		// load the page model
		$this->load->model('admin/page_model');

		// make the page accessible
		try
		{
			$this->page_model->make_visible($page_id, true);
			$data['success'] = true;
		}
		catch (Exception $e)
		{
			$data['success'] = FALSE;
			$data['error'] = $e->getMessage();
		}

		// return as ajax
		$this->_json_response($data);

		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * Hide Page
	 *
	 * Show a page. Make it inaccessible and invisible in the navigation
	 *
	 * @param Integer $page_id The page that will be set to invisible
	 *
	 * returns data (success) in an ajax response
	 */
	public function hide_page($page_id)
	{
		$page_id = intval($page_id);

		// load the page model
		$this->load->model('admin/page_model');

		// make the page accessible
		try
		{
			$this->page_model->make_visible($page_id, false);
			$data['success'] = true;
		}
		catch (Exception $e)
		{
			$data['success'] = FALSE;
			$data['error'] = $e->getMessage();
		}

		// return as ajax
		$this->_json_response($data);

		//$this->output->enable_profiler(TRUE);
	}


	/**
	 * Update
	 *
	 * Update a page with new data
	 *
	 * @param Integer $page_id The page id
	 *
	 * returns data (success) in an ajax response or error.message, error.title
	 */
	public function update($page_id)
	{
		$page = array();
		$page_id = intval($page_id);

		// get the data
		$page['title'] = $this->input->get('title', true);
		if($this->input->get('slug') !== FALSE)
		{
			$page['slug'] = $this->_slugify($this->input->get('slug', true), '-', TRUE);
		}
		$page['layout'] = $this->input->get('layout', true);
		$page['in_menu'] = $this->input->get('in_menu', true);
		$page['controller'] = $this->input->get('controller', TRUE);
		$page['controller_params'] = ($this->input->get('controller_params') !== FALSE) ? $this->input->get('controller_params', TRUE) : NULL;

		if (!empty($page['controller']) || !empty($page['controller_params']) || $this->input->get('is_system_page', true) == 'yes')
		{
			$page['is_system_page'] = 'yes';
		}
		else
		{
			$page['is_system_page'] = 'no';
		}

		$page['module'] = ($this->input->get('module') !== FALSE) ? $this->input->get('module', TRUE) : NULL;
		$page['description'] = str_replace("\n", " ", strip_tags($this->input->get('description', TRUE)));
		$page['language'] = $this->input->get('language', TRUE);
		$page['relative_page_id'] = $this->input->get('relative_page_id', TRUE);

		// replacement pages
		$page['replace_by'] = $this->input->get('replace_by', TRUE);
		$page['replace_value'] = ($this->input->get('replace_value_'.$page['replace_by'], TRUE) !== FALSE) ? $this->input->get('replace_value_'.$page['replace_by'], TRUE) : '';

		// load the page model
		$this->load->model('admin/page_model');

		// check if the slug exists
		if ($this->input->get('slug') !== FALSE && $this->page_model->slug_exists($page['slug'], $page['language'], $page_id))
		{
			$data['success'] = false;
			$data['errorMessage'] = 'Deze slug bestaat al. Kies een andere slug.';
			$data['errorFields'] = array('slug');
		}
		else
		{
			$this->page_model->update_page($page_id, $page);
			$data['success'] = true;
			$data['page'] = $page;
		}

		// return as ajax
		$this->_json_response($data);

		//$this->output->enable_profiler(TRUE);
	}

	/**
	 * Do the same as url_title, accept don't remove slashes
	 * @param  String $value
	 * @return String
	 */
	protected function _slugify($value)
	{
		$value = str_replace('/', '__DAANSINSERTSTRING__', $value);
		$slug = url_title($value);
		$slug = str_replace('__DAANSINSERTSTRING__', '/', $slug);

		return $slug;
	}

}
