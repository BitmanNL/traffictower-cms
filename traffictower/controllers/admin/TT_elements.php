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
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles all element actions (create, read, update, delete).
 */
class TT_elements extends Admin_controller {

	function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		show_error("Geen index voor deze controller!");
	}

	/**
	 * New Element
	 *
	 * Create a new element
	 *
	 * @param String $element_type Type of the element
	 * @param Integer $page_id Id of the page for the new element
	 * @param String $element_position position of the element on the page
	 */
	public function new_element($element_type, $page_id, $element_position)
	{
		$data['page_id'] = intval($page_id);
		$data['element_type'] = $element_type;
		$data['element_position'] = $element_position;
		$data['action'] = site_url('admin/elements/create_element');

		// load the element library
		$this->load->library('Element');

		// Generate the edit/new form
		$output = $this->element->generate_element_edit_form($data);
		$this->views['content'] = $output['html'];
		$this->javascript[] = $output['javascript'];
		$this->css[] = $output['css'];

		// load tinymce
		// No longer required with the new file manager.
		//$this->javascript_files[] = asset_url('assets/admin/grocery_crud/texteditor/tinymce4/tinymce.min.js');
		$this->javascript_files[] = asset_url('assets/admin/grocery_crud/js/jquery_plugins/config/jquery.tine_mce.config.js');

		// create the layout-HTML and show it to the user
		$this->_layout();
	}

	/**
	 * Edit Element
	 *
	 * Edit an element
	 *
	 * @param Integer $element_id Id of the element
	 */
	public function edit_element($element_id, $page_id)
	{
		$data['element_id'] = intval($element_id);

		// get element data from the DB
		$this->load->model('admin/element_model');
		$element = $this->element_model->get_element($data['element_id']);

		if (empty($element))
		{
			show_404();
		}

		$data['page_id'] = intval($page_id);
		$data['element_type'] = $element['type'];
		$data['element_content_id'] = $element['content_id'];
		$data['element_position'] = $element['position'];

		$data['action'] = site_url('admin/elements/update_element');

		// load the element library
		$this->load->library('Element');

		// Generate the edit/new form
		$output = $this->element->generate_element_edit_form($data);

		// Append a hidden textarea to make sure TinyMCE loads its assets (CSS
		// for themes, JavaScript for language files and plugins).
		$output['html'] .= '<textarea class="mini-texteditor" style="display: none;"></textarea>';

		$this->views['content'] = $output['html'];
		$this->javascript[] = $output['javascript'];
		$this->css[] = $output['css'];

		// load tinymce
		$this->javascript_files[] = asset_url('assets/admin/grocery_crud/texteditor/tinymce4/tinymce.min.js');
		$this->javascript_files[] = asset_url('assets/admin/grocery_crud/js/jquery_plugins/config/jquery.tine_mce.config.js');

		// create the layout-HTML and show it to the user
		$this->_layout();
	}

	/**
	 * Update Element Order
	 *
	 * Update the order and positions of the elements on a page
	 *
	 * returns data (success) in an ajax response or error.message, error.title
	 */
	public function update_element_order()
	{
		// get the data
		$element_order = $this->input->get('element_order');

		// load the element model
		$this->load->model('admin/element_model');

		$this->element_model->update_element_order($element_order, $this->input->get('page_id'));

		$data['success'] = true;

		// return as ajax
		$this->_json_response($data);
	}

	/**
	 * Create Element
	 *
	 * Create a new element
	 *
	 * returns data (success:true, content:HTML) in an ajax response or error.message, error.title
	 */
	public function create_element()
	{
		// get the data
		$page_id = intval($this->input->post('page_id'));
		$element = $this->input->post(null);

		// load the element library
		$this->load->library('Element');

		// create new content for this element
		$element['content_id'] = $this->element->create_element_content($element);

		// now create the element
		if ($element['content_id'])
		{
			// load the element model
			$this->load->model('admin/element_model');

			// create the element
			$element_id = $this->element_model->create_element($page_id, $element['element_position'], $element['element_type'], $element['content_id']);

			// callback after_create
			if(intval($element_id) > 0)
			{
				$this->element->callback_after_create($element, $element_id, $page_id);
			}
		}

		redirect(site_url('admin/page#page_id='.$page_id.'&tab=page-content'));
	}

	/**
	 * Update Element
	 *
	 * Update an element
	 */
	public function update_element()
	{
		// get the data
		$page_id = intval($this->input->post('page_id'));
		$element = $this->input->post(null);

		// load the element library
		$this->load->library('Element');

		// change the content for this element
		$this->element->update_element_content($element);

		redirect(site_url('admin/page#page_id='.$page_id.'&tab=page-content'));
	}

	/**
	 * Show Element
	 *
	 * Set an element to visible
	 *
	 * @param Integer $element_id This id of the element to set to visible
	 *
	 * returns data (success:true, content:HTML) in an ajax response or error.message, error.title
	 */
	public function show_element($element_id, $page_id)
	{
		// get the data
		$element_id = intval($element_id);

		$this->load->model('admin/element_model');
		$this->element_model->show_element($element_id, $page_id);

		$data['success'] = true;

		// return as ajax
		$this->_json_response($data);
	}

	/**
	 * Hide Element
	 *
	 * Set an element to invisible
	 *
	 * @param Integer $element_id This id of the element to set to invisible
	 *
	 * returns data (success:true, content:HTML) in an ajax response or error.message, error.title
	 */
	public function hide_element($element_id, $page_id)
	{
		// get the data
		$element_id = intval($element_id);

		$this->load->model('admin/element_model');
		$this->element_model->hide_element($element_id, $page_id);

		$data['success'] = true;

		// return as ajax
		$this->_json_response($data);
	}

	public function make_element_global($element_id, $page_id)
	{
		// get the data
		$element_id = intval($element_id);

		$this->load->model('admin/element_model');
		$element_created_count = $this->element_model->make_element_global($element_id, $page_id);

		if ($element_created_count > 0)
		{
			$data['success'] = TRUE;
		}
		else
		{
			$data['success'] = FALSE;
			$data['errorMessage'] = 'Dit is de enige pagina met deze layout. Globaal maken van dit element is dus (nog) niet van toepassing.';
		}

		// return as ajax
		$this->_json_response($data);
	}

	public function make_element_local($element_id, $page_id)
	{
		// get the data
		$element_id = intval($element_id);

		$this->load->model('admin/element_model');
		$this->element_model->make_element_local($element_id, $page_id);

		$data['success'] = true;

		// return as ajax
		$this->_json_response($data);
	}

	/**
	 * Delete Element
	 *
	 * Delete an element
	 *
	 * @param Integer $element_id This id of the element to delete
	 *
	 * returns data (success) in an ajax response or error.message, error.title
	 */
	public function delete_element($element_id)
	{
		$element_id = intval($element_id);

		// get element content id
		$this->load->model('admin/element_model');
		$element = $this->element_model->get_element($element_id);

		// load the element library
		$this->load->library('Element');

		// delete the element content
		$this->element->delete_element_content($element['content_id'], $element['type']);

		// load the element model
		$this->load->model('admin/element_model');

		$this->element_model->delete_element($element_id);

		$data['success'] = true;

		// return as ajax
		$this->_json_response($data);
	}

	/**
	 * custom
	 *
	 * Custom element method handler for direct access from URL
	 *
	 * @param string $element_name Element type name
	 * @param string $method Method name
	 * @param integer $element_content_id Element content id
	 * @return string Method return (html or json usually)
	 */
	public function custom($element_name, $method, $element_content_id)
	{
		$element_content_id = intval($element_content_id);

		// load the element library
		$this->load->library('Element');

		return $this->element->custom($element_name, $method, $element_content_id);
	}
}
