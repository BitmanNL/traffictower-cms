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
 * Element text admin.
 *
 * Element for displaying a text-block, admin code.
 */
class Element_text_admin extends Element_base
{
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
		$this->ci->load->model($this->element_path->models.'element_text_model');

		// load the text helper
		$this->ci->load->helper('text');

		// get text from database
		$data['text'] = $this->ci->element_text_model->get_text($this->id);

		// get optional preview version first
		$data['concept'] = $this->ci->element_text_model->get_text_concept($this->id);

		// limit number of characters in content
		if(!empty($data['text']))
		{
			$data['text']['content'] = character_limiter(strip_tags($data['text']['content'], '<p><br>'), 400);
		}

		// generate the HTML
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

		$data['revisions'] = array();//'0' => '- Ga naar versie -');

		// get config element text types
		$this->ci->load->config('admin', TRUE);
		$data['element_text_types'] = $this->ci->config->item('element_text_types', 'admin');

		// set values when not in editing mode
		if (!$data['element_id'])
		{
			// creating a new element, setting default values
			$data['text']['title'] = '';
			$data['text']['type'] = '';
			$data['text']['content'] = '';
			$data['current_status'] = 'new';
		}
		// get info from the db
		else
		{
			// load this elements model
			$this->ci->load->model($this->element_path->models.'element_text_model');

			// get concept
			$data['concept'] = $this->ci->element_text_model->get_text_concept($this->id);
			$data['published'] = $this->ci->element_text_model->get_text($this->id);

			// get the data
			$data['text'] = array();
			if(intval($this->ci->input->get('revision', TRUE)))
			{
				$data['text'] = $this->ci->element_text_model->get_text_revision($this->id, intval($this->ci->input->get('revision', TRUE)));
				$data['revision'] = '';
				$data['current_status'] = 'revision'; // kan ook published zijn
			}

			if(empty($data['text']))
			{
				$data['text'] = $data['concept'];
				$data['current_status'] = 'concept';

				if(empty($data['text']))
				{
					$data['text'] = $data['published'];
					$data['current_status'] = 'published';
				}
			}
			if($data['current_status'] != 'concept' && $data['text']['revision_id'] == $data['published']['revision_id'])
			{
				// omdat revision ook published kan zijn
				$data['current_status'] = 'published';
			}


			$data['element_content_id'] = $this->id;
			$data['element_revision_id'] = $data['text']['revision_id'];


			// get dropdown revisies
			$revisions = $this->ci->element_text_model->get_revisions($this->id);
			foreach($revisions as $revision)
			{
				if(!empty($data['concept']) && $revision['revision_id'] == $data['concept']['revision_id'])
				{
					// concept
					//$data['revisions'][$revision['revision_id']] = date('d-m-Y H:i', strtotime($revision['date_modified'])) . ': CONCEPT';
				}
				else if($revision['revision_id'] == $data['published']['revision_id'])
				{
					// published version
					$data['revisions'][$revision['revision_id']] = date('d-m-Y H:i', strtotime($revision['date_modified'])) . ': "' . $revision['title'] . '" door ' . $revision['user'] . ' ( Actieve publicatie )';
				}
				else
				{
					// revision
					// TIJDELIJK GEEN REVISIES TOT NADER ORDER
					$data['revisions'][$revision['revision_id']] =  date('d-m-Y H:i', strtotime($revision['date_modified'])) . ': "' . $revision['title'] . '" door '.$revision['user'];
				}

			}
		}

		// generate the HTML
		$output['html'] = $this->ci->load->view($this->element_path->views.'admin_edit', $data, true);
		$output['javascript'] = $this->ci->load->view($this->element_path->views.'js/admin_edit.js', $data, true);
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
		// load this elements model
		$this->ci->load->model($this->element_path->models.'element_text_model');

		// create the new content
		$content_data['user_id'] = $this->ci->auth->get_user_data('id');
		$element_content_id = $this->ci->element_text_model->create_element_content($content_data);

		return $element_content_id;
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
		if(isset($element['publish']))
		{
			// load this elements model
			$this->ci->load->model($this->element_path->models.'element_text_model');

			// publish item
			$this->ci->element_text_model->publish_element_content($element['content_id']);

			// set element visible
			$this->ci->load->model('admin/element_model');
			$this->ci->element_model->show_element($element_id, $page_id);
		}

	}

	/**
	 * Update Element Content
	 *
	 * Update the content of an element
	 *
	 * @param mixed[] $content_data Data for the new content
	 */
	public function update_element_content($content_data)
	{
		// load this elements model
		$this->ci->load->model($this->element_path->models.'element_text_model');

		if(isset($content_data['remove_concept']))
		{
			$this->ci->element_text_model->delete_element_revision($content_data['element_revision_id']);
		}
		else
		{
			// load this elements model
			$this->ci->load->model($this->element_path->models.'element_text_model');

			// get current published item
			$text = $this->ci->element_text_model->get_text_concept($this->id);
			if(empty($text))
			{
				$text = $this->ci->element_text_model->get_text($this->id);
			}

			// update or insert revision
			if($text['revision_id'] == $content_data['element_revision_id'])
			{
				// current chosen revision is also current published frontpage version
				if($text['status'] == 'concept')
				{
					$this->ci->element_text_model->update_element_content($content_data);
				}
				else
				{
					$content_data['user_id'] = $this->ci->auth->get_user_data('id');
					$content_data['element_revision_id'] = $this->ci->element_text_model->create_element_content($content_data);
				}
			}
			else
			{
				// new concept from old revision
				$content_data['user_id'] = $this->ci->auth->get_user_data('id');
				$content_data['element_revision_id'] = $this->ci->element_text_model->create_element_content($content_data);
			}

			// publish
			if(isset($content_data['publish']))
			{
				$this->ci->element_text_model->publish_element_content($content_data['element_revision_id']);

				// set element visible
				$this->ci->load->model('admin/element_model');
				$this->ci->element_model->show_element($content_data['element_id']);
			}
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
		$this->ci->load->model($this->element_path->models.'element_text_model');

		// delete the content
		$this->ci->element_text_model->delete_element_content($this->id);

		return $this->id;
	}
}
