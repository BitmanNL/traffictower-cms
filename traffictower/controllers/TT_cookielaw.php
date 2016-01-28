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
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Stores users permission to use cookies.
 */
class TT_cookielaw extends Frontend_controller
{
	/**
	 * Accept
	 *
	 * The user accepts cookies
	 */
	public function accept()
	{
		$this->load->library('session');

		// store this information in a cookie
		$cookie = array(
		    'name'   => 'cms_cookielaw_accept',
		    'value'  => 'yes',
		    'expire' => 60*60*24*365*10 // ten years
		);

		$this->input->set_cookie($cookie);

		$hash = $this->input->get('hash') !== '' ? '#' . urldecode($this->input->get('hash')) : '';

		redirect($this->session->flashdata('cookielaw_redirect_url') . $hash);
	}

	/**
	 * Reject
	 *
	 * The user rejects cookies
	 */
	public function reject()
	{
		$this->load->library('session');

		// store this information in a cookie
		$cookie = array(
		    'name'   => 'cms_cookielaw_accept',
		    'value'  => 'no',
		    'expire' => 60*60*24*365*10 // ten years
		);

		$this->input->set_cookie($cookie);

		$hash = $this->input->get('hash') !== '' ? '#' . urldecode($this->input->get('hash')) : '';

		redirect($this->session->flashdata('cookielaw_redirect_url') . $hash);
	}
}
