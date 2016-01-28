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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

# Stap 1: Create element order

DROP TABLE IF EXISTS `element_x_page`;
CREATE TABLE `element_x_page` (
  `element_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `is_visible` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'yes',
  PRIMARY KEY (`element_id`,`page_id`),
  KEY `order` (`order`),
  KEY `is_visible` (`is_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Stap 2: convert oude orders naar nieuwe order tabel

INSERT INTO element_x_page (element_id, page_id, `order`, is_visible) SELECT id, page_id, `order`, is_visible FROM element;

# Stap 3: edit element tabel: remove order en is_visible

ALTER TABLE `element` DROP `order`;
ALTER TABLE `element` DROP `is_visible`;
ALTER TABLE `element` DROP `page_id`;

# App settings upgrade - value big
ALTER TABLE `app_settings` ADD `value_big` text COLLATE 'utf8_unicode_ci' NOT NULL;
ALTER TABLE `app_settings` CHANGE `value` `value` varchar(255) COLLATE 'utf8_unicode_ci' NULL AFTER `key`, CHANGE `value_big` `value_big` text COLLATE 'utf8_unicode_ci' NULL AFTER `value`;

# Forgot password CMS
ALTER TABLE `user` ADD `admin_forgot_password_code` varchar(100) COLLATE 'utf8_unicode_ci' NULL;
ALTER TABLE `user` ADD INDEX `admin_forgot_password_code` (`admin_forgot_password_code`);
