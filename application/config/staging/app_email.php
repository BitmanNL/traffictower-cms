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
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *
 * App email default configuration.
 * For more information see: http://ellislab.com/codeigniter/user-guide/libraries/email.html
 *
 */


/**
 * Mailtype: html or text
 */
$config['mailtype'] = 'html';


/**
 * Email protocol, mail, sendmail or smtp.
 */
$config['protocol'] = 'mail';


/**
 * SMTP-settings (used if protocol is set to 'smtp').
 */

//$config['smtp_host'] = '';
//$config['smtp_user'] = '';
//$config['smtp_pass'] = '';
//$config['smtp_port'] = '';


/**
 * Custom "user agent"
 */
$config['useragent'] = 'TrafficTower CMS';

/**
 * Default E-mail template (No CI functionality).
 */
$config['email_template'] = 'default';

/**
 * Add site name as prefix to subject.
 */
$config['subject_prefix_site_name'] = TRUE;
