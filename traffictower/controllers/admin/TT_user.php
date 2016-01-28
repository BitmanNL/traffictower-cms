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
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles all user management.
 */
class TT_user extends Admin_controller
{

	public function index()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('bootstrap');
		$crud->set_table('user');
		$crud->set_subject('Gebruiker');

		// hide fields
		$crud->columns('is_super_user', 'is_active', 'screen_name', 'email', 'gebruikersgroepen');

		// new_password and new_password repeat are needed to make formvalidation work
		// id is needed to make the email unique check work
		$crud->fields('id', 'is_active', 'screen_name', 'email', 'gebruikersgroepen', 'password', 'new_password', 'new_password_repeat');
		$crud->required_fields('email', 'screen_name', 'is_active');

		$crud->set_relation_n_n('gebruikersgroepen', 'user_x_user_group', 'user_group', 'user_id', 'user_group_id', 'name');

		$crud->field_type('id', 'hidden');
		$crud->field_type('new_password_repeat', 'hidden');
		$crud->field_type('password', 'hidden');

		$crud->display_as('is_active', 'Actief');
		$crud->display_as('screen_name', 'Schermnaam');
		$crud->display_as('is_super_user', '');
		$crud->display_as('new_password', 'Wachtwoord');

		if ($crud->getState() == 'read')
		{
			$crud->field_type('new_password', 'hidden');
		}
		else
		{
			$crud->callback_field('new_password',array($this,'edit_field_callback_new_password'));
		}

		$crud->field_type('is_active','dropdown',array('yes' => 'ja','no' => 'nee'));

		$crud->callback_column('is_active',array($this,'callback_list_is_active'));
		$crud->callback_column('is_super_user',array($this,'callback_list_is_super_user'));

		$crud->callback_before_insert(array($this,'before_insert_update_callback'));
		$crud->callback_before_update(array($this,'before_insert_update_callback'));

		$crud->callback_after_insert(array($this,'after_insert_callback'));
		$crud->callback_after_update(array($this,'after_update_callback'));


		if ($crud->getState() === 'update_validation' OR $crud->getState() === 'insert_validation')
		{
			$crud->set_rules('email', 'E-mail', 'required|valid_email|callback_email_unique_check');
			$crud->set_rules('screen_name', 'Schermnaam', 'required');

			if ($this->input->post('new_password') != '' OR !$this->input->post('id'))
			{
				$crud->set_rules('new_password', 'Nieuw wachtwoord', 'required|min_length[10]|matches[new_password_repeat]');
				$crud->set_rules('new_password_repeat', 'Herhaal nieuw wachtwoord', '');
			}

			$this->form_validation->set_message('matches', 'De wachtwoorden zijn niet aan elkaar gelijk.');
		}

    	// render the crud HTML
		$crud_output = $crud->render();

		//get the needed css and javascript files
		$this->javascript_files = $crud->get_js_files();
		$this->css_files = $crud->get_css_files();

		// put the crud content in the right position
		$this->views['content'] = $crud_output->output;
		$this->css[] = $this->load->view('admin/user/css/index.css', array(), true);

		// add javascript and css to make the password generate and change function possible
		if ($crud->getState() === 'add' OR $crud->getState() === 'edit')
		{
			$this->css[] = $this->load->view('admin/user/css/edit_user.css', array(), true);
			$this->javascript[] = $this->load->view('admin/user/js/edit_user.js', array(), true);
		}

		// create the layout-HTML and show it to the user
		$this->_layout();
	}

	public function edit_field_callback_new_password($value, $primary_key, $field_info, $row = NULL)
	{
		return $this->load->view('admin/user/new_password', NULL, TRUE);
	}

	public function callback_list_is_active($value, $row)
	{
		if ($value === 'yes')
		{
			return '<i class="glyphicon glyphicon-ok"></i>';
		}
		else
		{
			return '<i class="glyphicon glyphicon-minus-sign"></i>';
		}
	}

	public function callback_list_is_super_user($value, $row)
	{
		return ($value === 'yes') ? '<i class="icon-bitman"></i>' : '<i class="glyphicon glyphicon-user"></i>';
	}

	public function before_insert_update_callback($post_array)
	{
		if($post_array['new_password'] != '')
		{
			// hash password
			$this->load->library('Bcrypt', 9);
			$post_array['password'] = $this->bcrypt->hash($post_array['new_password']);
		}

		unset($post_array['new_password']);
		unset($post_array['new_password_repeat']);

		return $post_array;
	}

	public function after_insert_callback($post_array)
	{
		// send password email
		if($this->input->post('new_password') != '' && $this->input->post('send_mail_to_user') !== FALSE)
		{
			$this->load->model('shared/app_settings_model');
			$this->load->library('email');
			$this->load->library('app_email', array('email_template' => 'admin'));

			$app_settings = $this->app_settings_model->get_app_settings();

			$this->app_email->subject('Een account is voor u aangemaakt');
			$this->app_email->message($this->load->view('app_email/emails/user_new', array('email' => $this->input->post('email'), 'password' => $this->input->post('new_password'), 'site_name' => $app_settings['site_name']), TRUE));
			$this->app_email->to($this->input->post('email'));
			$this->app_email->to_name($this->input->post('email'));
			$this->app_email->send();
		}

		// return not necessary
	}

	public function after_update_callback($post_array)
	{
		// send password email
		if($this->input->post('new_password') != '' && $this->input->post('send_mail_to_user') !== FALSE)
		{
			$this->load->model('shared/app_settings_model');
			$this->load->library('email');
			$this->load->library('app_email', array('email_template' => 'admin'));

			$app_settings = $this->app_settings_model->get_app_settings();

			$this->app_email->subject('Uw wachtwoord is aangepast');
			$this->app_email->message($this->load->view('app_email/emails/user_edit', array('email' => $this->input->post('email'), 'password' => $this->input->post('new_password'), 'site_name' => $app_settings['site_name']), TRUE));
			$this->app_email->to($this->input->post('email'));
			$this->app_email->to_name($this->input->post('email'));
			$this->app_email->send();
		}

		// return not necessary
	}

	/**
	 * Email Unique Check
	 *
	 * Check if an email address already exists in de user DB
	 *
	 * @param string $email This email address to check
	 *
	 * @return bool TRUE if the address is unique FALSE if not
	 */
	public function email_unique_check($email)
	{
		$this->load->model('shared/user_model');

		$this->form_validation->set_message('email_unique_check', 'Het opgegeven e-mailadres bestaat al.');

		return $this->user_model->is_email_unique($email, $this->input->post('id'));
	}
}
