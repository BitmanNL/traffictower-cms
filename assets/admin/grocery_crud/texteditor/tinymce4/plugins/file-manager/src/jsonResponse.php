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

require 'setHttpResponseCode.php';

/**
 * Output data as a JSON response.
 *
 * @api
 * @uses \setHttpResponseCode()
 *
 * @param mixed[] $data         The data to output in JSON format.
 * @param int $httpResponseCode The HTTP response code to return.
 *
 * @return void
 */
function jsonResponse($data, $httpResponseCode = 200)
{
    if (gettype($data) === 'resource') {
        throw new \InvalidArgumentException(
            __FUNCTION__ . ' cannot use a resource as the first argument.'
        );
    }

    setHttpResponseCode($httpResponseCode);

    // Set the HTTP headers appropriately for AJAX.
    header('Expires: 0');
    header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');
    header('Content-type: application/json; charset=utf-8');

    echo json_encode($data);
}
