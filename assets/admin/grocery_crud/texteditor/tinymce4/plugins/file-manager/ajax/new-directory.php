<?php
/**
 * Create a new directory on the server.
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
require '../src/createDirectory.php';
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

    if (empty($_POST['directory'])) {
        $directory = '';
    } else {
        // Remove backwards pointing relative components ("..").
        $directory = explode(
            '/',
            trim($_POST['directory'], '/')
        );

        for ($i = 0, $m = count($directory); $i < $m; ++$i) {
            if ($directory[$i] === '..') {
                unset($directory[$i]);
            }
        }

        $directory = implode(DIRECTORY_SEPARATOR, $directory);
    }

    createDirectory(
        $config['applicationPath'] . $config['uploadPath'] . $directory,
        $config['directoryPermissions']
    );
} catch(PermissionException $e) {
    $result['error']['message'] = $e->getMessage();
    $result['error']['code'] = 1;
} catch (\LogicException $e) {
    $result['error']['message'] = $e->getMessage();
    $result['error']['code'] = 2;
} catch (\UnexpectedValueException $e) {
    $result['error']['message'] = $e->getMessage();
    $result['error']['code'] = 4;
}

jsonResponse($result);
