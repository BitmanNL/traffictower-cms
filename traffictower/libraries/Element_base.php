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
 * Library: Base class where all elements extend from.
 */
class Element_base
{
	protected $ci = NULL;
	protected $element_path;
	protected $id = NULL;
	protected $model;

	protected $css = array();
	protected $javascript = array();

	/**
	 * Initiates the element with its id and path
	 *
	 * @param integer $id The id for this element
	 * @param string $element_path The path to this element
	 */
	public function init($id, $element_path)
	{
		// reset the element, for the case it gets called for more
		// than one time
		$this->_reset();

		// set the CI instance so we can access the CI-libraries
		$this->ci =& get_instance();

		// set the element id
		$this->id = intval($id);

		// set element paths
		$this->element_path = new stdClass;
		$this->element_path->root = $element_path;
		$this->element_path->models = $element_path . 'models/';
		$this->element_path->views = $element_path . 'views/';
		$this->element_path->libraries = $element_path . 'libraries/';
		$this->element_path->helpers = $element_path . 'helpers/';

		// load model
		$this->ci->load->model($this->element_path->models . $this->get_model());
		$this->model = $this->ci->{$this->get_model()};

		// reset css and javascript
		$this->css = array();
		$this->javascript = array();
	}

	protected function get_model()
	{
		return strtolower(str_replace('_admin', '', get_called_class() . '_model'));
	}

	/**
	 * Reset the element so it can be used again
	 */
	protected function _reset()
	{
		$this->element_path = NULL;
		$this->id = NULL;
	}

	/**
	 * Give the url to edit a new element.
	 *
	 * @param  string  $element_type     Type of the element
	 * @param  integer $page_id          Id of the page the element will be placed on
	 * @param  string  $element_position Name of the position on the page the element will be placed
	 * @return string                    Url to edit new element
	 */
	protected function new_element_url($element_type, $page_id, $element_position)
	{
		return site_url('admin/elements/new_element/' . $element_type . '/' . $page_id . '/' . $element_position);
	}

	/**
	 * Give the url to edit an existing element.
	 *
	 * @param  integer $element_id Id of the element in the element table (not the content_id)
	 * @return string              Url to edit new element
	 */
	protected function edit_element_url($element_id)
	{
		return site_url('admin/elements/edit_element/' . $element_id);
	}

	public function set_css($css)
	{
		$this->css[] = $css;
	}

	public function get_css()
	{
		return $this->css;
	}

	public function set_javascript($javascript)
	{
		$this->javascript[] = $javascript;
	}

	public function get_javascript()
	{
		return $this->javascript;
	}

}
