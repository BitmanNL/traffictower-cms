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
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

UPDATE page SET language = 'nl' WHERE language = 'dutch';
UPDATE page SET language = 'en' WHERE language = 'english';
UPDATE page SET language = 'de' WHERE language = 'german';

ALTER TABLE `user`
ADD `is_active` enum('no','yes') COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'no',
ADD `screen_name` varchar(100) COLLATE 'utf8_unicode_ci' NOT NULL AFTER `is_active`,
COMMENT='';

UPDATE user SET is_active = 'yes';
UPDATE user SET screen_name = email;

ALTER TABLE `page`
ADD `secondary_navigation` varchar(100) NULL,
COMMENT='';

ALTER TABLE `page`
ADD INDEX `secondary_navigation` (`secondary_navigation`);

CREATE TABLE `log` (
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_hash` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  KEY `date_created` (`date_created`),
  KEY `ip_hash` (`ip_hash`),
  KEY `action` (`action`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `page` CHANGE `language` `language` VARCHAR( 20 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nl'
ALTER TABLE `page` CHANGE `relative_page_id` `relative_page_id` INT( 11 ) NOT NULL DEFAULT '0'
