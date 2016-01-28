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
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library: Handler for all files (get, upload and delete) included in the CMS admin Help module.
 */
class Help_files
{
	protected $_ci;

	protected $_file_types = array(
		'image' => array('jpg', 'jpeg', 'png', 'tiff', 'gif')
	);

	protected $_dir = 'assets/admin/help/';

	public function __construct()
	{
		$this->_ci =& get_instance();
	}

	public function get_all()
	{
		$files = array();

		$i = 0;
		$dir = FCPATH.$this->_dir;
		foreach(scandir($dir) as $file)
		{
			if(is_file($dir.$file) && substr($file, 0, 1) !== '.')
			{
				$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

				if(in_array($extension, $this->_file_types['image']))
				{
					$type = 'image';
				}
				else
				{
					$type = 'general';
				}

				$files[filemtime($dir.$file).'_'.$i] = array(
					'extension' => $extension,
					'type' => $type,
					'file_name' => $file,
					'modified' => date('d-m-Y H:i:s', filemtime($dir.$file))
				);

				$i++;
			}
		}

		krsort($files);

		return $files;
	}

	public function save()
	{
		$config['upload_path'] = FCPATH.$this->_dir;
		$config['overwrite'] = TRUE;
		$config['allowed_types'] = 'gif|jpg|jpeg|png|tiff|pdf|txt|zip|rar|doc|docx|xls|xlsx|csv|psd|ai|ods|odt';

		$this->_ci->load->library('upload', $config);

		if(!$this->_ci->upload->do_upload('file'))
		{
			$error = array('error' => $this->_ci->upload->display_errors());
			return FALSE;
		}
		else
		{
			$data = array('upload_data' => $this->_ci->upload->data());
			return TRUE;
		}
	}

	public function delete($file)
	{
		unlink($file);
	}

	public function get_dir()
	{
		return $this->_dir;
	}

}

/* End of file Help_files.php */
/* Location: ./application/libraries/Help_files.php */
