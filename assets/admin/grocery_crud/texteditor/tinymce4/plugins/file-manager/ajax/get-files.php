<?php
/**
 * Send a directory listing as a JSON object.
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
use Intervention\Image\ImageManager;

require '../vendor/autoload.php';
require '../src/jsonResponse.php';
require '../src/createDirectory.php';
require '../src/formatBytes.php';
require '../config.php';
require '../src/ImageFilter/ThumbnailFilter.php';

$files = [
    'files' => [],
    'error' => [
        'message' => 'No errors detected.',
        'code' => 0
    ]
];

$directories = [];

try {
    if (!\check_login() || !\auth_user_groups(['administrator'])) {
        throw new PermissionException(
            'U dient ingelogd te zijn en tot de administrators te behoren.'
        );
    }

    if (empty($_POST['path'])) {
        $path = '';
    } else {
        $path = trim($_POST['path'], '/');

        // Remove backwards pointing relative components ("..").
        $path = explode(
            DIRECTORY_SEPARATOR,
            str_replace('/', DIRECTORY_SEPARATOR, $path)
        );

        for ($i = 0, $m = count($path); $i < $m; ++$i) {
            if ($path[$i] === '..') {
                unset($path[$i]);
            }
        }

        $path = implode(DIRECTORY_SEPARATOR, $path) . '/';
    }

    $uploadPath = str_replace(
        '/',
        DIRECTORY_SEPARATOR,
        $config['applicationPath'] . $config['uploadPath'] . $path
    );

    $thumbnailPath = str_replace(
        '/',
        DIRECTORY_SEPARATOR,
        $config['applicationPath'] . $config['thumbnailPath'] . $path
    );

    // Create the upload and thumbnail directories if they don't exist or return
    // an error on failure.
    if (!is_dir($uploadPath)) {
        createDirectory($uploadPath, $config['directoryPermissions']);
    }

    if (!is_dir($thumbnailPath)) {
        createDirectory($thumbnailPath, $config['directoryPermissions']);
    }

    // Add a navigation item pointing to the parent folder.
    if (!empty($path)) {
        $directorySize = iterator_count(
            new \FilesystemIterator(
                $uploadPath . '/..',
                \FilesystemIterator::SKIP_DOTS
            )
        );

        if ($directorySize === 1) {
            $directorySize = (string)$directorySize . ' item';
        } else {
            $directorySize = (string)$directorySize . ' items';
        }

        $files['files'][] = [
            'name' => '..',
            'size' => $directorySize,
            'type' => 'dir',
            'modified' => \DateTime::createFromFormat(
                    'U',
                    (new \SplFileInfo($uploadPath . '/..'))->getMTime()
                )->setTimeZone(new \DateTimeZone(date_default_timezone_get()))
                ->format('d-m-Y H:i:s'),
            'filePath' => '/' . $config['uploadPath'] .
                mb_substr(
                    $path,
                    0,
                    mb_strrpos($path, '/')
                ),
            'thumbnailPath' => ''
        ];
    }

    $directoryIterator = new \DirectoryIterator($uploadPath);

    foreach ($directoryIterator as $fileInfo) {
        if (!$fileInfo->isDot() && $fileInfo->isReadable()) {
            // Set the file type to "dir", "link" or the second part of the
            // file's MIME type.
            $fileType = $fileInfo->getType();

            if ($fileType === 'file') {
                // Use fileinfo because \DirectoryIterator does not return the
                // MIME type of files.
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $fileType = finfo_file($finfo, $fileInfo->getPathname());
                finfo_close($finfo);

                if (in_array($fileType, $config['allowedFileTypes']['images'])) {
                    // Create a thumbnail of 100x100 pixels if it doesn't not yet exist.
                    if (!is_file($thumbnailPath . $fileInfo->getFilename())) {
                        (new ImageManager())->make($fileInfo->getPathname())
                            ->filter(new ImageFilter\ThumbnailFilter())
                            ->save(
                                $thumbnailPath . $fileInfo->getFilename(),
                                $config['imageQuality']
                            )->destroy();
                    }
                }

                $files['files'][] = [
                    'name' => $fileInfo->getFilename(),
                    'size' => formatBytes($fileInfo->getSize(), 1, true),
                    'type' => $fileType,
                    'modified' => \DateTime::createFromFormat(
                            'U',
                            $fileInfo->getMTime()
                        )->setTimeZone(new \DateTimeZone(date_default_timezone_get()))
                        ->format('d-m-Y H:i:s'),
                    'filePath' => '/' . $config['uploadPath'] . $path . $fileInfo->getFilename(),
                    'thumbnailPath' => '/' . $config['thumbnailPath'] . $path . $fileInfo->getFilename()
                ];
            } elseif ($fileType === 'link') {
                $files['files'][] = [
                    'name' => $fileInfo->getFilename(),
                    'size' => '-',
                    'type' => $fileType,
                    'modified' => \DateTime::createFromFormat(
                            'U',
                            $fileInfo->getMTime()
                        )->setTimeZone(new \DateTimeZone(date_default_timezone_get()))
                        ->format('d-m-Y H:i:s'),
                    'filePath' => '/' . $config['uploadPath'] . $path . $fileInfo->getFilename(),
                    'thumbnailPath' => ''
                ];
            } elseif ($fileType === 'dir') {
                $directorySize = iterator_count(
                    new \FilesystemIterator(
                        $fileInfo->getPathname(),
                        \FilesystemIterator::SKIP_DOTS
                    )
                );

                if ($directorySize === 1) {
                    $directorySize = (string)$directorySize . ' item';
                } else {
                    $directorySize = (string)$directorySize . ' items';
                }

                $files['files'][] = [
                    'name' => $fileInfo->getFilename(),
                    'size' => $directorySize,
                    'type' => $fileType,
                    'modified' => \DateTime::createFromFormat(
                            'U',
                            $fileInfo->getMTime()
                        )->setTimeZone(new \DateTimeZone(date_default_timezone_get()))
                        ->format('d-m-Y H:i:s'),
                    'filePath' => '/' . $config['uploadPath'] . $path . $fileInfo->getFilename(),
                    'thumbnailPath' => ''
                ];
            }
        }
    }

    // Sort the files, directories first.
    usort(
        $files['files'],
        function($a, $b) {
            if ($a['type'] === 'dir' && $b['type'] !== 'dir') {
                return -1;
            } elseif ($a['type'] !== 'dir' && $b['type'] === 'dir') {
                return 1;
            } else {
                return strcmp($a['name'], $b['name']);
            }
        }
    );

    //var_dump($files['files']);
} catch(PermissionException $e) {
    $files['error']['message'] = $e->getMessage();
    $files['error']['code'] = 4;
} catch (\UnexpectedValueException $e) {
    $files['error']['message'] = $e->getMessage();

    if ($e->getCode() === 0) {
        $files['error']['code'] = 1;
    } else {
        $files['error']['code'] = $e->getCode();
    }
} catch (\RuntimeException $e) {
    $files['error']['message'] = 'Het pad mag niet leeg zijn.';
    $files['error']['code'] = 2;
}

jsonResponse($files);
