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
 * @author    Daan Porru
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2014-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if(!function_exists('app_version'))
{
    /**
     * Get the application version.
     *
     * Retrieve the application version string from version.txt. If it's empty
     * or version "1.0.0" the version will be "development". An optional boolean
     * argument may prepend a "v" to the version for printing. If version.txt
     * does not exist or cannot be read the version will be an empty string.
     *
     * @param bool $as_text Prepend the application version string with a "v".
     *
     * @return string The application version.
     */
	function app_version($as_text = FALSE)
	{
        $version_file = APPPATH . 'version.txt';

        $handle = fopen($version_file, 'r');

        if ($handle === false) {
            return '';
        }

        $version = trim(
            str_replace(
                'version',
                '',
                strtolower(fread($handle, filesize($version_file)))
            )
        );

        fclose($handle);

		if (empty($version) || $version == '1.0.0')
		{
			$version = 'development';
		}
		else if ($as_text)
		{
			$version = 'v' . $version;
		}

		return $version;
	}
}

/**
 * Get app setting by key helper.
 *
 * @param string $key App setting key
 * @return mixed App setting
 */
if(!function_exists('app_settings_get'))
{
	function app_settings_get($key = '')
	{
		$ci =& get_instance();
		$ci->load->model('shared/app_settings_model');
		return $ci->app_settings_model->get($key);
	}
}

/**
 * Get config set for TinyMCE.
 * @param string $set Config set
 * @return json TinyMCE config
 */
if(!function_exists('tinymce_config'))
{
	function tinymce_config($set)
	{
		$CI =& get_instance();
		$CI->config->load('tinymce');
		$config = $CI->config->item('tinymce_'.$set);

		// convert all special config items
		if (isset($config['plugins']) && count($config['plugins']) > 1)
		{
			$config['plugins'] = array(implode(' ', $config['plugins']));
		}

		if (isset($config['toolbar']) && count($config['toolbar']) > 1)
		{
			$config['toolbar'] = implode(' ', $config['toolbar']);
		}

		if (isset($config['toolbar1']) && count($config['toolbar1']) > 1)
		{
			$config['toolbar1'] = implode(' ', $config['toolbar1']);
		}

		if (isset($config['toolbar2']) && count($config['toolbar2']) > 1)
		{
			$config['toolbar2'] = implode(' ', $config['toolbar2']);
		}

        // Remove double quotes from the value to prevent the file browser
        // callback from being interpreted as a string.
        $json = json_encode((object)$config);

        $callbackOption = 'file_browser_callback';

        if (
            isset($config[$callbackOption]) &&
            !empty($config[$callbackOption]) &&
            is_string($config[$callbackOption])
        ) {
            $callbackPosition = strpos($json, $callbackOption);

            $startQuotePosition = strpos(
                $json,
                '"',
                $callbackPosition + strlen($callbackOption) + 1
            );

            $endQuotePosition = strpos($json, '"', $startQuotePosition + 1);

            $json = substr($json, 0, $startQuotePosition) .
                substr(
                    $json,
                    $startQuotePosition + 1,
                    ($endQuotePosition - $startQuotePosition) - 1
                ) .
                substr($json, $endQuotePosition + 1);
        }

        return $json;
	}
}

/**
 * Check if user is in preview mode and is allowed to (administrator user).
 *
 * @return mixed User is in preview mode
 */
if(!function_exists('app_preview_mode'))
{
	function app_preview_mode()
	{
		$CI =& get_instance();

		if ($CI->input->get('preview') === 'true')
		{
			// Check login admin
			$CI->load->library('auth');
			if ($CI->auth->check_login() && $CI->auth->auth_user_groups('administrator'))
			{
				return TRUE;
			}
		}

		return FALSE;
	}
}
