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
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library: Extension of the CI_Form_validation Library.
 * Adds a function to read out the error array.
 */
class TT_Form_validation extends CI_Form_validation
{
	/**
	 * Get Errors
	 *
	 * Get an array with fieldnames and error messages
	 * of all the fields that have errors
	 *
	 * @return mixed[] array of fieldnames with errormessages
	 */
	public function get_errors()
	{
		return $this->_error_array;
	}

	/**
	 * valid_youtube_url
	 *
	 * Check if the URL is a valid Youtube URL.
	 * A valid Youtube URL contains the youtube domain and a variable 'v'
	 *
	 * @param string $url Youtube URL to check
	 * @return boolean Whether the given url is valid or not
	 */
	public function valid_youtube_url($url)
	{
		$parsed_url = parse_url($url);

		$valid = true;
		if(!strstr(strtolower($parsed_url['host']), 'youtube'))
		{
			$valid = false;
		}
		else{
			parse_str($parsed_url['query'], $query_vars);
			if(!isset($query_vars['v']) || $query_vars['v'] === '')
			{
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * valid_vimeo_url
	 *
	 * Check if the URL is a valid Vimeo URL.
	 * A valid Vimeo URL contains the vimeo domain and a integer as first segment item
	 *
	 * @param string $url Vimeo URL to check
	 * @return boolean Whether the given url is valid or not
	 */
	public function valid_vimeo_url($url)
	{
		$parsed_url = parse_url($url);

		$valid = true;
		if(!strstr(strtolower($parsed_url['host']), 'vimeo'))
		{
			$valid = false;
		}
		else{
			$segments = explode('/', trim($parsed_url['path'], '/'));

			if(!isset($segments[0]) || !is_numeric($segments[0]))
			{
				$valid = false;
			}
		}

		return $valid;
	}

	/**
	 * Check if input is a valid Dutch phone number.
	 * Requirements: (1) only numbers allowed, (2) First number must be a 0, (3) Total of 10 numbers.
	 * Spaces and dashes are also allowed but not replaced by an empty string (eg. 06-12345678 or 012 34 56 789).
	 *
	 * @param string $phonenumber Phone number to check
	 * @return boolean Valid/invalid
	 */
	public function valid_phonenumber_nl($phonenumber)
	{
		// spaces and dashes permitted
		$phonenumber = str_replace(array(" ", "-"), "", $phonenumber);

		$reg_exp = "/^(?:\+\d{2,4}|0|00\d{2,4})(?:\d{9})$/";
		return preg_match($reg_exp, $phonenumber) ? TRUE : FALSE;
	}

}
