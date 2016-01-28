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
 * @package   CMS\Core\Admin\FileManager
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Bitman\Cms\FileManager;

/**
 * Remove a directory and all its contents, including subdirectories.
 *
 * @param string $filePath The path to the file or directory to remove.
 *
 * @return void
 */
function rmrdir($filePath)
{
    $objects = scandir($filePath);

    foreach ($objects as $object) {
        if ($object !== '.' && $object !== '..') {
            if (filetype($filePath . '/' . $object) === 'dir') {
                rmrdir($filePath . '/' . $object);
            } else {
                unlink($filePath . '/' . $object);
            }
        }
    }

    rmdir($filePath);
}
