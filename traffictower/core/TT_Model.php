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
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TT_Model extends CI_Model
{

	protected $table = NULL;

	protected $table_info = array();

	protected $primary_key = 'id';

	protected $cache = array();

	/**
	 * Set table name. Default named by called class
	 * @param string $table Table name
	 */
	public function set_table($table)
	{
		$this->table = strtolower($table);
	}

	/**
	 * Retrieve table name by called class if not set
	 */
	public function get_table()
	{
		if (is_null($this->table))
		{
			$called_class = get_called_class();
			$this->table = strtolower(str_replace('_model', '', $called_class));
		}

		return $this->table;
	}

	/**
	 * Set primary key field name. Default id
	 * @param string $primary_key Primary key field name
	 */
	public function set_primary_key($primary_key)
	{
		$this->primary_key = strtolower($primary_key);
	}

	/**
	 * Get one row result by primary key
	 * @param  string $id   Primary key value
	 * @return array        Row result
	 */
	public function get_by_id($id, $use_cache = FALSE)
	{
		$cached_result = $this->_get_cache($this->get_table(), $id);

		if ($use_cache && !is_null($cached_result))
		{
			return $cached_result;
		}
		else
		{
			$this->_check_field_indexed($this->primary_key);

			$this->db->where($this->primary_key, $id);
			$result = $this->db->get($this->get_table())->row_array();

			if (isset($result[$this->primary_key]))
			{
				$this->_set_cache($this->get_table(), $result[$this->primary_key], $result);
			}

			return $result;
		}
	}

	/**
	 * Get one row result by field=value
	 * @param  string $field   Field name
	 * @param  string $value Field value
	 * @return array        Row result
	 */
	public function get_by_field($field, $value)
	{
		$this->_check_field_indexed($field);

		$this->db->where($field, $value);
		$this->db->limit(1);

		$result = $this->db->get($this->get_table())->row_array();

		if (isset($result[$this->primary_key]))
		{
			$this->_set_cache($this->get_table(), $result[$this->primary_key], $result);
		}

		return $result;
	}

	/**
	 * Get one row result by multiple field=value.
	 * @param  array  $fields Array of field value
	 * @return array         Row result
	 */
	public function get_by_fields($fields = array())
	{
		$results = $this->get_list_by_fields($fields, NULL, 'asc', 1);

		if (isset($results[0]))
		{
			$result = $results[0];

			if (isset($result[$this->primary_key]))
			{
				$this->_set_cache($this->get_table(), $result[$this->primary_key], $result);
			}

			return $result;
		}
		else
		{
			return array();
		}
	}

	/**
	 * Get one or more results
	 * @param  string  $order  Order field
	 * @param  string  $sort   Order sort
	 * @param  integer  $limit  Limit count
	 * @param  integer $offset Offset count
	 * @return array 		Row results
	 */
	public function get_list($order = NULL, $sort = 'asc', $limit = NULL, $offset = 0)
	{
		return $this->get_list_by_field(NULL, NULL, $order, $sort, $limit, $offset);
	}

	/**
	 * Get one or more results by field=value
	 * @param  string $field   Field name
	 * @param  string $value Field value
	 * @param  string  $order  Order field
	 * @param  string  $sort   Order sort
	 * @param  integer  $limit  Limit count
	 * @param  integer $offset Offset count
	 * @param  boolean $count_all_results Give count results if true or results itself if false
	 * @return array 		Row results
	 */
	public function get_list_by_field($field = NULL, $value = NULL, $order = NULL, $sort = 'asc', $limit = NULL, $offset = 0, $count_all_results = FALSE)
	{
		$fields = array();
		if (!is_null($field))
		{
			$fields[$field] = $value;
		}
		return $this->get_list_by_fields($fields, $order, $sort, $limit, $offset, $count_all_results);
	}

	/**
	 * Get one or more results by array of field=value
	 * @param  string $fields   Array of field + value
	 * @param  string  $order  Order field
	 * @param  string  $sort   Order sort
	 * @param  integer  $limit  Limit count
	 * @param  integer $offset Offset count
	 * @param  boolean $count_all_results Give count results if true or results itself if false
	 * @return array 		Row results
	 */
	public function get_list_by_fields($fields = array(), $order = NULL, $sort = 'asc', $limit = NULL, $offset = 0, $count_all_results = FALSE)
	{
		if (!is_array($fields))
		{
			throw new Exception('Invalid fields data type');
		}

		foreach ($fields as $field => $value)
		{
			$this->_check_field_indexed($field);

			$this->db->where($field, $value);
		}

		if (!is_null($order))
		{
			$sort = ($sort == 'asc') ? 'asc' : 'desc';
			$this->db->order_by($order, $sort);

			$this->_check_field_indexed($order);
		}

		if (!is_null($limit))
		{
			$this->db->limit($limit, $offset);
		}

		if ($count_all_results)
		{
			$this->db->from($this->get_table());
			return $this->db->count_all_results();
		}
		else
		{
			return $this->db->get($this->get_table())->result_array();
		}
	}

	/**
	 * Check if a field with value is already existing in table.
	 * @param  string $field        Field name to check
	 * @param  string $value        Value to check if it is existing
	 * @param  array  $where_fields Optional extra where conditions
	 * @return boolean Field exists (true)
	 */
	public function field_exists($field, $value, $where_fields = array())
	{
		$this->db->where($field, $value);

		if (is_array($where_fields))
		{
			foreach ($where_fields as $key => $val)
			{
				$this->db->where($key, $val);
			}
		}

		$this->db->from($this->get_table());

		return ($this->db->count_all_results() > 0);
	}

	/**
	 * Create a unique slug based on existing values in table.
	 * @param  string  $field         Field name to check
	 * @param  string  $value         Slug base value
	 * @param  array   $where_fields  Optional extra where conditions
	 * @param  string  $count_divider Default count divider
	 * @param  integer $count         Current count
	 * @return string                 Unique slug
	 */
	public function create_unique_slug($field, $value, $where_fields = array(), $count_divider = '-', $count = 0)
	{
		$slug = ($count > 0) ? $value.$count_divider.$count : $value;

		if (!$this->field_exists($field, $slug, $where_fields))
		{
			return $slug;
		}
		else
		{
			return $this->create_unique_slug($field, $value, $where_fields, $count_divider, $count+1);
		}
	}

	/**
	 * Sync many-to-many relations.
	 * Exisiting pivot table primary keys will be saved (no full delete).
	 *
	 * @param integer $primary_key Primary key of table A
	 * @param array $relation_ids Primary keys of relation table B
	 * @param string $pivot_table Name of pivot table (table_a_x_table_b)
	 * @param string $primary_key_field Optional field name of primary key table A
	 * @param string $relation_field Optional field name of primary key relation table B
	 */
	public function sync($primary_key, array $relation_ids, $pivot_table, $primary_key_field, $relation_field)
	{
		$this->load->helper('array');

		// Remove doubles
		$relation_ids = array_unique($relation_ids);

		// 1. Delete all not in relation list
		$this->db->where($primary_key_field, $primary_key);
		if (!empty($relation_ids))
		{
			$this->db->where_not_in($relation_field, $relation_ids);
		}
		$this->db->delete($pivot_table);

		// 2. Get all left over relation ids
		$this->db->where($primary_key_field, $primary_key);
		$left_over_relation_ids = $this->db->get($pivot_table)->result_array();
		$left_over_relation_ids = (!empty($left_over_relation_ids) && is_array($left_over_relation_ids)) ? pluck($left_over_relation_ids, $relation_field) : array();

		// 3. Insert not in left over relation id list
		$new_relations = array();
		foreach ($relation_ids as $relation_id)
		{
			if (!in_array($relation_id, $left_over_relation_ids))
			{
				$new_relations[] = array(
					$primary_key_field => $primary_key,
					$relation_field => $relation_id
				);
			}
		}

		if (!empty($new_relations))
		{
			$this->db->insert_batch($pivot_table, $new_relations);
		}
	}

	/**
	 * Get table field information
	 * @return array Field information
	 */
	protected function _get_table_info()
	{
		if (!isset($this->table_info[$this->get_table()]))
		{
			if (preg_match('/[\'";`\\\\]/', $this->get_table()))
			{
				show_error('Special characters not allowed in table name.');
			}

			 $this->table_info[$this->get_table()] = $this->db->query("DESCRIBE `".$this->get_table()."`")->result_array();
		}

		return $this->table_info[$this->get_table()];
	}

	/**
	 * Check if field has an index (in development only)
	 * @param  string $field Field name
	 */
	protected function _check_field_indexed($field)
	{
		if (ENVIRONMENT === 'development')
		{
			$this->load->helper('array');

			$fields = pluck($this->_get_table_info(), 'Key', 'Field');
			if (isset($fields[$field]) && empty($fields[$field]))
			{
				// field has no index
				$this->firephp->warn('Field \''.$field.'\' in table \''.$this->get_table().'\' has no index!');
			}
		}
	}

	/**
	 * Get row from cache
	 * @param  string $table Table name
	 * @param  mixed $id    Primary key value (preferably integer)
	 * @return array        Row
	 */
	protected function _get_cache($table, $id)
	{
		return isset($this->cache[$table][$id]) ? $this->cache[$table][$id] : NULL;
	}

	/**
	 * Set cache for a row
	 * @param string $table Table name
	 * @param mixed $id    Primary key value (preferably integer)
	 */
	protected function _set_cache($table, $id, $row)
	{
		$this->cache[$table][$id] = $row;
	}

}
