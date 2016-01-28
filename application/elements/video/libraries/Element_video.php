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
 * Element video.
 *
 * Element for displaying a video from YouTube or Vimeo.
 * Required: CURL, url helper
 */
class Element_video extends Element_base
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

		$this->ci->load->model($this->element_path->models . 'element_video_model');

		$data['element_position'] = $element_position;
		$data['element_order'] = $element_order;
		$data['element_type_order'] = $element_type_order;

		// get video settings
		$data['video'] = $this->ci->element_video_model->get_video($this->id);

		// output video
		$output = $this->ci->load->view($this->element_path->views . 'element_video_' . $data['video']['type'], $data, TRUE);

		return $output;
	}

}
