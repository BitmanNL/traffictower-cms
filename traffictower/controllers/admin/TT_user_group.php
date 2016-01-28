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
 * @package   CMS\Core\Admin
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles all user group management.
 */
class TT_user_group extends Admin_controller
{

	public function index()
	{
		$crud = new grocery_CRUD();

		// crud settings
		$crud->set_theme('bootstrap');
		$crud->set_table('user_group');
		$crud->set_subject('Gebruikersgroepen');

		$crud->columns('name', 'key');

		$crud->display_as('name', 'Naam');
		$crud->required_fields('name');

		$crud->callback_before_insert(array($this,'before_insert'));
		$crud->change_field_type('key', 'invisible');

		$crud->set_relation_n_n('gebruikers', 'user_x_user_group', 'user', 'user_group_id', 'user_id', 'email');

    	// render the crud HTML
		$crud_output = $crud->render();
		$this->javascript_files = $crud->get_js_files();
		$this->css_files = $crud->get_css_files();
		$this->views['content'] = $crud_output->output;
		$this->_layout();
	}

	public function before_insert($post_array, $id)
	{
		// create key
		$this->load->model('shared/user_group_model');
		$post_array['key'] = $this->user_group_model->make_slug($post_array['name'], 0, intval($id));
		return $post_array;
	}

}
