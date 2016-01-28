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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

ALTER TABLE `page`
ADD `is_system_page` enum('yes','no') COLLATE 'utf8_unicode_ci' NOT NULL DEFAULT 'no' AFTER `is_visible`,
COMMENT='';

# Alle pagina's die een aan een controller gekoppeld zijn worden super_user_restricted.
UPDATE page SET is_system_page='yes' WHERE controller != ''
