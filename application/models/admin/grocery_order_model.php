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
 * @package   CMS\Core\Models
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Grocery_order_model.
 *
 * Model for Grocery_order library. Set order rows in tables in admin, setup in
 * Grocery_CRUD style.
 */
class Grocery_order_model extends CI_Model
{

	/**
	 * get_items
	 *
	 * Get items by order
	 *
	 * @param string $table Table name
	 * @param string $order_field Order field name
	 * @return array Items list
	 */
	public function get_items($table, $where = array(), $order_field)
	{
		$this->db->order_by($order_field);

		if(!empty($where))
		{
			foreach($where as $condition)
			{
				$this->db->where($condition[0], isset($condition[1]) ? $condition[1] : NULL);
			}
		}

		return $this->db->get($table)->result_array();
	}

	/**
	 * get_item
	 *
	 * Get single item by primary key
	 *
	 * @param string $table Table name
	 * @param string $primary_key_field Primary key field name
	 * @param string $id Primary key value
	 * @return array Item
	 */
	public function get_item($table, $primary_key_field, $id)
	{
		$this->db->where($primary_key_field, $id);
		return $this->db->get($table)->row_array();
	}

	/**
	 * update_item
	 *
	 * Update item data by primary key
	 *
	 * @param string $table Table name
	 * @param string $primary_key_field Primary key field name
	 * @param string $id Primary key value
	 * @param array $data Data to update
	 * @return boolean Success TRUE / FALSE
	 */
	public function update_item($table, $primary_key_field, $id, $data = array())
	{
		$this->db->where($primary_key_field, $id);
		return $this->db->update($table, $data);
	}

	/**
	 * update_order_items_after
	 *
	 * Update order for all items after order value
	 *
	 * @param string $table Table name
	 * @param string $primary_key_field Primary key field name
	 * @param string $id Primary key value
	 * @param string $order_field Order field name
	 * @param string $order Order value
	 * @param array $where Custom where conditions
	 * @return boolean Success TRUE / FALSE
	 */
	public function update_order_items_after($table, $primary_key_field, $id, $order_field, $order, $where = array())
	{
		$this->db->set($order_field, "`".$order_field."` + 1", FALSE);

		$this->db->where($order_field.' >=', intval($order));
		$this->db->where($primary_key_field.' <>', intval($id));

		if(!empty($where))
		{
			foreach($where as $condition)
			{
				$this->db->where($condition[0], isset($condition[1]) ? $condition[1] : NULL);
			}
		}

		return $this->db->update($table);
	}

	/**
	 * get_new_order
	 *
	 * Get new order value (last order + 1)
	 *
	 * @param string $table Table name
	 * @param string $order_field Order field name
	 * @param array $where Custom where conditions
	 * @return integer Order value
	 */
	public function get_new_order($table, $order_field, $where = array())
	{
		$this->db->order_by($order_field, 'desc');
		$this->db->limit(1);

		if(!empty($where))
		{
			foreach($where as $condition)
			{
				$this->db->where($condition[0], isset($condition[1]) ? $condition[1] : NULL);
			}
		}

		$result = $this->db->get($table)->row_array();

		return intval($result[$order_field]) + 1;
	}

}
