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
 * @package   CMS\Install
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

DROP TABLE IF EXISTS `app_email`;
CREATE TABLE `app_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` text COLLATE utf8_unicode_ci NOT NULL,
  `from_available` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `from_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `from_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to_available` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `to_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `to_email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `template` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `language` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nl',
  PRIMARY KEY (`id`),
  KEY `key` (`key`),
  KEY `from_available` (`from_available`),
  KEY `to_available` (`to_available`),
  KEY `template` (`template`),
  KEY `language` (`language`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
