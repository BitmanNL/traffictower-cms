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
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Is responsible for loading and displaying a normal pages by slug (including home page).
 *
 * @package CMS\Core
 */
class Page extends Frontend_controller
{
	/**
	 * Index
	 *
	 * Show the default homepage
	 */
	public function index()
	{
		// get the homepage in current language
		$this->page = $this->get_home();

		// geen titel weergeven voor de homepage
		$this->page['title'] = '';

		// create the layout-HTML and show it to the user
		$this->_layout();
	}

	/**
	 * Page
	 *
	 * Show a page requested by slug
	 *
	 * @parameter string $slug the page identified by its slug
	 */
	public function show_page($slug = NULL)
	{
		$this->page = $this->get_page_by_slug($slug);
		$this->_404_on_empty($this->page);


		$this->event->trigger('post_resource', array('type' => 'page', 'id' => $this->page['id']));

		// create the layout-HTML and show it to the user
		$this->_layout();
	}
}
