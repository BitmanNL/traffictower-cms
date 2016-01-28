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
 * Library: Handles all general element actions for front and admin.
 */
class Element
{
	/** @type Object $ci Holds the instance of the CodeIgniter methods */
	protected $ci;

	/** $var String $element_root The path the elements */
	protected $element_root = 'elements';

	protected $css = array();
	protected $javascript = array();

	/**
	 * Construct
	 *
	 * Load necessary library and config. Set the CI instance.
	 */
	public function __construct()
	{
		// set the CI instance so we can access the CI-libraries
		$this->ci =& get_instance();

		// load base element class for elements to extend from
		$this->ci->load->library('Element_base');
	}

	/**
	 * Generate Elements
	 *
	 * Generate the elements HTML, CSS and javascript for the given page.
	 *
	 * @param integer $page_id Id for the page to generate the elements for
	 *
	 * @return mixed[] content, css and javascript inserted in each element position
	 */
	public function genenerate_elements($page_id)
	{
		// store the output for content
		$output = array();

		// get elements from db per page id
		$this->ci->load->model('element_model');
		$elements = $this->ci->element_model->get_elements_by_page_id($page_id, app_preview_mode());

		// generate the HTML for each of the elements and put
		// them in the correct position
		if (is_array($elements))
		{
			$i = array();
			$i_type = array();
			foreach ($elements as $element)
			{
				// load element
				$element_library = $this->_load_element_library($element['type'], $element['content_id']);

				// store element order
				if(!isset($i[$element['position']]))
				{
					$i[$element['position']] = 0;
				}
				if(!isset($i_type[$element['position']][$element['type']]))
				{
					$i_type[$element['position']][$element['type']] = 0;
				}

				// generate element HTML output and put it in the correct position
				// send it's own position and order
				$output_element = $this->ci->{$element_library}->generate($element['position'], $i[$element['position']], $i_type[$element['position']][$element['type']]);
				if (!isset($output[$element['position']]))
				{
					$output[$element['position']] = $output_element;
				}
				else
				{
					$output[$element['position']] .= $output_element;
				}

				// Set global element custom css and javascript
				$this->css[$element['type']] = $this->ci->{$element_library}->get_css();
				$this->javascript[$element['type']] = $this->ci->{$element_library}->get_javascript();

				$i[$element['position']]++;
				$i_type[$element['position']][$element['type']]++;
			}
		}

		// Retrieve custom css an js and add to CMS controller
		$this->_generate_element_assets();

		return $output;
	}

	/**
	 * Add custom element css and javascripts to CodeIgniter CMS controller assets.
	 */
	protected function _generate_element_assets()
	{
		foreach ($this->css as $element_type => $css_list)
		{
			if (is_array($css_list) && !empty($css_list))
			{
				foreach ($css_list as $css)
				{
					$this->ci->set_css($css);
				}
			}
		}

		foreach ($this->javascript as $element_type => $javascript_list)
		{
			if (is_array($javascript_list) && !empty($javascript_list))
			{
				foreach ($javascript_list as $javascript)
				{
					$this->ci->set_javascript($javascript);
				}
			}
		}
	}

	/**
	 * Generate Admin Elements
	 *
	 * Generate a list of all the elements and their positions
	 * with HTML to show in admin page controller
	 *
	 * @param integer $page_id Id for the page to generate the elements for
	 *
	 * @return mixed For every slot a list of their elements with HTML to be inserted in each element position
	 */
	public function genenerate_admin_elements($page_id)
	{
		// get friendly names elements
		$this->ci->config->load('admin');
		$elements_friendly_names = $this->ci->config->item('elements_friendly_names');

		// store the elements
		$output_elements = array();

		// get elements from db per page id
		$this->ci->load->model('admin/element_model');
		$elements = $this->ci->element_model->get_elements_by_page_id($page_id);

		// generate the HTML for each of the elements and put
		// them in the correct position
		if (is_array($elements))
		{
			foreach ($elements as $element)
			{
				// add friendly name
				$element['type_name'] = isset($elements_friendly_names[$element['type']]) ? $elements_friendly_names[$element['type']] : ucfirst($element['type']);

				// generate element HTML output
				$element['content'] = $this->generate_element_admin_preview($element['content_id'], $element['type']);

				// put the element in the correct position
				if (!isset($output_elements[$element['position']]))
				{
					$output_elements[$element['position']] = array();
				}
				$output_elements[$element['position']][] = $element;
			}
		}

		return $output_elements;
	}

	/**
	 * Generate Element Admin Preview
	 *
	 * Generate admin preview HTML for a specific element
	 *
	 * @param integer $element_content_id Id of the element content to genenerate
	 * @param String $element_type The type of the element
	 *
	 * @return String content HTML
	 */
	public function generate_element_admin_preview($element_content_id, $element_type)
	{
		$output = '';

		// load element
		$element_library = $this->_load_element_library($element_type, $element_content_id, true);

		// generate element HTML output
		$output = $this->ci->{$element_library}->generate_admin_preview_HTML();

		return $output;
	}

	/**
	 * Generate Element Edit Form
	 *
	 * Generate edit and new element form for an element
	 *
	 * @param mixed[] $data The data for the new/edit element
	 *
	 * @return mixed content HTML, javascript and css
	 */
	public function generate_element_edit_form($data)
	{
		if (!isset($data['element_id']))
		{
			$data['element_id'] = NULL;
			$data['element_content_id'] = NULL;
		}

		// load element
		$element_library = $this->_load_element_library($data['element_type'], $data['element_content_id'], true);

		// create hidden base fields
		$data['hidden_base_fields'] = $this->ci->load->view('element/hidden_base_fields', $data, TRUE);

		// generate element HTML output
		$output = $this->ci->{$element_library}->generate_element_edit_form($data);

		return $output;
	}

	/**
	 * Create Element Content
	 *
	 * Create the content for a new element
	 *
	 * @param mixed[] $content_data Data for the new content
	 *
	 * @return Integer The id of the content row created
	 */
	public function create_element_content($content_data)
	{
		// load element
		$element_library = $this->_load_element_library($content_data['element_type'], NULL, true);

		// create the new content for this element
		$element_content_id = $this->ci->{$element_library}->create_element_content($content_data);

		return $element_content_id;
	}

	/**
	 * Update Element Content
	 *
	 * Update the content for an element
	 *
	 * @param mixed[] $content_data Data for the new content
	 */
	public function update_element_content($content_data)
	{
		// load element
		$element_library = $this->_load_element_library($content_data['element_type'], $content_data['element_content_id'], true);

		// update the content for this element
		$this->ci->{$element_library}->update_element_content($content_data);
	}

	/**
	 * Delete Element Content
	 *
	 * Delete the content belonging to a certain element
	 *
	 * @param integer $element_content_id Id of the element content to delete the content from
	 * @param String $element_type The type of the element
	 */
	public function delete_element_content($element_content_id, $element_type)
	{
		// load element
		$element_library = $this->_load_element_library($element_type, $element_content_id, true);

		// let the element delete it's own content
		$this->ci->{$element_library}->delete_element_content();
	}

	/**
	 * _Load Element Library
	 *
	 * Load the library of an element
	 *
	 * @param String $element_type The type of the element
	 * @param integer $element_content_id Id of the element content to genenerate
	 * @param Bool $admin Load the normal or the admin library
	 */
	protected function _load_element_library($element_type, $element_content_id, $admin=false)
	{
		// set element path and library name
		$element_path = '../' . $this->element_root . '/' . $element_type . '/';
		$element_library = 'element_' . $element_type;

		if ($admin)
		{
			$element_library .= '_admin';
		}

		// load element
		$this->ci->load->library($element_path . 'libraries/' . ucfirst($element_library));
		$this->ci->{$element_library}->init($element_content_id, $element_path);

		return $element_library;
	}

	/**
	 * callback_after_create
	 *
	 * Callback fired after succesfull element create. No return needed
	 *
	 * @param array $element Element content data
	 * @param integer $element_id Element id
	 * @param integer $page_id Page id element is located on
	 */
	public function callback_after_create($element, $element_id, $page_id)
	{
		// load element
		$element_library = $this->_load_element_library($element['element_type'], NULL, TRUE);

		if(method_exists($element_library, 'callback_after_create'))
		{
			$this->ci->{$element_library}->callback_after_create($element, $element_id, $page_id);
		}
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
		// load element
		$element_library = $this->_load_element_library($element_name, NULL, TRUE);

		if(method_exists($element_library, $method))
		{
			return $this->ci->{$element_library}->{$method}($element_content_id);
		}
		else
		{
			throw new Exception('Method '.$method.' not found for element type '.$element_name);
		}
	}


}
