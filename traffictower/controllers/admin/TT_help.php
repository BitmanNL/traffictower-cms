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
 * @copyright 2014-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */


if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Handles TrafficTower CMS documentation for the client.
 */
class TT_help extends Admin_controller 
{

	protected $_modules = array('core');

	public function index($id = 0, $slug = '')
	{
		$this->load->model('admin/help_model');
		$this->load->helper('date');

		// set get preview modus
		$preview_modus = 'on';
		if(is_super_user())
		{
			if($this->input->get('preview') !== FALSE)
			{
				$preview_modus = $this->input->get('preview', TRUE);
				$this->session->set_userdata('preview', $preview_modus);
			}

			$preview_modus = ($this->session->userdata('preview') !== FALSE) ? $this->session->userdata('preview') : $preview_modus;
		}

		// get all modules
		$module_path = APPPATH.'modules/';
		foreach(scandir($module_path) as $module)
		{
			if(is_dir($module_path.$module) && substr($module, 0, 1) !== '.')
			{
				$this->_modules[] = $module;
			}
		}

		// 1. get navigation
		$data['items'] = $this->_get_navigation(0, $preview_modus);
		
		if($id === 0 && !empty($data['items']))
		{
			// no id set, get first page of navigation
			redirect(site_url('admin/help/index/'.$data['items'][0]['id'].'/'.url_title($data['items'][0]['title'], '-', TRUE)));
		}
		else
		{
			$data['page'] = array();

			if($id !== 0)
			{
				// if there are pages, get page
				$data['page'] = $this->help_model->get_page($id, $preview_modus);

				if(empty($data['page']))
				{
					redirect(site_url('admin/help'));
				}

				// get alineas
				$data['paragraphs'] = $this->help_model->get_paragraphs($id, $preview_modus);

				// bestands fix
				$data['page']['content'] = $this->_file_fix($data['page']['content']);
				foreach($data['paragraphs'] as $key => $paragraph)
				{
					$data['paragraphs'][$key]['content'] = $this->_file_fix($paragraph['content']);
				}
			}
	
			$data['preview_modus'] = $preview_modus;
			$output['help_navigation'] = $this->load->view('admin/help/navigation', $data, TRUE);
			$output['help_content'] = $this->load->view('admin/help/content', $data, TRUE);

			$data['form_submit'] = '';
			if($this->session->flashdata('form_submit') !== FALSE)
			{
				$data['form_submit'] = $this->session->flashdata('form_submit');
				$data['form_message'] = $this->session->flashdata('form_message');
			}

			// 3. final output
			$this->javascript_files[] = asset_url('assets/admin/grocery_crud/js/jquery_plugins/jquery.fancybox-1.3.4.js');
			$this->css_files[] = asset_url('assets/admin/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css');
			$this->views['content'] = $this->load->view('admin/help/index', $output, TRUE);
			$this->css[] = $this->load->view('admin/help/css/index.css', NULL, TRUE);
			$this->javascript[] = $this->load->view('admin/help/js/index.js', $data, TRUE);
			$this->_layout();
		}
	}

	protected function _file_fix($string)
	{
		$regexp = "/\"\/file=(.*?)\"/";
		$string = preg_replace_callback($regexp, function($matches){
			return '"'.base_url($matches[1]).'"';
		}, $string);

		return $string;
	}

	protected function _get_navigation($parent_id = 0, $preview_modus = 'on')
	{
		$items = $this->help_model->get_pages($parent_id, $preview_modus, $this->_modules);

		foreach($items as $key => $item)
		{
			$items[$key]['sub_items'] = $this->_get_navigation($item['id'], $preview_modus);
		}

		return $items;
	}

	public function file()
	{
		if(!is_super_user())
		{
			redirect(site_url('admin/help'));
		}

		$this->load->library('help_files');

		$data['files'] = $this->help_files->get_all();
		$data['dir'] = $this->help_files->get_dir();

		$this->javascript_files[] = asset_url('assets/admin/grocery_crud/js/jquery_plugins/jquery.fancybox-1.3.4.js');
		$this->css_files[] = asset_url('assets/admin/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css');
		$this->javascript[] = $this->load->view('admin/help/js/file.js', NULL, TRUE);
		$this->views['content'] = $this->load->view('admin/help/file', $data, TRUE);
		$this->_layout();
	}

	public function file_delete($file = '')
	{
		if(!is_super_user())
		{
			redirect(site_url('admin/help'));
		}

		if(!empty($file))
		{
			$file = base64_decode(urldecode($file));

			$this->load->library('help_files');
			$this->help_files->delete($file);
		}

		redirect(site_url('admin/help/file'));
	}

	public function file_save($file = '')
	{
		if(!is_super_user())
		{
			redirect(site_url('admin/help'));
		}

		$this->load->library('help_files');
		$this->help_files->save();

		redirect(site_url('admin/help/file'));
	}

	public function page()
	{
		if(!is_super_user())
		{
			redirect(site_url('admin/help'));
		}

		$this->load->config('grocery_crud');
		$this->config->set_item('grocery_crud_text_editor_type', 'minimal');

		$crud = new grocery_CRUD();

		$crud->set_theme('bootstrap');
		$crud->set_table('help_page');
		$crud->set_subject("Documentatie pagina's");
	
		$crud->unset_add_fields('date_modified');
		$crud->unset_edit_fields('parent_id', 'date_modified');

		$crud->field_type('date_created', 'invisible');
		$crud->field_type('user_id', 'invisible');

		$crud->callback_add_field('parent_id', array($this, 'page_callback_add_field_parent_id'));
		$crud->callback_before_insert(array($this, 'page_callback_before_insert'));
		$crud->callback_after_delete(array($this, 'page_callback_after_delete'));
		$crud->callback_after_insert(array($this, 'save_to_sql'));
		$crud->callback_after_update(array($this, 'save_to_sql'));

		$crud->required_fields('module', 'title', 'content', 'is_visible');

		// render the crud HTML
		$crud_output = $crud->render();

		if($crud->getState() == 'list')
		{
			redirect(site_url('admin/help'));
		}
		else if($crud->getState() == 'success')
		{
			$this->session->set_flashdata('form_submit', 'success');
			$this->session->set_flashdata('form_message', 'Pagina met succes opgeslagen.');

			$state = $crud->getStateInfo();

			$this->load->model('admin/help_model');
			$page = $this->help_model->get_page($state->primary_key, 'off');

			redirect(site_url('admin/help/index/'.$state->primary_key.'/'.url_title($page['title'], '-', TRUE)));
		}

		$this->javascript_files = $crud->get_js_files();
		$this->css_files = $crud->get_css_files();
		$this->views['content'] = $crud_output->output;
		$this->_layout();
	}

	public function page_callback_add_field_parent_id()
	{
		return '<input name="parent_id" value="'.$this->input->get('parent_id').'" type="hidden">'.$this->input->get('parent_id');
	}

	public function page_callback_before_insert($post_array)
	{
		$post_array['date_created'] = date('Y-m-d H:i:s');

		if(check_login())
		{
			$post_array['user_id'] = get_user_data('id');
		}

		return $post_array;
	}

	public function page_callback_after_delete($primary_key)
	{
		$this->load->model('admin/help_model');
		$this->help_model->delete_paragraphs($primary_key);

		$this->save_to_sql($primary_key);
	}

	public function paragraph()
	{
		if(!is_super_user())
		{
			redirect(site_url('admin/help'));
		}

		$this->load->config('grocery_crud');
		$this->config->set_item('grocery_crud_text_editor_type', 'minimal');

		$crud = new grocery_CRUD();

		$crud->set_theme('bootstrap');
		$crud->set_table('help_paragraph');
		$crud->set_subject("Documentatie alinea's");
	
		$crud->unset_add_fields('date_modified');
		$crud->unset_edit_fields('page_id', 'date_modified');

		$crud->field_type('date_created', 'invisible');
		$crud->field_type('user_id', 'invisible');
		$crud->field_type('order', 'hidden');

		$crud->callback_add_field('page_id', array($this, 'paragraph_callback_add_field_page_id'));
		$crud->callback_before_insert(array($this, 'paragraph_callback_before_insert'));
		$crud->callback_before_update(array($this, 'paragraph_callback_before_update'));
		$crud->callback_after_insert(array($this, 'save_to_sql'));
		$crud->callback_after_update(array($this, 'save_to_sql'));
		$crud->callback_after_delete(array($this, 'save_to_sql'));

		$crud->required_fields('key', 'title', 'content', 'is_visible');

		// render the crud HTML
		$crud_output = $crud->render();

		if($crud->getState() == 'list')
		{
			redirect(site_url('admin/help'));
		}
		else if($crud->getState() == 'success')
		{
			$this->session->set_flashdata('form_submit', 'success');
			$this->session->set_flashdata('form_message', 'Alinea met succes opgeslagen.');

			$state = $crud->getStateInfo();

			$this->load->model('admin/help_model');
			$paragraph = $this->help_model->get_paragraph($state->primary_key, 'off');

			redirect(site_url('admin/help/index/'.$paragraph['page_id'].'#'.$paragraph['key']));
		}

		$this->javascript_files = $crud->get_js_files();
		$this->css_files = $crud->get_css_files();
		$this->views['content'] = $crud_output->output;
		$this->_layout();
	}

	public function paragraph_callback_add_field_page_id()
	{
		return '<input name="page_id" value="'.$this->input->get('page_id').'" type="hidden">'.$this->input->get('page_id');
	}

	public function paragraph_callback_before_insert($post_array)
	{
		$post_array['date_created'] = date('Y-m-d H:i:s');

		if(check_login())
		{
			$post_array['user_id'] = get_user_data('id');
		}

		// get last order
		$this->load->model('admin/help_model');
		$paragraphs = $this->help_model->get_paragraphs($post_array['page_id']);
		$last_paragraph = reset(array_slice($paragraphs, -1, 1, TRUE));
		$post_array['order'] = intval($last_paragraph['order']) + 1;

		return $this->paragraph_callback_before_update($post_array);
	}

	public function paragraph_callback_before_update($post_array)
	{
		$post_array['key'] = url_title($post_array['key'], '-', TRUE);

		return $post_array;
	}
	
	public function paragraph_order()
	{
		if(!is_super_user())
		{
			redirect(site_url('admin/help'));
		}

		$page_id = $this->input->get('page_id');
		if($page_id !== FALSE)
		{
			$this->session->set_userdata('order_page_id', $page_id);
		}
		else
		{
			$page_id = $this->session->userdata('order_page_id');
		}


		$this->load->library('grocery_order');
		$order = new Grocery_order();
	
		$order->set_table('help_paragraph');
		$order->set_subject('');

		$order->where('page_id', intval($page_id));
		$order->unset_fields('content', 'user_id', 'date_created', 'page_id', 'id', 'key');

		$order->callback_after_move(array($this, 'save_to_sql'));
	
		$output = $order->render();

		if($order->get_state() == 'index')
		{
			// get page
			$this->load->model('admin/help_model');
			$page = $this->help_model->get_page($page_id);

			$this->views['content'] = $this->load->view('admin/help/paragraph_order', array('page' => $page), TRUE);

			$this->javascript_files = $order->get_js_files();
			$this->css_files = $order->get_css_files();
			$this->views['content'] .= $output;
			$this->_layout();	
		}
	}

	public function save_to_sql($primary_key = 0)
	{
		// dump db to install folder
		$config['tables'] = array('help_page', 'help_paragraph');
		$config['format'] = 'txt';
		$config['filename'] = 'help.sql';

		$this->load->dbutil();

		$backup =& $this->dbutil->backup($config);

		$this->load->helper('file');
		write_file(FCPATH.'install/'.$config['filename'], $backup); 
	}

	public function get_help()
	{
		$key = $this->input->get('key', TRUE);
		$controller = $this->input->get('controller', TRUE);
		$method = $this->input->get('method', TRUE);

		$this->load->model('admin/help_model');
		
		$help = array();
		$page = $this->help_model->get_page_by_controller($controller.'/'.$method);
		if(empty($page))
		{
			$page = $this->help_model->get_page_by_controller($controller);
		}

		if(!empty($page))
		{
			$help = $this->help_model->get_paragraph_by_key($key, $page['id']);
		}

		if(empty($help))
		{
			$results['success'] = FALSE;
		}
		else
		{
			$results['success'] = TRUE;
			$results['page'] = $page;
			$results['help'] = $help;
		}

		$this->_json_response($results);
	}

}

/* End of file help.php */
/* Location: ./application/controllers/admin/help.php */
