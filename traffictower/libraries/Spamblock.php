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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Spamblock
{
	protected $ci;

	public function __construct()
	{
		$this->ci =& get_instance();
	}

	/**
	 * Add anti spam block to form HTML.
	 * Based on hidden input field added by Javascript.
	 */
	public function secure_forms()
	{
		$this->ci->set_javascript_files(asset_url('assets/js/boterham.min.js'));
	}

	/**
	 * Validate form for spam.
	 * @param string $http_method Optional HTTP-method, default post
	 * @return boolean True if no spambot
	 */
	public function validate($http_method = 'post')
	{
		$value = ($http_method == 'get') ? $this->ci->input->get('boterham') : $this->ci->input->post('boterham');

		return ($value === '1');
	}

}

/* End of file Spamblock.php */
/* Location: ./application/libraries/Spamblock.php */
