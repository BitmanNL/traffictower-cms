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
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles app settings for the application.
 */
class TT_site extends Admin_controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('shared/app_settings_model');
	}

	public function index()
	{
		$data['app_settings'] = $this->app_settings_model->get_app_settings();

		// get form submit
		$data['form_submit_success'] = $this->session->flashdata('form_submit_success');

		$this->views['content'] = $this->load->view('admin/site/index', $data, TRUE);

		$this->javascript_files[] = asset_url('assets/admin/js/cms.config.js');
		$this->javascript_files[] = asset_url('assets/admin/grocery_crud/texteditor/tinymce4/tinymce.min.js');
		$this->javascript_files[] = asset_url('assets/admin/grocery_crud/js/jquery_plugins/config/jquery.tine_mce.config.js');
		$this->javascript_files[] = asset_url('assets/admin/grocery_crud/js/jquery_plugins/jquery.datetimepicker.js');
		$this->css_files[] = asset_url('assets/admin/grocery_crud/css/jquery_plugins/jquery.datetimepicker.css');
		$this->javascript[] = $this->load->view('admin/site/js/index.js', $data, TRUE);

		// create the layout-HTML and show it to the user
		$this->_layout();
	}

	public function update()
	{
		$this->form_validation->set_rules('site_name', 'Website naam', 'required');
		$this->form_validation->set_rules('email', 'E-mailadres', 'valid_email');
		$this->form_validation->set_rules('url', 'URL', 'prep_url');

		if($this->form_validation->run())
		{
			$post_data = $this->input->post();
			$post_data['description'] = str_replace("\n", " ", strip_tags($post_data['description']));

			$this->app_settings_model->update($post_data);
			$this->session->set_flashdata('form_submit_success', TRUE);
		}
		else
		{
			flash_error_messages();
		}

		redirect(site_url('admin/site'));
	}

}
