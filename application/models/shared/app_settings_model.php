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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * App settings model.
 *
 * This model is responsible for loading the specific overall site information.
 */
class App_settings_model extends CI_Model
{

	/**
	 * @var array $app_settings Memorized app settings
	 */
	protected $app_settings = array();

	/**
	 * get_app_settings
	 *
	 * Get all app settings from database or memory
	 *
	 * @return array App settings
	 */
	public function get_app_settings()
	{
		if(empty($this->app_settings))
		{
			$query = $this->db->get('app_settings');
			$results = $query->result_array();

			$app_settings = array();
			foreach($results as $item)
			{
				$app_settings[$item['key']] = !is_null($item['value']) ? $item['value'] : $item['value_big'];
			}

			// save in 'memory'
			$this->app_settings = $app_settings;
		}
		else
		{
			// get from 'memory'
			$app_settings = $this->app_settings;
		}

		return $app_settings;
	}

	public function update($post)
	{
		$app_settings = $this->get_app_settings();

		foreach($post as $key => $value)
		{
			if (strlen($value) <= 255)
			{
				$value_big = NULL;
			}
			else
			{
				$value_big = $value;
				$value = NULL;
			}

			if (!isset($app_settings[$key]))
			{
				$this->db->insert('app_settings', array('key' => $key, 'value' => $value, 'value_big' => $value_big));
			}
			else
			{
				$this->db->where(array('key' => $key));
				$this->db->update('app_settings', array('value' => $value, 'value_big' => $value_big));
			}
		}
	}

	/**
	 * get
	 *
	 * Get app setting by key
	 *
	 * @param string $key App settings key
	 * @return string App settings value
	 */
	public function get($key = '')
	{
		if(empty($this->app_settings))
		{
			$this->get_app_settings();
		}

		$value = '';
		if(isset($this->app_settings[$key]))
		{
			$value = $this->app_settings[$key];
		}

		return $value;
	}

}
