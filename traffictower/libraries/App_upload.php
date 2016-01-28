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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class App_upload
{
	/**
	 * @var Object $ci Holds the instance of the CodeIgniter methods
	 */
	protected $ci;
	protected $callbacks = array('before_resize' => array(), 'after_resize' => array());

	public function __construct($ci = NULL)
	{
		if (!is_null($ci))
		{
			$this->ci = $ci;
		}
		else
		{
			$this->ci =& get_instance();
		}
	}

	/**
	 * Give an HTML form upload key and a config array, this function will take care of uploading
	 * and thumbnails. Uses the Codeigniter upload-library for uploading and image-moo for thumbnais.
	 *
	 * @param  string $upload_image_key The name of the form-element used for the file upload.
	 * @param  array  $config           The same config items as for the CodeIgniter upload-library are valid
	 *                                  plus the following:
	 *                                  'upload_path' -> path to put the image and thumnails in relative to assets (required)
	 *                                  'upload_image_sizes' -> ['small' => ['width' => 400, 'height' => 500]]
	 */
	public function do_upload($upload_image_key, $config)
	{
		$config['upload_path'] = $this->_make_absolute_path($config['upload_path']);

		$this->_check_patch_exists_or_create($config['upload_path']);

		$this->_check_upload_path($config['upload_path']);

		if (isset($config['upload_image_sizes']))
		{
			$image_sizes = $this->_check_upload_image_sizes($config['upload_image_sizes'], $config['upload_path']);
		}

		$this->ci->load->library('upload', $config);

		if($this->ci->upload->do_upload($upload_image_key))
		{
			$image = $this->ci->upload->data();

			if (!empty($image_sizes))
			{
				$this->_create_thumbs($image['file_name'], $config['upload_path'], $image_sizes);
			}

			return $image['file_name'];
		}
		else
		{
			throw new Exception($this->ci->upload->display_errors('', ''));
		}
	}

	public function remove_image($file_name, $config)
	{
		$config['upload_path'] = $this->_make_absolute_path($config['upload_path']);
		$this->_check_upload_path($config['upload_path']);

		if (empty($file_name))
		{
			throw new Exception('$file_name may not be empy');
		}

		$file = $config['upload_path'] . '/' . $file_name;
		if (file_exists($file))
		{
			unlink($file);
		}

		foreach ($config['upload_image_sizes'] as $size_name => $image_size)
		{
			$file = $config['upload_path'] . '/' . $size_name . '/' . $file_name;

			if (file_exists($file))
			{
				unlink($file);
			}
		}
	}

	/**
	 * Set callback function. This function will be executed before the resize command on
	 * the image_moo object. The function will be called with the image_moo object and
	 * the thumb config.
	 * The callback function could be something like this:
	 *
	 * $callback = function($image_moo, $thumb_config)
	 * {
	 *     if (isset($thumb_config['rotation_angle']))
	 *     {
	 *         return $image_moo->rotate($thumb_config['rotation_angle']);
	 *     }
	 *     else
	 *     {
	 *         return $image_moo;
	 *     }
	 * };
	 *
	 * @param  function $callback Callback function
	 */
	public function callback_before_resize($callback)
	{
		$this->callbacks['before_resize'][] = $callback;
	}

	/**
	 * Set callback function. This function will be executed after the resize command on
	 * the image_moo object. The function will be called with the image_moo object and
	 * the thumb config.
	 * The callback function could be something like this:
	 *
	 * $callback = function($image_moo, $thumb_config)
	 * {
	 *     if (isset($thumb_config['border']))
	 *     {
	 *         return $image_moo->border($thumb_config['border']['width'],$thumb_config['border']['color']);
	 *     }
	 *     else
	 *     {
	 *         return $image_moo;
	 *     }
	 * };
	 *
	 * @param  function $callback Callback function
	 */
	public function callback_after_resize($callback)
	{
		$this->callbacks['after_resize'][] = $callback;
	}

	/**
	 * Create thumbs of the given image.
	 *
	 * @param  string $file_name   File name
	 * @param  string $file_path   Absolute path to the file
	 * @param  array  $image_sizes List of sizes ['small' => ['width' => 400, 'height' => 500]]
	 */
	protected function _create_thumbs($file_name, $file_path, $image_sizes)
	{
		// maak de resized images
		foreach ($image_sizes as $size_name => $image_size)
		{
			$this->_create_thumb($file_name, $file_path, $size_name, $image_size);
		}
	}

	/**
	 * Create thumbs of the given image.
	 *
	 * @param  string $file_name   File name
	 * @param  string $file_path   Absolute path to the file
	 * @param  string #size_name   Name for this thumb type
	 * @param  array  $thumb_config  ['width' => 400, 'height' => 500]
	 */
	protected function _create_thumb($file_name, $file_path, $size_name, $thumb_config)
	{
		$this->ci->load->library('image_moo');

		$quality = isset($thumb_config['quality']) ? intval($thumb_config['quality']) : 100;

		$this->ci->image_moo->set_jpeg_quality($quality);

		$image_size_path = $file_path . '/' . $size_name;
		$this->_check_patch_exists_or_create($image_size_path);

		// Load original image.
		$this->ci->image_moo->load($file_path . '/' . $file_name);

		$this->_execute_callbacks('before_resize', array($this->ci->image_moo, $thumb_config));

		// Set thumb resize config in image moo.
		$this->_thumb_resize($this->ci->image_moo, $thumb_config);

		$this->_execute_callbacks('after_resize', array($this->ci->image_moo, $thumb_config));

		// Save thumb.
		$this->ci->image_moo->save($image_size_path . '/' .$file_name, TRUE);

		$this->ci->image_moo->clear();
	}

	/**
	 * Work on an Image Moo object to resize an image.
	 * @param  object $image_moo    Image Moo object
	 * @param  array  $thumb_config Thumb configuration e.g. ['width' => 400, 'height' => 500]
	 * @return object               Image Moo object
	 */
	protected function _thumb_resize($image_moo, $thumb_config)
	{
		if (isset($thumb_config['crop']) && $thumb_config['crop'])
		{
			$image_moo->resize_crop($thumb_config['width'], $thumb_config['height']);
		}
		else
		{
			$image_moo->resize($thumb_config['width'], $thumb_config['height']);
		}

		return $image_moo;
	}

	/**
	 * Execute all calbacks with given key.
	 *
	 * @param  string $callback_key Callback key
	 * @param  array  $params       List of params that should be called on the callback function
	 */
	protected function _execute_callbacks($callback_key, $params)
	{
		if (isset($this->callbacks[$callback_key]))
		{
			foreach ($this->callbacks[$callback_key] as $callback)
			{
				call_user_func_array($callback, $params);
			}
		}
	}

	/**
	 * Check if the absolute upload path is valid
	 *
	 * @param  string $upload_path Absolute path
	 * @return string              Absolute path
	 */
	protected function _check_upload_path($upload_path)
	{
		if (empty($upload_path))
		{
			throw new Exception('Uploadpad (upload_path) mag niet leeg zijn, zet het uploadpad in de config.');
		}

		if (preg_match('/[.]{2}/', $upload_path) === 1)
		{
			throw new Exception('Uploadpad (upload_path) mag geen punten (..) bevatten.');
		}

		return $upload_path;
	}

	/**
	 * Checks if given path relative to assets/ exists and creates if not. Return absolute path.
	 *
	 * @param  string $relative_upload_path Path relative to assets/
	 * @return string                       Absolute path
	 */
	protected function _make_absolute_path($relative_upload_path)
	{
		$absolute_upload_path = FCPATH . 'assets/' . $relative_upload_path;

		return $absolute_upload_path;
	}

	/**
	 * Check if an absolute path exists, otherwise create it.
	 *
	 * @param  string $absolute_upload_path Absolute path to a directory.
	 */
	protected function _check_patch_exists_or_create($absolute_upload_path)
	{
		if (!file_exists($absolute_upload_path))
		{
			mkdir($absolute_upload_path, 0777, TRUE);
		}
	}

	/**
	 * Check the image sizes config array. Check if the paths exists and are valid
	 *
	 * @param  array $upload_image_sizes List of sizes ['small' => ['width' => 400, 'height' => 500]]
	 * @param  string $path              Aboslute path to the original image directory
	 * @return array                     List of sizes ['small' => ['width' => 400, 'height' => 500]]
	 */
	protected function _check_upload_image_sizes($upload_image_sizes, $path)
	{
		if (!is_array($upload_image_sizes))
		{
			throw new Exception('Upload_image_sizes moet een array zijn.');
		}

		foreach ($upload_image_sizes as $size_name => $image_size)
		{
			$this->_check_upload_path(rtrim($path, '/') . '/' . $size_name);
		}

		return $upload_image_sizes;
	}

}
