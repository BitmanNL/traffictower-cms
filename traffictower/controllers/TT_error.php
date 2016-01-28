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
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles custom error pages (403 forbidden, 404 not found).
 */
class TT_error extends Frontend_controller
{

	public function error_404()
	{
		$this->page = $this->get_home();
		$this->show_elements = FALSE;

		$this->views['content'] = $this->load->view('error/error_404', NULL, TRUE);
		$this->page['layout'] = 'default';
		$this->_layout();
	}

	public function error_403()
	{
		$this->page = $this->get_home();
		$this->show_elements = FALSE;

		$this->views['content'] = $this->load->view('error/error_403', NULL, TRUE);
		$this->page['layout'] = 'default';
		$this->_layout();
	}

}

/* End of file error.php */
/* Location: ./application/controllers/error.php */
