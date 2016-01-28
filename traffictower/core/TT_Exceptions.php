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
 * @package   CMS\Core
 * @author    Jeroen de Graaf
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extends CI_Exceptions.
 * Used for replacement of errors (e.a. 403, 404).
 */
class TT_Exceptions extends CI_Exceptions
{

	public function show_error($heading, $message, $template = 'error_general', $status_code = 500)
	{
		set_status_header($status_code);

		$error = NULL;
		if(in_array($status_code, array(403, 404)))
		{
			// custom
			$CI =& get_instance();
			$CI->load->helper('url');

			// show_error event
			$CI->event->trigger('show_error', array('message' => $message, 'status_code' => $status_code));

			if($CI->uri->segment(1) === 'admin')
			{
				$error_url = site_url('admin/error/error_'.$status_code.'?return=false');
			}
			else
			{
				$error_url = site_url('error/error_'.$status_code.'?return=false');
			}

			// check if curl exists
			if($CI->input->get('return') === FALSE) // only if GET 'return' does not exist!
			{
				if(function_exists('curl_version'))
				{
					// curl
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $error_url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$error = curl_exec($ch);
					curl_close($ch);
				}
				else
				{
					// try by file_get_contents
					$error = file_get_contents($error_url);
				}
			}

			if($error === FALSE || $error === '')
			{
				$error = NULL;
			}
		}

		// output
		if(!is_null($error))
		{
			return $error;
		}
		else
		{
			// normal
			$message = '<p>'.implode('</p><p>', ( ! is_array($message)) ? array($message) : $message).'</p>';

			if (ob_get_level() > $this->ob_level + 1)
			{
				ob_end_flush();
			}
			ob_start();
			include(APPPATH.'errors/'.$template.'.php');
			$buffer = ob_get_contents();
			ob_end_clean();
			return $buffer;
		}
	}

}
