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
 * Set the HTTP response code of the current request.
 *
 * @api
 * @uses \header()
 *
 * @param int $responseCode The HTTP response code to set.
 *
 * @return void
 *
 * @throws \DomainException if the provided argument is an unknown HTTP status
 *                          code.
 */
function setHttpResponseCode($responseCode)
{
    switch ($responseCode) {
        case 100:
            $message = 'Continue';

            break;
        case 101:
            $message = 'Switching Protocols';

            break;
        case 200:
            $message = 'OK';

            break;
        case 201:
            $message = 'Created';

            break;
        case 202:
            $message = 'Accepted';

            break;
        case 203:
            $message = 'Non-Authoritative Information';

            break;
        case 204:
            $message = 'No Content';

            break;
        case 205:
            $message = 'Reset Content';

            break;
        case 206:
            $message = 'Partial Content';

            break;
        case 300:
            $message = 'Multiple Choices';

            break;
        case 301:
            $message = 'Moved Permanently';

            break;
        case 302:
            $message = 'Moved Temporarily';

            break;
        case 303:
            $message = 'See Other';

            break;
        case 304:
            $message = 'Not Modified';

            break;
        case 305:
            $message = 'Use Proxy';

            break;
        case 307:
            $message = 'Temporary Redirect';

            break;
        case 400:
            $message = 'Bad Request';

            break;
        case 401:
            $message = 'Unauthorized';

            break;
        case 402:
            $message = 'Payment Required';

            break;
        case 403:
            $message = 'Forbidden';

            break;
        case 404:
            $message = 'Not Found';

            break;
        case 405:
            $message = 'Method Not Allowed';

            break;
        case 406:
            $message = 'Not Acceptable';

            break;
        case 407:
            $message = 'Proxy Authentication Required';

            break;
        case 408:
            $message = 'Request Time-out';

            break;
        case 409:
            $message = 'Conflict';

            break;
        case 410:
            $message = 'Gone';

            break;
        case 411:
            $message = 'Length Required';

            break;
        case 412:
            $message = 'Precondition Failed';

            break;
        case 413:
            $message = 'Request Entity Too Large';

            break;
        case 414:
            $message = 'Request-URI Too Large';

            break;
        case 415:
            $message = 'Unsupported Media Type';

            break;
        case 416:
            $message = 'Requested Range Not Satisfiable';

            break;
        case 417:
            $message = 'Expectation Failed';

            break;
        case 500:
            $message = 'Internal Server Error';

            break;
        case 501:
            $message = 'Not Implemented';

            break;
        case 502:
            $message = 'Bad Gateway';

            break;
        case 503:
            $message = 'Service Unavailable';

            break;
        case 504:
            $message = 'Gateway Timeout';

            break;
        case 505:
            $message = 'HTTP Version Not Supported';

            break;
        default:
            throw new \DomainException(
                'Unknown HTTP status code "' .
                htmlentities($responseCode) . '"'
            );
    }

    if (isset($_SERVER['SERVER_PROTOCOL'])) {
        $protocol = $_SERVER['SERVER_PROTOCOL'];
    } else {
        $protocol = 'HTTP/1.0';
    }

    header($protocol . ' ' . $responseCode . ' ' . $message);
}
