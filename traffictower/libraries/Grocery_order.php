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
 * @package   CMS\Core\Libraries
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Library: Set order rows for tables in admin.
 * Setup in Grocery_CRUD style.
 */
class Grocery_order
{

	/**
	 * @var string $_table Name of table (default controller name)
	 */
	protected $_table;

	/**
	 * @var string $_subject Name of header of page (default controller name)
	 */
	protected $_subject;

	/**
	 * @var string $_order_field Field name of order field (default 'order')
	 */
	protected $_order_field = 'order';

	/**
	 * @var string $_primary_key_field Field name of primary key (default 'id')
	 */
	protected $_primary_key_field = 'id';

	/**
	 * @var array $_fields Fields to display in thead
	 */
	protected $_fields = array();

	protected $_unset_fields = array();

	/**
	 * @var array $_display_as Friendly field names to display in thead
	 */
	protected $_display_as = array();

	protected $_callback_column = array();

	protected $_callback_after_move;

	protected $_where = array();

	/**
	 * @var array $_css_files List of stylesheets to include
	 */
	protected $_css_files = array();

	/**
	 * @var array $_js_files List of javascript files to include
	 */
	protected $_js_files = array();

	/**
	 * @var string $_state Current state (index, move)
	 */
	protected $_state;

	protected $_state_data = array();

	/**
	 * @var object $_ci CodeIgniter instance
	 */
	protected $_ci;

	/**
	 * __construct
	 *
	 * Grocery_order constructor. Loads CodeIgniter instance
	 */
	public function __construct()
	{
		$this->_ci =& get_instance();
	}

	/**
	 * set_table
	 *
	 * Set custom table name
	 *
	 * @param string $table Table name
	 */
	public function set_table($table = NULL)
	{
		$this->_table = $table;
	}

	/**
	 * set_subject
	 *
	 * Set subject name
	 *
	 * @param string $subject Subject name
	 */
	public function set_subject($subject = NULL)
	{
		$this->_subject = $subject;
	}

	/**
	 * set_order_field
	 *
	 * Set order field name
	 *
	 * @param string $order_field Order field name
	 */
	public function set_order_field($order_field = NULL)
	{
		$this->_order_field = $order_field;
	}

	/**
	 * set_primary_key_field
	 *
	 * Set primary key field name
	 *
	 * @param string $primary_key_field Primary key field name
	 */
	public function set_primary_key_field($primary_key_field = NULL)
	{
		$this->_primary_key_field = $primary_key_field;
	}

	/**
	 * set_fields
	 *
	 * Set fields to display in thead
	 *
	 * @param mixed $fields Fields to display
	 */
	public function set_fields($fields = array())
	{
		if(!is_array($fields))
		{
			$fields = func_get_args();
		}

		$this->_fields = $fields;
	}

	public function unset_fields($fields = array())
	{
		if(!is_array($fields))
		{
			$fields = func_get_args();
		}

		$this->_unset_fields = $fields;
	}

	/**
	 * display_as
	 *
	 * Set friendly field name
	 *
	 * @param string $field Field name
	 * @param string $display_as Friendly field name
	 * @return object Grocery_order instance
	 */
	public function display_as($field, $display_as)
	{
		$this->_display_as[$field] = $display_as;

		return $this;
	}

	public function set_state($state, $data = array())
	{
		$this->_state = $state;
		$this->_state_data = $data;
	}

	public function callback_column($field, $callback)
	{
		$this->_callback_column[$field] = array(
			'class' => $callback[0],
			'method' => $callback[1]
		);

		return $this;
	}

	public function callback_after_move($callback)
	{
		$this->_callback_after_move = array(
			'class' => $callback[0],
			'method' => $callback[1]
		);

		return $this;
	}

	public function where($where = array())
	{
		if(!is_array($where))
		{
			$where = func_get_args();
		}

		$this->_where[] = $where;

		return $this;
	}

	public function get_state()
	{
		return $this->_state;
	}

	/**
	 * render
	 *
	 * Render order view, based on all settings
	 *
	 * @return string HTML view/JSON output
	 */
	public function render()
	{
		if(empty($this->_state))
		{
			// get state
			$total_segments = $this->_ci->uri->total_rsegments();
			$last_segment = $this->_ci->uri->segment($total_segments+1);

			if($last_segment == 'move')
			{
				$this->_state = 'move';
			}
			else
			{
				$this->_state = 'index';
			}
		}

		// set table and subject by controller if not specifically set
		if(is_null($this->_table))
		{
			$this->_table = $this->_ci->router->class;
		}
		if(is_null($this->_subject))
		{
			$this->_subject = ucfirst($this->_ci->router->class);
		}

		return $this->{'_render_state_'.$this->_state}();
	}

	/**
	 * get_css_files
	 *
	 * Get list of stylesheet files to include
	 *
	 * @return array List of stylesheet files
	 */
	public function get_css_files()
	{
		return $this->_css_files;
	}

	/**
	 * get_js_files
	 *
	 * Get list of javascript files to include
	 *
	 * @return array List of javascript files
	 */
	public function get_js_files()
	{
		return $this->_js_files;
	}

	/**
	 * _render_state_index
	 *
	 * Render state index
	 *
	 * @return string HTML view
	 */
	protected function _render_state_index()
	{
		// get items
		$this->_ci->load->model('admin/grocery_order_model');
		$item_data = $this->_ci->grocery_order_model->get_items($this->_table, $this->_where, $this->_order_field);

		// set fields to display
		if(empty($this->_fields) && isset($item_data[0]))
		{
			$this->_fields = array_keys($item_data[0]);
			unset($this->_fields[array_search($this->_order_field, $this->_fields)]);
		}

		// unset fields
		$this->_fields = array_diff($this->_fields, $this->_unset_fields);

		// set display as if not set
		foreach($this->_fields as $field)
		{
			if(!in_array($field, array_keys($this->_display_as)))
			{
				$this->_display_as[$field] = str_replace("_", " ", ucfirst($field));
			}
		}

		// get field only set by fields (and optional callbacks)
		$items = array();
		foreach($item_data as $key => $item)
		{
			foreach($item as $field => $value)
			{
				if(in_array($field, $this->_fields))
				{
					if(isset($this->_callback_column[$field]))
					{
						$items[$key][$field] = $this->_callback_column[$field]['class']->{$this->_callback_column[$field]['method']}($value, (object)$item);
					}
					else
					{
						$items[$key][$field] = $value;
					}
				}
			}
		}

		// render
		$data['subject'] = $this->_subject;
		$data['fields'] = $this->_fields;
		$data['display_as'] = $this->_display_as;
		$data['primary_key_field'] = $this->_primary_key_field;
		$data['items'] = $items;
		$data['item_data'] = $item_data;

		$this->_css_files[] = base_url('assets/admin/css/jqueryui/jquery-ui-1.10.3.custom.min.css');
		$this->_css_files[] = base_url('assets/admin/grocery_order/css/order.css');
		$this->_js_files[] = base_url('assets/admin/js/jquery-ui-1.10.3.custom.min.js');
		$this->_js_files[] = base_url('assets/admin/grocery_order/js/order.js');

		return $this->_ci->load->view('admin/grocery_order/index', $data, TRUE);
	}

	/**
	 * _render_state_move
	 *
	 * Render state move
	 *
	 * @return string JSON output
	 */
	protected function _render_state_move()
	{
		$this->_ci->load->model('admin/grocery_order_model');

		$item_id = intval($this->_ci->input->get('item', TRUE));
		$previous_id = intval($this->_ci->input->get('previous', TRUE));

		$data['success'] = TRUE;

		// get order previous item
		if($previous_id == 0)
		{
			$item_order = 0;
		}
		else
		{
			$previous_item = $this->_ci->grocery_order_model->get_item($this->_table, $this->_primary_key_field, $previous_id);
			if(!empty($previous_item))
			{
				$item_order = intval($previous_item[$this->_order_field]) + 1;
			}
			else
			{
				$data['success'] = FALSE;
			}
		}

		// update items
		if($data['success'] === TRUE)
		{
			// update item with new order
			$update_item_success = $this->_ci->grocery_order_model->update_item($this->_table, $this->_primary_key_field, $item_id, array($this->_order_field => $item_order));

			if($update_item_success)
			{
				// update everything after item order
				$this->_ci->grocery_order_model->update_order_items_after($this->_table, $this->_primary_key_field, $item_id, $this->_order_field, $item_order, $this->_where);
			}
			else
			{
				$data['success'] = FALSE;
			}
		}

		// set error
		if($data['success'] === FALSE)
		{
			$data['message'] = "Er is iets fout gegaan bij het verplaatsen van het item. Probeer nogmaals.";
		}

		// after move callback
		if(!is_null($this->_callback_after_move))
		{
			$this->_callback_after_move['class']->{$this->_callback_after_move['method']}($item_id);
		}

		$this->_json_response($data);
	}

	protected function _render_state_set_order()
	{
		$state_data = $this->_state_data;

		$this->_ci->load->model('admin/grocery_order_model');

		$state_data[$this->_order_field] = $this->_ci->grocery_order_model->get_new_order($this->_table, $this->_order_field, $this->_where);

		return $state_data;
	}

	/**
	 * _json_responde
	 *
	 * Render JSON output
	 *
	 * @param array $data Data to output in JSON
	 * @return string JSON output
	 */
	protected function _json_response($data = array())
	{
		// set ajax headers
		$this->_ci->output->set_header("Expires: 0");
		$this->_ci->output->set_header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0");
		$this->_ci->output->set_header("Pragma: no-cache");
		$this->_ci->output->set_header('Content-type: application/json; charset=utf-8');

		// output the json
		die(json_encode($data));
	}

}
