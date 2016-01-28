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
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * do_not_track
 *
 * Tells you whether or not you can use third-party trackers (Google Analytics, Facebook Like Buttons, etc.)
 *
 * @access public
 * @return boolean TRUE = do not track / FALSE = trackers allowed
 */
if(!function_exists('do_not_track'))
{
	function do_not_track()
	{
		$CI =& get_instance();

		$show_3rd_party = FALSE;
		$hide_3rd_party = TRUE;

		if($CI->config->item('do_not_track_enabled') === TRUE && $CI->input->server('HTTP_DNT') === '1')
		{
			// do not track enabled in CMS by config AND user browser settings do not track is set
			return $hide_3rd_party;
		}
		else if ($CI->config->item('do_not_track_enabled') === TRUE && $CI->input->server('HTTP_DNT') === '0')
		{
			// do not track enabled in CMS by config AND user browser settings do not track is set to "do track me"
			return $show_3rd_party;
		}
		else if ($CI->config->item('cookielaw_enabled') === FALSE || $CI->input->cookie('cms_cookielaw_accept') === 'yes' || $CI->input->cookie('cms_cookielaw_accept') === FALSE)
		{
			// cookie bar is disabled in CMS by config OR 3rd party cookies are accepted by user
			return $show_3rd_party;
		}
		else
		{
			// cookie bar is enabled AND (user has not decided yet OR user doesn't allow cookies)
			return $hide_3rd_party;
		}
	}
}
