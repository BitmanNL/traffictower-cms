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
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

DROP TABLE IF EXISTS `app_settings`;
CREATE TABLE `app_settings` (
  `key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `value_big` text COLLATE utf8_unicode_ci,
  UNIQUE KEY `key` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `element`;
CREATE TABLE `element` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `position` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `content_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `position` (`position`),
  KEY `widget` (`type`),
  KEY `content_id` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

DROP TABLE IF EXISTS `element_image`;
CREATE TABLE `element_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `alt` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `element_text`;
CREATE TABLE `element_text` (
  `revision_id` int(11) NOT NULL AUTO_INCREMENT,
  `id` int(11) NOT NULL,
  `status` enum('concept','published','revision') COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`revision_id`),
  KEY `id` (`id`),
  KEY `status` (`status`),
  KEY `type` (`type`),
  FULLTEXT KEY `title_content` (`title`,`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `element_video`;
CREATE TABLE `element_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `autoplay` int(11) NOT NULL,
  `format_type` enum('absolute','relative') COLLATE utf8_unicode_ci NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `thumbnail` (`thumbnail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ip_hash` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `date_created` (`date_created`),
  KEY `ip_hash` (`ip_hash`),
  KEY `action` (`action`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_visible` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL,
  `is_system_page` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `in_menu` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `controller` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `controller_params` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `layout` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'default',
  `language` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'nl',
  `relative_page_id` int(11) NOT NULL DEFAULT '0',
  `secondary_navigation` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `module` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `replace_by` enum('internal','external','first_sub') COLLATE utf8_unicode_ci DEFAULT NULL,
  `replace_value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `layout` (`layout`),
  KEY `class` (`controller`),
  KEY `parent_id` (`parent_id`),
  KEY `slug` (`slug`),
  KEY `language` (`language`),
  KEY `relative_page_id` (`relative_page_id`),
  KEY `secondary_navigation` (`secondary_navigation`),
  KEY `module` (`module`),
  KEY `is_visible` (`is_visible`),
  KEY `in_menu` (`in_menu`),
  KEY `order` (`order`),
  KEY `user_id` (`user_id`),
  KEY `controller_params` (`controller_params`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `session_id` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY `session_id_unique` (`session_id`),
  KEY `session_id` (`session_id`),
  KEY `created_at` (`created_at`),
  KEY `updated_at` (`updated_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_super_user` enum('no','yes') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `is_active` enum('no','yes') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `screen_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `admin_forgot_password_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `is_super_user` (`is_super_user`),
  KEY `is_active` (`is_active`),
  KEY `admin_forgot_password_code` (`admin_forgot_password_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user_group`;
CREATE TABLE `user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `user_x_user_group`;
CREATE TABLE `user_x_user_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `user_group_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `user_group_id` (`user_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

# INSERT STATEMENTS

TRUNCATE `user_group`;
INSERT INTO `user_group` (`id`, `key`, `name`) VALUES (1, 'administrator',  'Administrator');

INSERT INTO `page` (`id`, `is_visible`, `in_menu`, `title`, `date_created`, `language`, `layout`) VALUES (1, 'yes', 'yes', 'Home', NOW(),  'nl', 'home');

INSERT INTO `element_text` (`revision_id`, `id`, `status`, `user_id`, `title`, `content`, `date_created`) VALUES
(1, 1, 'published', 1, 'Home', '<p>Praesent in mauris eu tortor porttitor accumsan. Mauris suscipit, ligula sit amet pharetra semper, nibh ante cursus purus, vel sagittis velit mauris vel metus. Aenean fermentum risus id tortor. Integer imperdiet lectus quis justo. Integer tempor. Vivamus ac urna vel leo pretium faucibus. Mauris elementum mauris vitae tortor. In dapibus augue non sapien. Aliquam ante. Curabitur bibendum justo non orci.</p>', NOW()),
(2, 2, 'published', 1, 'element 2', '<p>Nam quis nulla. Integer malesuada. In in enim a arcu imperdiet malesuada. Sed vel lectus. Donec odio urna, tempus molestie, porttitor ut, iaculis quis, sem. Phasellus rhoncus. Aenean id metus id velit ullamcorper pulvinar. Vestibulum fermentum tortor id mi. Pellentesque ipsum. Nulla non arcu lacinia neque faucibus fringilla. Nulla non lectus sed nisl molestie malesuada. Proin in tellus sit amet nibh dignissim sagittis. Vivamus luctus egestas leo. Maecenas sollicitudin. Nullam rhoncus aliquam metus. Etiam egestas wisi a erat.</p>\n<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam feugiat, turpis at pulvinar vulputate, erat libero tristique tellus, nec bibendum odio risus sit amet ante. Aliquam erat volutpat. Nunc auctor. Mauris pretium quam et urna. Fusce nibh. Duis risus. Curabitur sagittis hendrerit ante. Aliquam erat volutpat. Vestibulum erat nulla, ullamcorper nec, rutrum non, nonummy ac, erat. Duis condimentum augue id magna semper rutrum. Nullam justo enim, consectetuer nec, ullamcorper ac, vestibulum in, elit. Proin pede metus, vulputate nec, fermentum fringilla, vehicula vitae, justo. Fusce consectetuer risus a nunc. Aliquam ornare wisi eu metus. Integer pellentesque quam vel velit. Duis pulvinar.</p>', NOW());

INSERT INTO `element` (`id`, `position`, `type`, `content_id`) VALUES (1, 'content',  'text', 1), (2, 'content',  'text', 2);
INSERT INTO `element_x_page` (`element_id`, `page_id`, `order`, `is_visible`) VALUES (1, 1, 0, 'yes'), (2, 1, 1, 'yes');

TRUNCATE `app_settings`;
INSERT INTO `app_settings` (`key`, `value`) VALUES
('site_name', 'Bitman'),
('description', 'Een TrafficTower CMS website, binnenkort in uw huiskamer!'),
('url', 'http://www.bitman.nl'),
('email', 'info@bitman.nl'),
('image', ''),
('apple_touch_icon',  '');
