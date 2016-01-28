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
 * Element video admin.
 *
 * Element for displaying a video, admin code.
 */
class Element_video_admin extends Element_base
{

	protected $_video_url = array(
		'vimeo' => 'http://www.vimeo.com/',
		'youtube' => 'http://www.youtube.com/watch?v='
	);

	protected $_defaults = array(
		'type' => 'youtube',
		'key' => '',
		'title' => '',
		'autoplay' => 0,
		'format_type' => 'relative',
		'width' => 100,
		'height' => 300,
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
		$this->ci->load->model($this->element_path->models.'element_video_model');

		// get text from database
		$data['video'] = $this->ci->element_video_model->get_video($this->id);

		$data['video']['video_url'] = $this->_video_url[$data['video']['type']];

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

		// load this elements model
		$this->ci->load->model($this->element_path->models.'element_video_model');

		// defaults
		$data['video'] = $this->_defaults;

		$data['video_types'] = $this->_video_url;

		// set values from db when in editing mode
		if($data['element_id'])
		{
			$data['element_content_id'] = $this->id;

			$video = $this->ci->element_video_model->get_video($this->id);
			$data['video'] = array_merge($data['video'], $video);
		}

		// generate the HTML
		$output['html'] = $this->ci->load->view($this->element_path->views.'admin_edit', $data, TRUE);
		$output['javascript'] = $this->ci->load->view($this->element_path->views.'js/admin_edit.js', $data, TRUE);

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
		$this->ci->load->model($this->element_path->models.'element_video_model');

		// validation

		// valid urls for vimeo or youtube/vimeo
		if($content_data['type'] === 'youtube')
		{
			$this->ci->form_validation->set_rules('url', 'URL', 'required|prep_url|valid_youtube_url');
		}
		if($content_data['type'] === 'vimeo')
		{
			$this->ci->form_validation->set_rules('url', 'URL', 'required|prep_url|valid_vimeo_url');
		}

		// valid width and height
		if($content_data['format_type'] == 'relative')
		{
			$this->ci->form_validation->set_rules('width_percentage', 'Breedte', 'required|is_natural_no_zero');
			$this->ci->form_validation->set_rules('height_percentage', 'Hoogte', 'required|is_natural_no_zero');
		}
		else
		{
			$this->ci->form_validation->set_rules('width', 'Breedte', 'required|is_natural_no_zero');
			$this->ci->form_validation->set_rules('height', 'Hoogte', 'required|is_natural_no_zero');
		}

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

		// success form validation -> prep data
		if($content_data['type'] === 'youtube')
		{
			$content_data['key'] = $this->_get_youtube_key($this->ci->form_validation->set_value('url'));

			// optional - get title from Youtube API
			if($content_data['title_from_api'] == '1')
			{
				$content_data['title'] = $this->_get_youtube_title($content_data['key']);
			}

			// get thumbnail image url
			$content_data['thumbnail'] = 'http://img.youtube.com/vi/' . $content_data['key'] . '/0.jpg';
		}
		if($content_data['type'] === 'vimeo')
		{
			$content_data['key'] = $this->_get_vimeo_key($this->ci->form_validation->set_value('url'));

			$vimeo_data = $this->_get_vimeo_data($content_data['key']);

			// optional - get title from Vimeo API
			if($content_data['title_from_api'] == '1')
			{
				$content_data['title'] = isset($vimeo_data['title']) ? $vimeo_data['title'] : '';
			}

			// get thumbnail image url
			$content_data['thumbnail'] = isset($vimeo_data['thumbnail_large']) ? $vimeo_data['thumbnail_large'] : '';
		}

		if($content_data['format_type'] == 'relative')
		{
			$content_data['width'] = $content_data['width_percentage'];
			$content_data['height'] = $content_data['height_percentage'];
		}

		// update content in db
		if($is_update)
		{
			return $this->ci->element_video_model->update_element_content($content_data);
		}
		else
		{
			return $this->ci->element_video_model->create_element_content($content_data);
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
		$this->ci->load->model($this->element_path->models.'element_video_model');

		// delete the content
		$this->ci->element_video_model->delete_element_content($this->id);
	}

	/**
	 * _get_youtube_key
	 *
	 * Get Youtube key from URL (v)
	 *
	 * @param string $url Youtube URL
	 * @return string Youtube key
	 */
	protected function _get_youtube_key($url)
	{
		$key = "";

		$parsed_url = parse_url($url);
		parse_str($parsed_url['query'], $query_vars);
		if(isset($query_vars['v']) && $query_vars['v'] !== '')
		{
			$key = trim($query_vars['v']);
		}

		return $key;
	}

	/**
	 * _get_vimeo_key
	 *
	 * Get Vimeo key from URL
	 *
	 * @param string $url Vimeo URL
	 * @return string Vimeo key
	 */
	protected function _get_vimeo_key($url)
	{
		$key = "";

		$parsed_url = parse_url($url);
		$segments = explode('/', trim($parsed_url['path'], '/'));
		if(isset($segments[0]) && is_numeric($segments[0]))
		{
			$key = intval($segments[0]);
		}

		return $key;
	}

	/**
	 * _get_youtube_title
	 *
	 * Get title from Youtube API (v2) if present
	 *
	 * @param string $key Unique Youtube video identifier
	 * @return string Video title
	 */
	protected function _get_youtube_title($key)
	{
		try
		{
			$url = "https://gdata.youtube.com/feeds/api/videos/".urlencode($key)."?v=2&alt=json";
			$data = json_decode(file_get_contents($url), TRUE);
			$title = $data['entry']['title']['$t'];
		}
		catch(Exception $e)
		{
			$title = "";
		}

		return $title;
	}

	/**
	 *
	 * Get title, thumbnail ea from Vimeo API if present
	 *
	 * @param string $key Unique Vimeo video identifier
	 * @return array Video data
	 */
	protected function _get_vimeo_data($key)
	{
		try
		{
			$url = "http://vimeo.com/api/v2/video/".urlencode($key).".json";
			$data = json_decode(file_get_contents($url), TRUE);
			return isset($data[0]) ? $data[0] : array();
		}
		catch(Exception $e)
		{
			return array();
		}
	}

}
