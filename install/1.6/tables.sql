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

ALTER TABLE `element`
ADD INDEX `order` (`order`),
ADD INDEX `is_visible` (`is_visible`);

ALTER TABLE `element_text`
ADD INDEX `id` (`id`),
ADD INDEX `status` (`status`);

ALTER TABLE `help_page`
ADD INDEX `parent_id` (`parent_id`),
ADD INDEX `module` (`module`),
ADD INDEX `controller` (`controller`),
ADD INDEX `is_visible` (`is_visible`);

ALTER TABLE `help_paragraph`
ADD INDEX `page_id` (`page_id`),
ADD INDEX `order` (`order`),
ADD INDEX `key` (`key`),
ADD INDEX `is_visible` (`is_visible`);

ALTER TABLE `page`
ADD INDEX `is_visible` (`is_visible`),
ADD INDEX `in_menu` (`in_menu`),
ADD INDEX `order` (`order`);

ALTER TABLE `user`
ADD INDEX `email` (`email`),
ADD INDEX `is_super_user` (`is_super_user`),
ADD INDEX `is_active` (`is_active`),
ADD INDEX `sso_id` (`sso_id`);

ALTER TABLE `user_x_user_group`
ADD INDEX `user_id` (`user_id`),
ADD INDEX `user_group_id` (`user_group_id`);

ALTER TABLE `page`
ADD `replace_by` enum('internal','external','first_sub') COLLATE 'utf8_unicode_ci' NULL,
ADD `replace_value` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL AFTER `replace_by`,
COMMENT='';

ALTER TABLE `element_text`
ADD `type` varchar(100) COLLATE 'utf8_unicode_ci' NULL AFTER `content`,
COMMENT='';

ALTER TABLE `element_text`
ADD INDEX `type` (`type`);

ALTER TABLE `user`
DROP `sso_provider`,
DROP `sso_id`,
COMMENT='';
