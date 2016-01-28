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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('google_analytics_tag'))
{
	function google_analytics_tag()
	{
		$CI =& get_instance();
		$CI->load->config('google_analytics');

		$tracking_id = $CI->config->item('tracking_id');
		$tag_on = $CI->config->item('tag_on');

		if($tag_on)
		{
			$tag = "<script>
	  				(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					ga('create', '" . $tracking_id . "', 'auto');
					ga('send', 'pageview');
					</script>";

			echo stripslashes($tag);
		}
	}
}
