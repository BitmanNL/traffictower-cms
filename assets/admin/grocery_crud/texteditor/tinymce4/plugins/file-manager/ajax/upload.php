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

use Bitman\Cms\FileManager\Exception\FileManagerException;
use Bitman\Cms\FileManager\Exception\PermissionException;
use Bitman\Cms\FileManager\Exception\Upload\EmptyUploadException;
use Bitman\Cms\FileManager\Exception\Upload\ExtensionException;
use Bitman\Cms\FileManager\Exception\Upload\FileTypeRejectedException;
use Bitman\Cms\FileManager\Exception\Upload\MaxFileUploadSizeExceededException;
use Bitman\Cms\FileManager\Exception\Upload\PartialUploadException;
use Bitman\Cms\FileManager\Exception\Upload\PermissionException as UploadPermissionException;

require '../vendor/autoload.php';
require '../src/jsonResponse.php';
require '../src/createDirectory.php';
require '../src/getMaxFileUploadSize.php';
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
            'U dient ingelogd te zijn en tot de administrators te behoren.',
            1
        );
    }

    if (empty($_FILES)) {
        throw new EmptyUploadException('U heeft geen bestand(en) gekozen.', 2);
    }

    $total = 0;

    $result['test'] = $_FILES;
    //foreach( $_FILES[ 'image' ][ 'tmp_name' ] as $index => $tmpName )
    foreach ($_FILES as $file) {
        if (($extensionSeparatorPos = mb_strrpos($file['name'], '.')) !== false) {
            $extension = mb_strtolower(mb_substr($file['name'], $extensionSeparatorPos + 1));
        } else {
            $extension = '';
        }

        // Check if the file's type and extension are allowed.
        if (
            !in_array(
                $extension,
                array_merge(
                    $config['allowedFileExtensions']['archives'],
                    $config['allowedFileExtensions']['documents'],
                    $config['allowedFileExtensions']['images']
                )
            ) || !in_array(
                $file['type'],
                array_merge(
                    $config['allowedFileTypes']['archives'],
                    $config['allowedFileTypes']['documents'],
                    $config['allowedFileTypes']['images']
                )
            )
        ) {
            throw new FileTypeRejectedException(
                $file['name'] . ' kan niet geüpload worden, omdat het bestandstype (' .
                    $file['type'] . ') niet toegestaan wordt.',
                3
            );
        }

        // Check for any of PHP's internal file upload errors.
        if ($file['error'] !== UPLOAD_ERR_OK) {
            switch ($file['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    throw new MaxFileUploadSizeExceededException(
                        'De bestanden kunnen niet worden geüpload, ' .
                            'omdat deze samen groter dan ' .
                            formatBytes(getMaxFileUploadSize(), 0, true) .
                            ' zijn.',
                        8
                    );

                    break;
                case UPLOAD_ERR_PARTIAL:
                    throw new PartialUploadException(
                        'Het bestand ' . $file['name'] . ' was slechts deels ' .
                            'geüpload. Probeer het alstublieft opnieuw.',
                        4
                    );

                    break;
                case UPLOAD_ERR_NO_FILE:
                    throw new EmptyUploadException(
                        'U heeft geen bestand(en) gekozen.',
                        2
                    );

                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new NoTemporaryDirectoryException(
                        'Er is geen tijdelijke map beschikbaar voor ' .
                            'het uploaden van bestanden.',
                        5
                    );

                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    throw new UploadPermissionException(
                        'De bestanden kunnen niet worden geüpload, ' .
                            'omdat de map niet schrijfbaar is.',
                        6
                    );

                    break;
                case UPLOAD_ERR_EXTENSION:
                    throw new ExtensionException(
                        'De bestanden kunnen niet worden geüpload, ' .
                            'omdat een PHP extensie de upload heeft gestopt.',
                        7
                    );

                    break;
            }
        }

        $total += $file['size'];
    }

    if ($total > getMaxFileUploadSize()) {
        throw new MaxFileUploadSizeExceededException(
            'De bestanden kunnen niet worden geüpload, omdat deze samen groter dan '
                . formatBytes(getMaxFileUploadSize(), 0, true) . ' zijn.',
            8
        );
    }

    if (empty($_POST['path'])) {
        $path = '';
    } else {
        // Remove backwards pointing relative components ("..").
        $path = explode(
            DIRECTORY_SEPARATOR,
            $_POST['path']
        );

        for ($i = 0, $m = count($path); $i < $m; ++$i) {
            if ($path[$i] === '..') {
                unset($path[$i]);
            }
        }

        $path = implode(DIRECTORY_SEPARATOR, $path);
    }

    $destination = $config['applicationPath'] .
        $config['uploadPath'] .
        trim($path, '/');

    foreach ($_FILES as $file) {
        if (
            !move_uploaded_file(
                $file['tmp_name'],
                $destination . DIRECTORY_SEPARATOR . $file['name']
            ) || !chmod(
                $destination . DIRECTORY_SEPARATOR . $file['name'],
                $config['filePermissions']
            )
        ) {
            throw new UploadException(
                $file['name'] . ' kan niet worden geüpload, ' .
                    'omdat deze incorrect is verstuurd.',
                9
            );
        }
    }
} catch(FileManagerException $e) {
    $result['error']['message'] = $e->getMessage();
    $result['error']['code'] = $e->getCode();
}

jsonResponse($result);
