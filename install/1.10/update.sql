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
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

# Lost type field re-add to element text
ALTER TABLE `element_text` ADD `type` varchar(100) COLLATE 'utf8_unicode_ci' NULL AFTER `content`;
ALTER TABLE `element_text` ADD INDEX `type` (`type`);

# Add user id aan page
ALTER TABLE `page` ADD `user_id` int(11) NOT NULL AFTER `parent_id`;
ALTER TABLE `page` ADD INDEX `user_id` (`user_id`);

# Add thumbnail add video
ALTER TABLE `element_video` ADD `thumbnail` varchar(255) COLLATE 'utf8_unicode_ci' NOT NULL AFTER `title`;
ALTER TABLE `element_video` ADD INDEX `thumbnail` (`thumbnail`);
