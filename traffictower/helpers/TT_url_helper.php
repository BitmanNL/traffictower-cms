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
 * Reverse routing url creator.
 * Compares routed url with routing rule and replaces url with rule.
 *
 * @param string $url Routed url
 * @param boolean $add_site_url Optional whether to add site_url() around url
 * @return string Routing rule url
 */
if(!function_exists('route_url'))
{
	function route_url($url, $add_site_url = TRUE)
	{
		$CI =& get_instance();

		$parse_url = parse_url($url);
		$url = $parse_url['path'];
		$url_query = (isset($parse_url['query']) && !empty($parse_url['query'])) ? '?' . $parse_url['query'] : '';
		$url_hash = (isset($parse_url['fragment']) && !empty($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '';

		$url_exp = explode('/', trim($url, '/'));

		$routes = $CI->router->routes;
		foreach ($routes as $route => $route_url)
		{
			$route_url_exp = explode('/', trim($route_url, '/'));

			$is_route = TRUE;
			$replacements = array();
			if (count($route_url_exp) === count($url_exp))
			{
				foreach ($route_url_exp as $k => $route_pt)
				{
					if (substr($route_pt, 0, 1) !== '$' && $route_pt !== $url_exp[$k]) {
						$is_route = FALSE;
					}

					if (substr($route_pt, 0, 1) === '$') {
						$replacements[] = $url_exp[$k];
					}
				}
			}
			else
			{
				$is_route = FALSE;
			}

			if ($is_route)
			{
				$i = 0;
				$new_url = array();
				$new_url_exp = explode('/', trim($route, '/'));
				foreach ($new_url_exp as $new_url_pt)
				{
					if (substr($new_url_pt, 0, 1) === '(') {
						$new_url[] = $replacements[$i];
						$i++;
					}
					else {
						$new_url[] = $new_url_pt;
					}

				}

				$url = implode('/', $new_url);
			}
		}

		$url .= $url_query . $url_hash;

		if ($add_site_url)
		{
			$url = site_url($url);
		}

		return $url;
	}
}

/**
 * Original Site Url
 *
 * Gives the functionality of site_url, without the language string implanted
 */
if(!function_exists('original_site_url'))
{
	function original_site_url($uri = '')
	{
		$CI =& get_instance();

		if (!isset($CI->original_index_page))
		{
			return site_url($uri);
		}

		$new_index_page = $CI->config->item('index_page');

		// set index_page back to it's original value
		$CI->config->set_item('index_page', $CI->original_index_page);

		$uri = site_url($uri);

		// restore proevious sitiuation
		$CI->config->set_item('index_page', $new_index_page);

		return $uri;
	}
}


/**
 * Full current Url
 *
 * Gives the url of the incoming request, including query variables
 *
 * @param mixed[] $query_vars Replacement query vars (optional)
 *
 * @return String the current page url
 */
if(!function_exists('full_current_url'))
{
	function full_current_url($query_vars = NULL)
	{
		$CI =& get_instance();

		if (is_null($query_vars) OR !is_array($query_vars))
		{
			$query_vars = $CI->input->get();
		}

		if(!empty($query_vars))
		{
			$query_var_string = '?'. http_build_query($query_vars);
		}
		else
		{
			$query_var_string = '';
		}

		$current_url = $CI->uri->uri_string . $query_var_string;

		return site_url($current_url);
	}
}

/**
 * Create slug from string, where special characters will be replaced by its equivalent normal character (e umlaut to e).
 *
 * @param  string  $title     Title
 * @param  string  $separator Space separator
 * @param  boolean $lowercase Slug in lowercase TRUE
 * @return string             Slug
 */
if(!function_exists('slug'))
{
	function slug($title, $separator = '-', $lowercase = TRUE)
	{
		$special_characters = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ');
		$normal_characters = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','ss','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');

		$title = str_replace($special_characters, $normal_characters, $title);

		return url_title($title, $separator, $lowercase);
	}
}

/**
 * Add optional version number to file if local + base_url
 * @param  string $url URL strinng
 * @return string URL with optional asset version number
 */
if(!function_exists('asset_url'))
{
	function asset_url($url)
	{
		// check if it is a local file
		if (substr($url, 0, 4) !== 'http' && substr($url, 0, 2) !== '//')
		{
			$ci =& get_instance();
			$ci->config->load('cms', TRUE);
			$url .= '?v='.$ci->config->item('asset_version');

			// add assets/ if not present already
			$exploded_url = explode('/', ltrim($url, '/'));
			if ($exploded_url[0] !== 'assets')
			{
				$url = 'assets/' . ltrim($url, '/');
			}
		}

		return base_url($url);
	}
}

if(!function_exists('less_url'))
{
	function less_url($less_url)
	{
		if (!file_exists(FCPATH . $less_url) ||	!is_file(FCPATH . $less_url))
		{
			show_error('LESS asset invalid: <code>' . $less_url . '</code>');
		}

		$ci =& get_instance();
		$ci->load->library('less_Parser');

		$url = str_replace(array('.less', 'less'), array('.min.css', 'css'), $less_url);

		if (ENVIRONMENT === 'development')
		{
			try
			{
				$ci->less_parser->reset();
				$ci->less_parser->setOption('compress', TRUE);
				$less_parser = $ci->less_parser->parseFile(FCPATH . $less_url);
				$less_css = $less_parser->getCss();
			}
			catch (Exception $error)
			{
				show_error('Fout bij compileren LESS file:<code>' . $less_url . '</code><strong>Error message:</strong><code>' . $error->getMessage() . '</code>');
			}

			file_put_contents(FCPATH . 'assets/css/' . basename($url), $less_css);
		}

		return asset_url($url);
	}
}
