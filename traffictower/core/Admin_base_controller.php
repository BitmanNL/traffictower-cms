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
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2015-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_base_controller extends CMS_controller
{

	/** @var String $layout Which layout to use for generating the HTML */
	protected $layout = 'default';

	protected $auth_user_groups = array(
		'*' => 'administrator'
	);

	/**
	 * __Construct
	 *
	 *
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library(array('grocery_CRUD', 'auth'));

		// check for logged in user
		if ($this->router->fetch_class() != 'TT_login' && $this->router->fetch_class() != 'login' && $this->router->fetch_class() != 'error' && $this->router->fetch_class() != 'TT_error' && !$this->auth->check_login())
		{
			$this->auth->save_prelogin_url();
			redirect(site_url('admin/login'));
		}

		// check controller/method auth - user groups
		if($this->router->fetch_class() != 'TT_login' && $this->router->fetch_class() != 'login' && $this->router->fetch_class() != 'error' && $this->router->fetch_class() != 'TT_error' && !$this->auth->check_auth_user_groups_for_controller($this->auth_user_groups))
		{
			show_error('Forbidden.', 403);
		}

		// Still logged in; update the native session.
		$this->auth->set_native_session('auth_logged_in', TRUE);
	}

	/**
	 * Layout
	 *
	 * Uses the information in the $view, $css and $javascript
	 * variables to generate the layout for a page.
	 */
	protected function _layout()
	{
		// help
		if (check_login())
		{
			//$this->_help_general();
		}

		$this->load->helper(array('app', 'date'));

		// Load defaults
		$this->data['javascript_params'] = $this->get_javascript_param(NULL, TRUE);

		// insert the css files (if any)
		if (is_array($this->css_files))
		{
			$this->data['css_files'] = array_unique($this->css_files);
		}

		// gather the css (if any) to insert
		$this->data['css'] = '';
		if (is_array($this->css) && !empty($this->css))
		{
			foreach ($this->css as $css)
			{
				$this->data['css'] .= $css;
			}
		}

		// check if a layout was set
		if (is_null($this->layout))
		{
			show_error('Set the admin layout with $this->layout=\'layout_name\'!');
		}

		if (is_array($this->preloaded_javascript_files)) {
			$this->preloaded_javascript_files[] = asset_url('assets/admin/grocery_crud/texteditor/tinymce4/plugins/file-manager/js/file-manager.min.js');

			$this->data['preloaded_javascript_files'] = array_unique(
				$this->preloaded_javascript_files
			);
		}

		// insert the javascript files (if any)
		if (is_array($this->javascript_files))
		{
			$this->data['javascript_files'] = array_unique($this->javascript_files);
		}

		// output the html-footer including javascript (if any)
		$this->data['javascript'] = '';
		if (is_array($this->javascript) && !empty($this->javascript))
		{
			foreach ($this->javascript as $javascript)
			{
				$this->data['javascript'] .= $javascript;
			}
		}

		// is the user logged in
		$this->data['is_logged_in'] = check_login();
		$this->data['user_data'] = $this->data['is_logged_in'] ? get_user_data() : NULL;

		if (check_login())
		{
			$this->load->library('app_log');
			$this->data['last_user_logs'] = $this->app_log->get_logs(NULL, 'login_success', NULL, get_user_data('id'), FALSE, 'date_created', 'desc', 2);
		}

		// get app settings (meta ea)
		$this->load->model('shared/app_settings_model');
		$this->data['app'] = $this->app_settings_model->get_app_settings();

		$this->data['data'] = $this->data;

		// merge data into views, views get the upperhand in conficts
		$this->views = array_merge($this->data, $this->views);

		// output the html-body with the views
		$this->load->view('admin/layouts/'.$this->layout, $this->views);
	}

	protected function _help_general()
	{
		$class = $this->router->class;
		$method = $this->router->method;

		$this->load->model('admin/help_model');

		$page = $this->help_model->get_page_by_controller($class.'/'.$method);
		if(empty($page))
		{
			$page = $this->help_model->get_page_by_controller($class);
		}

		if(!empty($page))
		{
			$this->css[] = $this->load->view('admin/help/css/index.css', NULL, TRUE);
			$this->javascript[] = $this->load->view('admin/help/js/index.js', NULL, TRUE);
			$this->data['help_general'] = $this->load->view('admin/help/help_general', array('help_page' => $page), TRUE);
		}
	}
}
