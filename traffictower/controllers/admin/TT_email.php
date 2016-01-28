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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TT_email extends Admin_controller
{

	public function index($language = '')
	{
		// bepaal de taal
		$data['languages'] = $this->config->item('languages');
		$data['language_data'] = $this->config->item('language_data');
		if (!in_array($language, $data['languages']))
		{
			$data['language'] = $data['languages'][0];
		}
		else
		{
			$data['language'] = $language;
		}

		// voor de callback
		$this->language = $data['language'];

		$crud = new grocery_CRUD();

		$crud->set_theme('bootstrap');
		$crud->set_table('app_email');
		$crud->set_subject('E-mail');

		$crud->where('language', $data['language']);

		$crud->unset_print();
		$crud->unset_export();
		$crud->unset_read();

		if(!is_super_user())
		{
			$crud->unset_add();
			$crud->unset_delete();

			$fields = array('subject', 'message');

			// get email if state is edit to retrieve availability for from and to
			if ($crud->getState() == 'edit' || $crud->getState() == 'update' || $crud->getState() == 'update_validation' || $crud->getState() == 'read')
			{
				// edit state -> get item
				$email_id = $this->uri->rsegment($this->uri->total_rsegments());
				if (intval($email_id))
				{
					$this->load->model('admin/app_email_model');
					$email = $this->app_email_model->get_by_id($email_id);

					if ($email['from_available'] == 'yes')
					{
						$fields[] = 'from_name';
						$fields[] = 'from_email';
					}
					if ($email['to_available'] == 'yes')
					{
						$fields[] = 'to_name';
						$fields[] = 'to_email';
					}
				}
			}

			$crud->fields($fields);
			$crud->required_fields($fields);
			$crud->columns('subject', 'message');
		}
		else
		{
			$crud->required_fields('key', 'subject', 'from_available', 'to_available', 'template');
			$crud->columns('key', 'subject', 'message', 'from_available', 'to_available', 'template');
		}

		$crud->display_as('key', 'Sleutel')
			 ->display_as('subject', 'Onderwerp')
			 ->display_as('message', 'Bericht')
			 ->display_as('from_available', 'Afzender')
			 ->display_as('to_available', 'Ontvanger')
			 ->display_as('from_name', 'Naam afzender')
			 ->display_as('from_email', 'E-mail afzender')
			 ->display_as('to_name', 'Naam ontvanger')
			 ->display_as('to_email', 'E-mail ontvanger');

		$crud->field_type('from_available', 'dropdown', array('no' => 'Instellen in code', 'yes' => 'Instellen in CMS'));
		$crud->field_type('to_available', 'dropdown', array('no' => 'Instellen in code', 'yes' => 'Instellen in CMS'));

		$crud->callback_before_insert(array($this,'before_insert_callback'));
		$crud->change_field_type('language', 'invisible');

		// render the crud HTML
		$crud_output = $crud->render();
		$this->javascript_files = $crud->get_js_files();
		$this->css_files = $crud->get_css_files();

		$data['state'] = $crud->getState();

		$this->javascript[] = $this->load->view('admin/email/js/index.js', NULL, TRUE);
		$this->views['content'] = $this->load->view('admin/email/index', $data, TRUE);
		$this->views['content'] .= $crud_output->output;
		$this->_layout();
	}

	public function language($language)
	{
		$this->index($language);
	}

	public function before_insert_callback($post_array)
	{
	  	$post_array['language'] = $this->language;
	  	return $post_array;
	}

}

/* End of file email.php */
/* Location: ./application/controllers/admin/email.php */
