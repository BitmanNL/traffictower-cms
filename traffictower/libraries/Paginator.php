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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Paginator
{

	protected $current_page;
	protected $total_items;

	protected $config = array(
		'items_per_page' => 10,					// Number of items per page
		'context' => 1,							// Number of page links besides the active page
		'previous' => '<',						// HTML for previous button
		'next' => '>',							// HTML for next button
		'first' => '<<',						// HTML for first button
		'last' => '>>',							// HTML for last button
		'divider' => '...',						// HTML for divider between the numbers
		'numbers' => TRUE,						// Show numbers
		'first_last' => TRUE,					// Show first and last buttons
		'previous_next' => TRUE,				// Show previous and next buttons
		'divide_from_page_count' => NULL,		// From which count of pages the divider must be used. NULL|int
		'arrow_behaviour' => 'link',			// Behaviour of next/last/etc: link|hide|nolink
		'page_query_var' => 'page',				// Page variable for query string
		'url' => NULL 							// URL to add to url page
	);

	public function get($current_page, $total_items, $config = array())
	{
		// Merge custom configuration with default configuration
		$this->set_config($current_page, $total_items, $config);

		// Build paginator array
		$paginator = array();

		if ($this->total_items > $this->config['items_per_page'])
		{
			// Add all links to the paginator
			$paginator = $this->first($paginator);
			$paginator = $this->previous($paginator);

			$paginator = $this->numbers($paginator);

			$paginator = $this->next($paginator);
			$paginator = $this->last($paginator);
		}

		return $paginator;
	}

	protected function set_config($current_page, $total_items, $config = array())
	{
		$this->current_page = max(1, intval($current_page));
		$this->total_items = max(1, intval($total_items));
		$this->config = array_merge($this->config, $config);
	}

	protected function last_page()
	{
		return ceil($this->total_items / $this->config['items_per_page']);
	}

	protected function first($paginator)
	{
		if ($this->config['first_last'] && ($this->current_page != 1 || $this->config['arrow_behaviour'] != 'hide'))
		{
			$page = ($this->current_page == 1 && $this->config['arrow_behaviour'] == 'nolink') ? NULL : 1;
			$paginator[] = $this->create_link($this->config['first'], $page, FALSE);
		}
		return $paginator;
	}

	protected function last($paginator)
	{
		if ($this->config['first_last'] && ($this->current_page != $this->last_page() || $this->config['arrow_behaviour'] != 'hide'))
		{
			$page = ($this->current_page == $this->last_page() && $this->config['arrow_behaviour'] == 'nolink') ? NULL : $this->last_page();
			$paginator[] = $this->create_link($this->config['last'], $page, FALSE);
		}
		return $paginator;
	}

	protected function previous($paginator)
	{
		if ($this->config['previous_next'] && ($this->current_page != 1 || $this->config['arrow_behaviour'] != 'hide'))
		{
			$page = ($this->current_page == 1 && $this->config['arrow_behaviour'] == 'nolink') ? NULL : max(1, $this->current_page-1);
			$paginator[] = $this->create_link($this->config['previous'], $page, FALSE);
		}
		return $paginator;
	}

	protected function next($paginator)
	{
		if ($this->config['previous_next'] && ($this->current_page != $this->last_page() || $this->config['arrow_behaviour'] != 'hide'))
		{
			$page = ($this->current_page == $this->last_page() && $this->config['arrow_behaviour'] == 'nolink') ? NULL : min($this->last_page(), $this->current_page+1);
			$paginator[] = $this->create_link($this->config['next'], $page, FALSE);
		}
		return $paginator;
	}

	protected function numbers($paginator)
	{
		if ($this->config['numbers'])
		{
			$number_of_pages = $this->last_page();

			if ($number_of_pages >= intval($this->config['divide_from_page_count']))
			{
				// get anchor numbers
				$numbers = array();
				for ($i = 1; $i <= ($this->config['context'] + 1); $i++)
				{
					$numbers[] = intval($i);
				}
				for ($i = ($this->current_page - $this->config['context']); $i <= ($this->current_page + $this->config['context']); $i++)
				{
					$numbers[] = intval(min($number_of_pages, max(1, $i)));
				}
				for ($i = ($number_of_pages - $this->config['context']); $i <= $number_of_pages; $i++)
				{
					$numbers[] = intval($i);
				}
				$numbers = array_unique($numbers);

				// add links and dividers between
				$mem_number = 0;
				foreach ($numbers as $number)
				{
					if (($number - 1) !== $mem_number) {
						$paginator[] = $this->create_link($this->config['divider'], NULL, FALSE);
					}

					$paginator[] = $this->create_link($number, $number, ($number == $this->current_page) ? TRUE : FALSE);
					$mem_number = $number;
				}
			}
			else
			{
				// normal
				for ($i = 1; $i <= $number_of_pages; $i++)
				{
					$paginator[] = $this->create_link($i, $i, ($i == $this->current_page) ? TRUE : FALSE);
				}
			}
		}

		return $paginator;
	}

	protected function create_link($label, $page, $current)
	{
		if (!is_null($page))
		{
			// get url
			if (!is_null($this->config['url']))
			{
				$query_str = '?' . $this->config['page_query_var'] . '=' . $page;

				// get vars
				$url_hash_expl = explode('#', $this->config['url']);
				$url_expl = explode('?', $url_hash_expl[0]);
				if (isset($url_expl[1]))
				{
					parse_str($url_expl[1], $parse_str);
					unset($parse_str[$this->config['page_query_var']]);

					// build
					if (!empty($parse_str))
					{
						$query_str .= '&' . http_build_query($parse_str);
					}
				}
				$url = $url_expl[0] . $query_str;

				// hash
				if (isset($url_hash_expl[1]))
				{
					$url .= '#' . $url_hash_expl[1];
				}
			}
			else
			{
				$url = $this->config['page_query_var'] . '=' . $page;
			}
		}
		else
		{
			$url = NULL;
		}

		$link = array(
			'label' => strval($label),
			'url' => $url,
			'current' => $current
		);

		return $link;
	}

}

/* End of file Paginator.php */
/* Location: ./application/libraries/Paginator.php */
