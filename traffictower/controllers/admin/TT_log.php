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
 * Makes the logs accessible/viewable in admin.
 */
class TT_log extends Admin_controller
{

	public function index($log_type = NULL)
	{
		$this->load->model('shared/log_model');

		$this->config->load('grocery_crud');
		$this->config->set_item('grocery_crud_character_limiter', 0);

		$crud = new grocery_CRUD();

		if ($crud->getState() == 'list')
		{
			// get all known log types
			$log_types = $this->log_model->get_log_types();
			$this->views['content'] = $this->load->view('admin/log/action_selector', array('log_types' => $log_types, 'selected_log_type' => $log_type), TRUE);
			$this->javascript[] = $this->load->view('admin/log/js/action_selector.js', NULL, TRUE);
		}

		if (!is_null($log_type) && $log_type != 'ajax_list' && $log_type != 'export' && $log_type != 'print' && $log_type != 'ajax_list_info')
		{
			$crud->where(array('action' => $log_type));
			$crud->unset_columns('action');
		}

		$crud->set_theme('bootstrap');
		$crud->set_table('log');
		$crud->set_subject('Logs');

		// Op twee velden orderen. Grocerycrud houd zich niet aan de Codeigniter active record
		// maar dit werkt.
		$crud->order_by('date_created desc, id', 'desc');

		$crud->columns('date_created', 'action', 'message', 'user_id');

		$crud->set_relation('user_id', 'user', 'screen_name');

		$crud->display_as('user_id', 'Gebruiker');
		$crud->display_as('date_created', 'Tijd');
		$crud->display_as('action', 'Actie');
		$crud->display_as('message', 'Bericht');
		$crud->display_as('ip_hash', 'IP-hash');

		$crud->unset_add();
		$crud->unset_edit();
		$crud->unset_delete();

    	// render the crud HTML
		$crud_output = $crud->render();

		//get the needed css and javascript files
		$this->javascript_files = $crud->get_js_files();
		$this->css_files = $crud->get_css_files();

		if (isset($this->views['content']))
		{
			$this->views['content'] .= $crud_output->output;
		}
		else
		{
			$this->views['content'] = $crud_output->output;
		}

		$this->_layout();
	}
}
