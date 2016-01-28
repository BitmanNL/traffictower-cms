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
 * @author    Daan Porru
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2014-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Extends CMS_controller, used for Frontend_controller.
 * Adds functionality to show view, css and javascript with different layout files.
 */
class Frontend_base_controller extends CMS_controller
{

	/**
	 * @var mixed[] $page Holds the information about the current page.
	 */
	protected $page = array();

	/**
	 * @var boolean $show_elements Render elements on layout TRUE/FALSE.
	 */
	protected $show_elements = TRUE;

	/**
	 * Contructor. Loads all languages and trigger event pre_controller.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->event->trigger('pre_controller');

		$this->_cms_language();
	}

	/**
	 * Generate page. Put site specific logic for all pages here.
	 *
	 * @param array $page Optional page
	 */
	protected function _layout($page = NULL)
	{
		// Backwards compatibility: if page not set by input, then use global this page
		$page = is_null($page) ? $this->page : $page;

		$this->event->trigger('pre_layout');

		if(is_array($page) && !empty($page))
		{
			// PUT HERE YOUR SITE LOGIC FOR ALL PAGES


			// execute rendering
			$this->_render_page($page);
		}
	}

	/**
	 * Load languages.
	 */
	protected function _cms_language()
	{
		$language = $this->config->item('language');
		$languages = $this->config->item('languages');
		$domain_language = array_search($this->input->server('HTTP_HOST'), $this->config->item('language_domain_mapping'));

		$this->original_index_page = $this->config->item('index_page');

		// if not default language, extend index_page with the language
		if (($domain_language === FALSE && $languages[0] !== $language) || ($domain_language !== FALSE && $domain_language !== $language))
		{
			if ($this->config->item('index_page') !== '' && substr($this->config->item('index_page'), -1) !== '/')
			{
				$new_index_page = $this->config->item('index_page') . '/' . $language;
			}
			else
			{
				$new_index_page = $language;
			}

			$this->config->set_item('index_page', $new_index_page);
		}

		$language_data = $this->config->item('language_data');

		// set locales
		if (isset($language_data[$language]) && isset($language_data[$language]['locale']))
		{
			$locale = $language_data[$language]['locale'];
			$this->db->query("SET lc_time_names = '{$locale}'");
		}
		else
		{
			show_error("No locale set for language '" . $language . "'");
		}
	}

	/**
	 * Get Page By Controller. Get the page information for the current controller.
	 *
	 * @param string $class Controller name
	 * @param string $method Method name
	 * @param array $params Optional array of extra controller parameters
	 * @return array Page
	 */
	public function get_page_by_controller($class = NULL, $method = NULL, array $params = array())
	{
		$this->load->model('page_model');

		$page = $this->page_model->get_page_by_controller($this->config->item('language'), app_preview_mode(), $class, $method, $params);

		// page does not exist
		if(!is_array($page) || !isset($page['id']))
		{
			// no controller page found, so get the page instead
			$page = $this->get_home();
		}

		$this->_redirect_replace_by($page);

		// Convert query string controller parameters to array
		if (!empty($page) && !is_array($page['controller_params']))
		{
			parse_str($page['controller_params'], $page['controller_params']);
		}

		return $page;
	}

	/**
	 * Get page by given slug.
	 *
	 * @param  string $slug Page slug
	 * @return array Page
	 */
	public function get_page_by_slug($slug)
	{
		$this->load->model('page_model');

		$page = $this->page_model->get_page_by_slug($slug, $this->config->item('language'), app_preview_mode());

		$this->_redirect_replace_by($page);

		// Convert query string controller parameters to array
		if (!empty($page) && !is_array($page['controller_params']))
		{
			parse_str($page['controller_params'], $page['controller_params']);
		}

		return $page;
	}

	/**
	 * Get the home page.
	 *
	 * @return array Page
	 */
	public function get_home()
	{
		$this->load->model('page_model');

		$page = $this->page_model->get_home($this->config->item('language'), app_preview_mode());

		if (!is_array($page) || !isset($page['id']))
		{
			// no page found
			show_error('No home page set. Create at least one page for your site.', 404);
		}

		$this->_redirect_replace_by($page);

		// Convert query string controller parameters to array
		if (!empty($page) && !is_array($page['controller_params']))
		{
			parse_str($page['controller_params'], $page['controller_params']);
		}

		return $page;
	}

	/**
	 * Get the current page.
	 *
	 * @return array Page
	 */
	public function get_page()
	{
		return $this->page;
	}

	/**
	 * Render Page. Uses the information in the $view, $css and $javascript
	 * variables to generate the layout for a page.
	 *
	 * @param array $page Page to render
	 */
	protected function _render_page(array $page)
	{
		// load piwik helper for piwik code in footer
		$this->load->helper('piwik');
		$this->load->helper('google_analytics');
		$this->load->helper('text');
		$this->load->library('Navigation');

		// load pages relative in other languages for current page
		$this->_render_language_relative_pages($page);

		// cookielaw banner
		$this->_render_cookielaw_banner();

		// Render elements for page
		if($this->show_elements)
		{
			$this->views['elements'] = $this->_render_elements($page['id']);
		}

		// handle all assets: css, css files, js and js files
		$this->_render_assets();

		// render meta tags (into $app)
		$this->views['app'] = $this->_render_meta($page);

		// general page data + merge data into views, views get the upperhand in conficts
		$this->data['page'] = $page;
		$this->data['data'] = $this->data;
		$this->views = array_merge($this->data, $this->views);

		// set custom headers
		$this->_render_headers();

		// output the html-body with the views
		$this->load->view('layouts/'.$page['layout'], $this->views);

		$this->event->trigger('post_layout');
	}

	/**
	 * Render meta information.
	 *
	 * @param array $page Current page
	 * @return array List of 'meta' tags (basically $page)
	 */
	protected function _render_meta($page)
	{
		// get app settings (meta ea)
		$this->load->model('shared/app_settings_model');

		// merge with page values
		if(empty($page['description']))
		{
			unset($page['description']);
		}

		$meta = array_merge($this->app_settings_model->get_app_settings(), $page);

		// url encode image (for facebook caching ea)
		if (isset($meta['image']) && !empty($meta['image']))
		{
			preg_match('/(.*\/)(.*)(\..*)/', $meta['image'], $results);

			if (!empty($results))
			{
				$meta['image'] = $results[1] . urlencode($results[2]) . $results[3];
			}
		}

		return $meta;
	}

	/**
	 * Handle all assets: css, css files, js and js files.
	 */
	protected function _render_assets()
	{
		// Load defaults
		$this->data['javascript_params'] = $this->get_javascript_param(NULL, TRUE);

		// insert the css files (if any)
		if (is_array($this->css_files))
		{
			$this->data['css_files'] = array_unique($this->css_files);
		}

		// gather the css (if any) to insert
		$this->data['css'] = '';
		if (is_array($this->css) && !empty($this->css))
		{
			foreach ($this->css as $css)
			{
				$this->data['css'] .= $css;
			}
		}

        $this->data['preloaded_javascript_files'] = array_unique(
            $this->preloaded_javascript_files
        );

		// insert the javascript files (if any)
		if (is_array($this->javascript_files))
		{
			$this->data['javascript_files'] = array_unique($this->javascript_files);
		}

		// output the html-footer including javascript (if any)
		$this->data['javascript'] = '';
		if (is_array($this->javascript) && !empty($this->javascript))
		{
			foreach ($this->javascript as $javascript)
			{
				$this->data['javascript'] .= $javascript;
			}
		}
	}

	/**
	 * Set custom headers, loaded from cms-config.
	 */
	protected function _render_headers()
	{
		$headers = $this->config->item('headers');
		foreach ($headers as $header)
		{
			$this->output->set_header($header);
		}
	}

	/**
	 * Set cookielaw banner if set in cms-config.
	 */
	protected function _render_cookielaw_banner()
	{
		if ($this->config->item('cookielaw_enabled') && !($this->config->item('do_not_track_enabled') === TRUE && $this->input->server('HTTP_DNT') !== FALSE))
		{
			if ($this->input->cookie('cms_cookielaw_accept') === FALSE)
			{
				$this->load->library('session');

				$this->lang->load('cookielaw');
				$this->views['cookielaw_banner'] = $this->load->view('page/cookielaw/banner', NULL, TRUE);
				$this->css[] = $this->load->view('page/cookielaw/css/banner.css', NULL, TRUE);
				$this->javascript[] = $this->load->view('page/cookielaw/js/banner.js', NULL, TRUE);

				$this->session->set_flashdata('cookielaw_redirect_url', full_current_url());
			}
		}
	}

	/**
	 * Load pages relative in other languages for current page.
	 *
	 * @param array $page Current page
	 */
	protected function _render_language_relative_pages($page)
	{
		$this->data['language_relative_pages'] = $this->page_model->get_relative_pages($page);
		$this->data['languages'] = $this->config->item('languages');
		$this->data['language_data'] = $this->config->item('language_data');
		$domain_language = array_search($this->input->server('HTTP_HOST'), $this->config->item('language_domain_mapping'));

		foreach ($this->data['languages'] as $language)
		{
			if (($domain_language === FALSE && $this->data['languages'][0] !== $language) || ($domain_language !== FALSE && $domain_language !== $language))
			{
				$language_uri = $language;
			}
			else
			{
				$language_uri = '';
			}

			if (!isset($this->data['language_relative_pages'][$language]))
			{
				$this->data['language_relative_pages'][$language] = $language_uri . '';
			}
			else
			{
				$this->data['language_relative_pages'][$language] = $language_uri . '/' . $this->data['language_relative_pages'][$language];
			}
		}

		// Create link hreflang rel alternates
		$this->_render_language_alternates($this->data['language_relative_pages']);
	}

	/**
	 * Construct language hreflang alternates (<link rel="alternate"...>).
	 *
	 * @param array $language_relative_pages Language relative pages
	 */
	protected function _render_language_alternates($language_relative_pages)
	{
		$languages = $this->config->item('languages');
		$language_data = $this->config->item('language_data');

		$data['alternate_default_href'] = '';
		$data['alternates'] = array();

		// Get alternates
		foreach ($languages as $language)
		{
			// Alternate only when its not empty nor only language code
			if ($language_relative_pages[$language] != $language && !empty($language_relative_pages[$language]))
			{
				$data['alternates'][] = array(
					'href' => $language_relative_pages[$language],
					'hreflang' => $language_data[$language]['code']
				);
			}
		}

		// Get x-default hreflang
		if (!empty($data['alternates']))
		{
			if (isset($language_relative_pages[$languages[0]]) && !empty($language_relative_pages[$languages[0]]))
			{
				// get href from first languages (default)
				$data['alternate_default_href'] = $language_relative_pages[$languages[0]];
			}
			else
			{
				// get current hreflang
				$this->firephp->log('current needed?');
				$data['alternate_default_href'] = $language_relative_pages[$this->config->item('language')];
			}
		}

		$this->views['language_alternates'] = $this->load->view('layouts/includes/header_language_alternates', $data, TRUE);
	}

	/**
	 * Render element content for a specific page.
	 *
	 * @param integer $page_id Page id to render elements for
	 * @return array List of element content per position
	 */
	protected function _render_elements($page_id)
	{
		$this->load->library('Element');
		return $this->element->genenerate_elements($page_id);
	}

	/**
	 * Redirect() page if set field replace_by in database.
	 *
	 * @param array $page Page to check for redirect
	 */
	protected function _redirect_replace_by($page)
	{
		if (!empty($page))
		{
			if ($page['replace_by'] == 'external')
			{
				if (substr($page['replace_value'], 0, 4) == 'http' || substr($page['replace_value'], 0, 2) == '//')
				{
					redirect($page['replace_value']);
				}
				else
				{
					redirect(site_url($page['replace_value']));
				}
			}
			else
			{
				$replace_by_page = array();
				if (!empty($page['replace_by']))
				{
					$this->load->model('page_model');

					if ($page['replace_by'] == 'internal' && is_numeric($page['replace_value']))
					{
						$replace_by_page = $this->page_model->get_page_by_id($page['replace_value']);
					}
					if ($page['replace_by'] == 'first_sub')
					{
						// get first sub
						$secondary_navigation = !empty($page['secondary_navigation']) ? $page['secondary_navigation'] : NULL;
						$sub_navigation = $this->page_model->get_navigation_by_parent_id($page['id'], $this->config->item('language'), $secondary_navigation, app_preview_mode());
						if (!empty($sub_navigation))
						{
							$replace_by_page = current($sub_navigation);
						}
					}
				}

				if (!empty($replace_by_page))
				{
					redirect(site_url($replace_by_page['slug']));
				}
			}
		}
	}

}

/* End of file Frontend_base_controller.php */
/* Location: ./application/core/Frontend_base_controller.php */
