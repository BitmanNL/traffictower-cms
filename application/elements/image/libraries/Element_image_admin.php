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
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Element image admin.
 *
 * Element for displaying an image admin.
 */
class Element_image_admin extends Element_base
{

	protected $_defaults = array(
		'title' => '',
		'image' => '',
		'alt' => '',
		'link' => ''
	);

	/**
	 * Generate Admin Preview HTML
	 *
	 * Generate and return the HTML for this element for
	 * the preview in the admin page controller
	 *
	 * @return string The HTML for this element
	 */
	public function generate_admin_preview_HTML()
	{
		// store the generated HTML
		$output = NULL;

		// load this elements model
		$this->ci->load->model($this->element_path->models.'element_image_model');

		// get image from database
		$data['image'] = $this->ci->element_image_model->get_image($this->id);

		$output = $this->ci->load->view($this->element_path->views.'admin_preview', $data, true);

		return $output;
	}

	/**
	 * Generate Admin Edit Form
	 *
	 * Generate edit and new element form for an element
	 *
	 * @param mixed[] $data The data for the new/edit element
	 *
	 * @return mixed[] The HTML, javascript and css for this element
	 */
	public function generate_element_edit_form($data)
	{
		// store the generated HTML
		$output = array('html'=>'', 'javascript'=>'', 'css'=>'');

		// defaults
		$data['image'] = $this->_defaults;

		// load this elements model
		$this->ci->load->model($this->element_path->models.'element_image_model');

		// set values from db when in editing mode
		if ($data['element_id'])
		{
			$data['element_content_id'] = $this->id;

			// get image data
			$image_data = $this->ci->element_image_model->get_image($this->id);

			$data['image'] = array_merge($data['image'], $image_data);
		}

		// generate the HTML
		$output['html'] = $this->ci->load->view($this->element_path->views.'admin_edit', $data, true);

		// generate javascript
		$output['javascript'] = $this->ci->load->view($this->element_path->views.'js/admin_edit.js', NULL, true);

		return $output;
	}

	/**
	 * Create Element Content
	 *
	 * Create a new content row for a new element
	 * fill it with content
	 *
	 * @param mixed[] $content_data Data for the new content
	 *
	 * @return Integer The id of the content row created
	 */
	public function create_element_content($content_data)
	{
		return $this->update_element_content($content_data, FALSE);
	}

	/**
	 * Update Element Content
	 *
	 * Update the content of an element
	 *
	 * @param mixed[] $content_data Data for the new content
	 */
	public function update_element_content($content_data, $is_update = TRUE)
	{
		$this->ci->load->model($this->element_path->models.'element_image_model');

		// validation
		$this->ci->form_validation->set_rules('image', 'Afbeelding', 'required');
		$this->ci->form_validation->set_rules('alt', 'Alt titel', 'required');

		if(!$this->ci->form_validation->run()){
			flash_error_messages();
			if($is_update)
			{
				redirect(site_url('admin/elements/edit_element/'.$content_data['element_id'].'/'.$content_data['page_id']));
			}
			else
			{
				redirect(site_url('admin/elements/new_element/'.$content_data['element_type'].'/'.$content_data['page_id'].'/'.$content_data['element_position']));
			}
		}

		// update content in db
		if($is_update)
		{
			return $this->ci->element_image_model->update_element_content($content_data);
		}
		else
		{
			return $this->ci->element_image_model->create_element_content($content_data);
		}
	}

	/**
	 * Delete Element Content
	 *
	 * delete the content row for an element
	 *
	 * @return Integer The id of the content row created
	 */
	public function delete_element_content()
	{
		// load this elements model
		$this->ci->load->model($this->element_path->models.'element_image_model');

		// delete the content
		$this->ci->element_image_model->delete_element_content($this->id);
	}

}
