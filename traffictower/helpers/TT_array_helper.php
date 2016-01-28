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
 * @package   CMS\Core\Helpers
 * @author    Jeroen de Graaf
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('pluck'))
{
	function pluck(array $items, $field, $key = NULL)
	{
		if (empty($items))
		{
			return array();
		}

		if (is_string($field) || count($field) === 1)
		{
			if (is_array($field))
			{
				$field = $field[0];
			}

			$field_count = 0;
			$key_count = 0;

			foreach ($items as $item)
			{
				if (isset($item[$field]) || is_null($item[$field]))
				{
					$field_count++;

					if (is_null($key))
					{
						$data[] = $item[$field];
					}
					else if (isset($item[$key]))
					{
						$key_count++;
						$data[$item[$key]] = $item[$field];
					}
				}
			}

			if ($field_count === 0)
			{
				throw new Exception('Field not found in items');
			}

			if (!is_null($key) && $key_count === 0)
			{
				throw new Exception('Key not found in items');
			}

			return $data;
		}
		else
		{
			// Multi column pluck
			$data = array();

			if (!empty($items))
			{
				$key_count = 0;

				foreach ($items as $item)
				{
					if (is_null($key))
					{
						$data[] = elements($field, $item);
					}
					else if (isset($item[$key]))
					{
						$key_count++;
						$data[$item[$key]] = elements($field, $item);
					}
				}

				if (!is_null($key) && $key_count === 0)
				{
					throw new Exception('Key not found in items');
				}
			}

			return $data;
		}
	}
}


/**
 * Divide array into a number of given columns.
 *
 * @param array #array Array to divide
 * @param integer $column_count Number of column to divide array in
 * @return array Array divided intro given column count
 */
if(!function_exists('array_chunk_column'))
{
	function array_chunk_column(array $array, $column_count)
	{
		return array_chunk($array, ceil(count($array) / (int)$column_count));
	}
}

/* End of file CMS_array_helper.php */
/* Location: ./application/helpers/CMS_array_helper.php */
