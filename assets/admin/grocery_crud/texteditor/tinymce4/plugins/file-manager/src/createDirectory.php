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
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Bitman\Cms\FileManager;

/**
 * Recursively create a directory if allowed.
 *
 * @param string $path     The path to the directory which will be created.
 * @param int $permissions The Unix permissions to set on the directory. Ignored
 *                         on Windows machines.
 *
 * @return void
 *
 * @throws \LogicException           if the directory already exists.
 * @throws \UnexpectedValueException if the first existing parent directory in
 *                                   the $path argument is not readable or
 *                                   writable by the user running PHP.
 * @throws \UnexpectedValueException when a recursive call to mkdir() with the
 *                                   given $path and $permissions arguments
 *                                   fails.
 */
function createDirectory($path, $permissions = 0755)
{
    if (is_dir($path)) {
        throw new \LogicException(
            'De map "' . $path . '" bestaat al.',
            2
        );
    }

    // Find the first parent directory and check its permissions.
    $permission = false;
    $parentPath = $path;

    do {
        $parentPath = explode(
            DIRECTORY_SEPARATOR,
            trim($parentPath, DIRECTORY_SEPARATOR)
        );

        $parentPathCount = count($parentPath);

        unset($parentPath[--$parentPathCount]);

        $parentPath = implode(DIRECTORY_SEPARATOR, $parentPath);

        // Don't prepend the path with a directory separator on Windows.
        // The drive letter, for example: "C:\", is enough.
        if (PHP_OS !== 'Windows') {
            $parentPath = DIRECTORY_SEPARATOR . $parentPath;
        }

        if (file_exists($parentPath)) {
            $fileInfo = new \SplFileInfo($parentPath);

            if ($fileInfo->isReadable() && $fileInfo->isWritable()) {
                $permission = true;

                break;
            }
        }
    } while ($parentPathCount > 1);

    if ($permission) {
        if (!mkdir($path, $permissions, true)) {
            throw new \UnexpectedValueException(
                'De map "' . $path . '" kon niet aangemaakt worden.',
                8
            );
        }
    } else {
        throw new \UnexpectedValueException(
            'De eerstvolgende bestaande map die boven "' . $path . '" ligt ' .
            'is niet lees- of schrijfbaar.',
            4
        );
    }
}
