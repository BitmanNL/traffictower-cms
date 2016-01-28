<?php
/**
 * Remove a file from the server.
 *
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

use Bitman\Cms\FileManager\Exception\PermissionException;

require '../vendor/autoload.php';
require '../src/jsonResponse.php';
require '../src/rmrdir.php';
require '../config.php';

$result = [
    'error' => [
        'message' => 'No errors detected.',
        'code' => 0
    ]
];

try {
    if (!\check_login() || !\auth_user_groups(['administrator'])) {
        throw new PermissionException(
            'U dient ingelogd te zijn en tot de administrators te behoren.'
        );
    }

    if (empty($_POST['filePath'])) {
        throw new \UnexpectedValueException('The file path cannot be empty.');
    }

    $filePath = str_replace(
        '/',
        DIRECTORY_SEPARATOR,
        trim($_POST['filePath'], '/')
    );

    // Remove backwards pointing relative components ("..").
    $filePath = explode(
        DIRECTORY_SEPARATOR,
        $filePath
    );

    for ($i = 0, $m = count($filePath); $i < $m; ++$i) {
        if ($filePath[$i] === '..') {
            unset($filePath[$i]);
        }
    }

    $filePath = implode(DIRECTORY_SEPARATOR, $filePath);

    $filePath = $config['applicationPath'] . $config['uploadPath'] . $filePath;

    if (is_dir($filePath)) {
        rmrdir($filePath);
    } else {
        unlink($filePath);
    }

} catch(PermissionException $e) {
    $result['error']['message'] = $e->getMessage();
    $result['error']['code'] = 1;
} catch (\UnexpectedValueException $e) {
    $result['error']['message'] = $e->getMessage();
    $result['error']['code'] = 2;
}

jsonResponse($result);
