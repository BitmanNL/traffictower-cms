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
 * Element image.
 *
 * Element for displaying an image element.
 */
class Element_image extends Element_base
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

		$this->ci->load->model($this->element_path->models . 'element_image_model');

		$data['element_position'] = $element_position;
		$data['element_order'] = $element_order;
		$data['element_type_order'] = $element_type_order;

		// get image
		$data['image'] = $this->ci->element_image_model->get_image($this->id);

		// get target for link
		$data['image']['target'] = NULL;
		if (!empty($data['image']['link']))
		{
			if (strstr($data['image']['link'], 'http://') || strstr($data['image']['link'], 'https://')) {
				$data['image']['target'] = '_blank';
			}
		}

		// output image
		$output = $this->ci->load->view($this->element_path->views . 'element_image', $data, TRUE);

		return $output;
	}

}
