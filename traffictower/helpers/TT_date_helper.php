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

/**
 * Taalafhankelijke versie van de php date-functie
 *
 * @param  string $format
 * @param  integer $timestamp
 * @return string            date string
 */
if(!function_exists('cms_date'))
{
	function cms_date($format, $timestamp = NULL)
	{
		$ci =& get_instance();

		$timestamp = is_null($timestamp) ? time() : $timestamp;

		if($ci->config->item('language') != 'en')
		{
			$ci->lang->load('date');
			$days = lang('days');
			$days_short = lang('days_short');
			$months = lang('months');
			$months_short = lang('months_short');

			$language_changes = array(
				array('#1#', 'l', 'w', $days),
				array('#2#', 'D', 'w', $days_short),
				array('#3#', 'F', 'n', $months),
				array('#4#', 'M', 'n', $months_short)
			);

			$format_keys = array();
			$format_replacements = array();
			foreach($language_changes as $change)
			{
				$format_keys[] = $change[0];
				$format = strstr($format, $change[1]) ? str_replace($change[1], end($format_keys), $format) : $format;
				$format_replacements[] = "\\".implode("\\", str_split($change[3][date($change[2], $timestamp)]));
			}
			$format = str_replace($format_keys, $format_replacements, $format);
		}

		return date($format, $timestamp);
	}
}

/**
 * Return a date for a recent event. ie. vandaag 16:50 or gister 12:00
 *
 * @param mixed $date Timestamp or date string (parsable by strtotime)
 * @return string
 */
if(!function_exists('human_date_recent'))
{
	function human_date_recent($date)
	{
		$ci =& get_instance();
		$ci->lang->load('date');

		$date = is_numeric($date) ? $date : strtotime($date);

		$day_reference = array(
			1 => lang('yesterday'),
			0 => lang('today'),
			-1 => lang('tomorrow')
		);

		$day_diff = ceil((strtotime(date("Y-m-d", time())) - strtotime(date("Y-m-d", $date))) / (3600 * 24));

		if (isset($day_reference[$day_diff]))
		{
			return $day_reference[$day_diff] . ' ' . date('H:i', $date);
		}
		else
		{
			return date("d-m-Y H:i", $date);
		}
	}
}
