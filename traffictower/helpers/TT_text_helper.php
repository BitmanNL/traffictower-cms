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

if(!function_exists('html_to_text'))
{
	function html_to_text($html)
	{
		$tekst = trim($html);

		// Case-insensitive replace

		$tekst = str_ireplace("</p>", "\n", $tekst);
		$tekst = str_ireplace("<br>", "\n", $tekst);
		$tekst = str_ireplace("<br/>", "\n", $tekst);
		$tekst = str_ireplace("<br />", "\n", $tekst);

		// Strip all tags except <a>

		$tekst = strip_tags($tekst,'<a>');

		// Replace a-tags with their href attribute

		$tekst = preg_replace('/<a(.*)href="([^"]*)"(.*)>(.*)<\/a>/','$2',$tekst);

		// Safe trimming, instead of trim() function

		$tekst = str_replace("\t", "", $tekst);
		$tekst = str_replace("\0", "", $tekst);
		$tekst = str_replace("\x0B", "", $tekst);

		$tekst = html_entity_decode($tekst, ENT_NOQUOTES, 'UTF-8');

		$tekst = str_replace("\r", "", $tekst);
		$tekst = str_replace("\n", "", $tekst);

		return($tekst);
	}
}

if(!function_exists('text_excerpt_by_needle'))
{
	function text_excerpt_by_needle($text, $needle, $excerpt_length = 200, $end_character = '&#8230;')
	{
		$excerpt = '';

		$text = strip_tags($text);
		$needle_pos = !empty($needle) ? intval(strpos($text, $needle)) : 0;

		$start_pos = $needle_pos - intval($excerpt_length);
		$end_pos = ($excerpt_length * 2) + strlen($needle);
		if($start_pos < 0)
		{
			$start_pos = 0;
		}
		else
		{
			$excerpt .= $end_character;
		}

		$excerpt .= substr($text, $start_pos, $end_pos);

		if($end_pos < strlen($text))
		{
			$excerpt .= $end_character;
		}

		return $excerpt;
	}
}

if(!function_exists('meta_description'))
{
	function meta_description($html, $character_count = 200)
	{
		return character_limiter(html_to_text($html), $character_count, '...');
	}
}
