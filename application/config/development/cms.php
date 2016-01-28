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
 * @package   CMS\Config
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// disable firephp log by default
$config['cms_firephp_enabled'] = TRUE;

// do not track
$config['do_not_track_enabled'] = TRUE;

// show cookielaw banner
$config['cookielaw_enabled'] = FALSE;

// asset version no
$config['asset_version'] = '1';

// custom headers
$config['headers'] = array(
	// set headers to no-sniff. IE likes this
	'X-Content-Type-Options: nosniff',

	// don't allow iframing by default (http://blogs.msdn.com/b/ieinternals/archive/2010/03/30/combating-clickjacking-with-x-frame-options.aspx?Redirected=true)
	'X-Frame-Options: DENY',

	// Get last IE engine and load Google Chrome frame plugin (for HTML5)
	'X-UA-Compatible: IE=edge,chrome=1',

	// Content encoding in de header meesturen
	'Content-Type: text/html; charset=utf-8'
);
