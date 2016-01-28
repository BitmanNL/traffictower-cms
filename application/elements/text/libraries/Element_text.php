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
 * Element text.
 *
 * Element for displaying a text-block.
 */
class Element_text extends Element_base
{
	/**
	 * Generate and return the HTML for this element
	 *
	 * @param String $element_position Name of the position in the layout
	 * @param Integer $element_order The order of appearance
	 * @param Integer $element_type_order The order of appearance per element type
	 *
	 * @return string The HTML for this element
	 */
	public function generate($element_position, $element_order, $element_type_order)
	{
		// store the generated HTML
		$output = NULL;

		// load this elements model
		$this->ci->load->model($this->element_path->models.'element_text_model');

		// get text from database
		$data['text'] = array();
		$data['element_position'] = $element_position;
		$data['element_order'] = $element_order;
		$data['element_type_order'] = $element_type_order;

		// only when logged in
		if($this->ci->input->get('preview', TRUE) === 'true')
		{
			if(app_preview_mode())
			{
				$data['text'] = $this->ci->element_text_model->get_text_concept($this->id);
			}
			else if(intval($this->ci->input->get('revision', TRUE)))
			{
				$data['text'] = $this->ci->element_text_model->get_text_revision($this->id, intval($this->ci->input->get('revision', TRUE)));
			}
		}

		// geen pagina gevonden voor preview of revision -> pak huidige pagina
		if(empty($data['text']))
		{
			$data['text'] = $this->ci->element_text_model->get_text($this->id);
		}

		// generate the HTML, only if visible
		if(is_array($data['text']) AND !empty($data['text']))
		{
			$output = $this->ci->load->view($this->element_path->views . 'element_text', $data, true);
		}

		return $output;
	}
}
