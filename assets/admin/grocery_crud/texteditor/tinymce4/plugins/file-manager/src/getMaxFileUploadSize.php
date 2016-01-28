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
 * Get the maximum file upload size (in bytes).
 * @return int The maximum file upload size in bytes.
 *
 * @throws \InvalidArgumentException if the first argument is less than zero.
 */
function getMaxFileUploadSize()
{
    $convertToBytes = function($size) {
        if (is_int($size)) {
            if ($size >= 0) {
                return $size;
            } else {
                throw new \InvalidArgumentException(
                    __FUNCTION__ . ' expects the first argument to be zero or higher.'
                );
            }
        }

        $suffix = substr($size, -1);
        $value = substr($size, 0, -1);

        if ((int)$value < 0) {
            throw new \InvalidArgumentException(
                __FUNCTION__ . ' expects the first argument to be zero or higher.'
            );
        }

        switch (strtoupper($suffix)) {
            case 'P':
                $value *= 1024;
            case 'T':
                $value *= 1024;
            case 'G':
                $value *= 1024;
            case 'M':
                $value *= 1024;
            case 'K':
                $value *= 1024;

                break;
        }

        return $value;
    };

    $postMaxSize = ini_get('post_max_size');
    $uploadMaxFilesize = ini_get('upload_max_filesize');

    if ($postMaxSize === '0') {
        return $convertToBytes($uploadMaxFilesize);
    }

    return min(
        $convertToBytes($postMaxSize),
        $convertToBytes($uploadMaxFilesize)
    );
}
