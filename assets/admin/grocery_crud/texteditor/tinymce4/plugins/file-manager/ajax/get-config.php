<?php
/**
 * Send the file manager configuration as a JSON object.
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

require '../src/jsonResponse.php';
require '../config.php';

try {
    if (!\check_login() || !\auth_user_groups(['administrator'])) {
        throw new PermissionException(
            'U dient ingelogd te zijn en tot de administrators te behoren.'
        );
    }

    // Remove the application and installation paths. It's calculated and set as
    // a URL dynamically.
    unset($config['applicationPath']);
    unset($config['installationPath']);

    // Remove the temporary path, permissions and CI instance as they have no
    // use outside PHP.
    unset($config['temporaryPath']);
    unset($config['filePermissions']);
    unset($config['directoryPermissions']);
    unset($config['ci']);
} catch(PermissionException $e) {
    $config['error']['message'] = $e->getMessage();
    $config['error']['code'] = 1;
}

jsonResponse($config);
