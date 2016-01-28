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
use Bitman\Cms\FileManager\Exception\Edit\FilterException;
use Bitman\Cms\FileManager\Exception\Edit\InvalidActionException;
use Bitman\Cms\FileManager\Exception\Edit\InvalidFileException;
use Bitman\Cms\FileManager\Exception\Edit\ResetException;
use Bitman\Cms\FileManager\Exception\Edit\SaveException;
use Bitman\Cms\FileManager\Exception\Edit\TemporaryFileException;
use Intervention\Image\ImageManager;

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
            'U dient ingelogd te zijn en tot de administrators te behoren.',
            1
        );
    }

    if (empty($_POST['path']) || empty($_POST['originalPath'])) {
        throw new InvalidFileException(
            'Er is geen bestand gekozen om te bewerken.',
            2
        );
    }

    // Remove backwards pointing relative components ("..").
    $path = explode('/', trim($_POST['path'], '/'));

    for ($i = 0, $m = count($path); $i < $m; ++$i) {
        if ($path[$i] === '..') {
            unset($path[$i]);
        }
    }

    $path = implode(DIRECTORY_SEPARATOR, $path);

    $originalPath = explode('/', trim($_POST['originalPath'], '/'));

    for ($i = 0, $m = count($originalPath); $i < $m; ++$i) {
        if ($originalPath[$i] === '..') {
            unset($originalPath[$i]);
        }
    }

    $originalPath = implode(DIRECTORY_SEPARATOR, $originalPath);

    // Remove the query string if the path contains one.
    $querySeparator = mb_strpos($path, '?');

    if ($querySeparator !== false) {
        $path = mb_substr($path, 0, $querySeparator);
    }

    if (isset($_POST['action']) && $_POST['action'] === 'init') {
        // Create a temporary copy of the file that's going to be edited.
        $source = $config['applicationPath'] . $path;
        $destination = $config['applicationPath'] . $config['temporaryPath'] .
            str_replace($config['uploadPath'], '', $path);

        $path = '/' . str_replace(
            [$config['uploadPath'], DIRECTORY_SEPARATOR],
            [$config['temporaryPath'], '/'],
            $path
        );

        if (!is_dir(dirname($destination))) {
            createDirectory(
                dirname($destination),
                $config['directoryPermissions']
            );
        }

        if (
            !copy($source, $destination)
            || !chmod($destination, $config['filePermissions'])
        ) {
            throw new TemporaryFileException(
                'Er kon geen tijdelijk bestand worden aangemaakt.',
                3
            );
        }

        $image = (new ImageManager())->make($config['applicationPath'] . $path);

        $result['width'] = $image->width();
        $result['height'] = $image->height();

        $image->destroy();
    } else {
        // Perform the requested action.
        if (!empty($_POST['arguments'])) {
            $arguments = json_decode($_POST['arguments'], true);
        } else {
            $arguments = [];
        }

        $image = (new ImageManager())->make(
            $config['applicationPath'] . ltrim($path, '/')
        );

        switch ($_POST['action']) {
            case 'save':
                $image->save(
                    $config['applicationPath'] . $originalPath,
                    $config['imageQuality']
                );

                // Re-create the thumbnail.
                (new ImageManager())->make($image->getCore())
                    ->filter(new ImageFilter\ThumbnailFilter())
                    ->save(
                        $config['applicationPath'] . $config['thumbnailPath'] .
                            str_replace(
                                $config['temporaryPath'],
                                '',
                                $path
                            ),
                        $config['imageQuality']
                    )->destroy();

                break;
            case 'reset':
                $image->destroy();

                $image = (new ImageManager())->make(
                    $config['applicationPath'] . $originalPath
                );

                break;
            case 'alter-size':
                if (
                    empty($arguments['width']) ||
                    empty($arguments['height'])
                ) {
                    throw new FilterException(
                        'U heeft geen breedte of hoogte ingevuld.',
                        5
                    );
                }

                $image->filter(
                    new ImageFilter\SizeFilter(
                        (int)$arguments['width'],
                        (int)$arguments['height']
                    )
                );

                break;
            case 'alter-crop':
                if (
                    !isset($arguments['x']) ||
                    !isset($arguments['y'])
                ) {
                    throw new FilterException(
                        'U heeft geen X of Y coördinaten ingevuld.',
                        6
                    );
                }

                if (
                    empty($arguments['width']) ||
                    empty($arguments['height'])
                ) {
                    throw new FilterException(
                        'U heeft geen breedte of hoogte ingevuld.',
                        7
                    );
                } else {
                    $x = (int)$arguments['x'];
                    $y = (int)$arguments['y'];
                    $width = (int)$arguments['width'];
                    $height = (int)$arguments['height'];

                    if (
                        $x < 0 || $x > $image->width() ||
                        $y < 0 || $y > $image->height()
                    ) {
                        throw new FilterException(
                            'De coördinaten vallen buiten de afbeelding.',
                            8
                        );
                    }

                    if ($width < 0 || $height < 0) {
                        throw new FilterException(
                            'De breedte en hoogte kunnen niet lager dan 0 zijn.',
                            9
                        );
                    }

                    $image->filter(
                        new ImageFilter\CropFilter($x, $y, $width, $height)
                    );
                }

                break;
            case 'alter-flip-rotate':
                if (!empty($arguments['flip'])) {
                    if ($arguments['flip'] === 'horizontal') {
                        $flipRotateFilter = new ImageFilter\FlipRotateFilter(
                            ImageFilter\FlipRotateFilter::FLIP_HORIZONTAL
                        );
                    } elseif($arguments['flip'] === 'vertical') {
                        $flipRotateFilter = new ImageFilter\FlipRotateFilter(
                            ImageFilter\FlipRotateFilter::FLIP_VERTICAL
                        );
                    } else {
                        throw new FilterException(
                            'De manier van spiegelen die u ' .
                                'gekozen heeft bestaat niet.',
                            10
                        );
                    }
                } elseif (!empty($arguments['rotate'])) {
                    if ($arguments['rotate'] === 'left') {
                        $flipRotateFilter = new ImageFilter\FlipRotateFilter(
                            ImageFilter\FlipRotateFilter::ROTATE_LEFT
                        );
                    } elseif ($arguments['rotate'] === 'right') {
                        $flipRotateFilter = new ImageFilter\FlipRotateFilter(
                            ImageFilter\FlipRotateFilter::ROTATE_RIGHT
                        );
                    } else {
                        throw new FilterException(
                            'De manier van draaien die u ' .
                                'gekozen heeft bestaat niet.',
                            11
                        );
                    }
                } else {
                    throw new FilterException(
                        'U heeft niet gekozen in welke richting u de ' .
                            'afbeelding wilt spiegelen of draaien.',
                        12
                    );
                }

                $image->filter($flipRotateFilter);

                break;
            case 'filter-brightness':
                if (!isset($arguments['level'])) {
                    throw new FilterException(
                        'U heeft geen helderheid ingevuld.',
                        13
                    );
                }

                $arguments['level'] = (int)$arguments['level'];

                if (
                    $arguments['level'] < -100 ||
                    $arguments['level'] > 100
                ) {
                    throw new FilterException(
                        'De helderheid kan alleen een waarde ' +
                            'van -100% tot en met 100% zijn.',
                        14
                    );
                }

                $image->filter(
                    new ImageFilter\BrightnessFilter($arguments['level'])
                );

                break;
            case 'filter-contrast':
                if (!isset($arguments['level'])) {
                    throw new FilterException(
                        'U heeft geen contrast ingevuld.',
                        15
                    );
                }

                $arguments['level'] = (int)$arguments['level'];

                if (
                    $arguments['level'] < -100 ||
                    $arguments['level'] > 100
                ) {
                    throw new FilterException(
                        'Het contrast kan alleen een waarde ' +
                            'van -100% tot en met 100% zijn.',
                        16
                    );
                }

                $image->filter(
                    new ImageFilter\ContrastFilter($arguments['level'])
                );

                break;
            case 'filter-exposure':
                if (!isset($arguments['level'])) {
                    throw new FilterException(
                        'U heeft geen belichting ingevuld.',
                        17
                    );
                }

                $arguments['level'] = (int)$arguments['level'];

                if (
                    $arguments['level'] < 0 ||
                    $arguments['level'] > 200
                ) {
                    throw new FilterException(
                        'De belichting kan alleen een waarde ' +
                            'van 0% tot en met 200% zijn.',
                        18
                    );
                }

                $image->filter(
                    new ImageFilter\ExposureFilter($arguments['level'])
                );

                break;
            case 'filter-gamma':
                if (!isset($arguments['correction'])) {
                    throw new FilterException(
                        'U heeft geen gamma waarde ingevuld.',
                        19
                    );
                }

                $arguments['correction'] = (float)$arguments['correction'];

                if (
                    $arguments['correction'] < 0.0 ||
                    $arguments['correction'] > 2.0
                ) {
                    throw new FilterException(
                        'De gamma kan alleen een waarde ' +
                            'van 0 tot en met 2 zijn.',
                        20
                    );
                }

                $image->filter(
                    new ImageFilter\GammaFilter($arguments['correction'])
                );

                break;
            case 'filter-hue':
                if (!isset($arguments['hue'])) {
                    throw new FilterException(
                        'U heeft geen tint ingevuld.',
                        21
                    );
                }

                $arguments['hue'] = (int)$arguments['hue'];

                if (
                    $arguments['hue'] < -100 ||
                    $arguments['hue'] > 100
                ) {
                    throw new FilterException(
                        'De tint kan alleen een waarde ' +
                            'van -100% tot en met 100% zijn.',
                        22
                    );
                }

                $image->filter(
                    new ImageFilter\HueFilter($arguments['hue'])
                );

                break;
            case 'filter-saturate':
                if (!isset($arguments['level'])) {
                    throw new FilterException(
                        'U heeft geen verzadiging ingevuld.',
                        23
                    );
                }

                $arguments['level'] = (int)$arguments['level'];

                if (
                    $arguments['level'] < -100 ||
                    $arguments['level'] > 100
                ) {
                    throw new FilterException(
                        'De verzadiging kan alleen een waarde ' +
                            'van -100% tot en met 100% zijn.',
                        24
                    );
                }

                $image->filter(
                    new ImageFilter\SaturateFilter($arguments['level'])
                );

                break;
            case 'filter-sepia':
                $image->filter(new ImageFilter\SepiaFilter());

                break;
            case 'filter-vibrance':
                if (!isset($arguments['level'])) {
                    throw new FilterException(
                        'U heeft geen levendigheid ingevuld.',
                        25
                    );
                }

                $arguments['level'] = (int)$arguments['level'];

                if (
                    $arguments['level'] < -100 ||
                    $arguments['level'] > 100
                ) {
                    throw new FilterException(
                        'De levendigheid kan alleen een waarde ' +
                            'van -100% tot en met 100% zijn.',
                        26
                    );
                }

                $image->filter(
                    new ImageFilter\VibranceFilter($arguments['level'])
                );

                break;
            case 'filter-blur':
                if (!isset($arguments['amount'])) {
                    throw new FilterException(
                        'U heeft geen blur sterkte ingevuld.',
                        27
                    );
                }

                $arguments['amount'] = (int)$arguments['amount'];

                if (
                    $arguments['amount'] < 0 ||
                    $arguments['amount'] > 100
                ) {
                    throw new FilterException(
                        'De blur sterkte kan alleen een waarde ' +
                            'van 0% tot en met 100% zijn.',
                        28
                    );
                }

                $image->filter(
                    new ImageFilter\BlurFilter($arguments['amount'])
                );

                break;
            case 'filter-colorize':
                if (!isset($arguments['red'])) {
                    throw new FilterException(
                        'U heeft geen hoeveelheid rood ingevuld.',
                        29
                    );
                }

                if (!isset($arguments['green'])) {
                    throw new FilterException(
                        'U heeft geen hoeveelheid groen ingevuld.',
                        30
                    );
                }

                if (!isset($arguments['blue'])) {
                    throw new FilterException(
                        'U heeft geen hoeveelheid blauw ingevuld.',
                        31
                    );
                }

                $arguments['red'] = (int)$arguments['red'];
                $arguments['green'] = (int)$arguments['green'];
                $arguments['blue'] = (int)$arguments['blue'];

                if (
                    $arguments['red'] < -100 ||
                    $arguments['red'] > 100 ||
                    $arguments['green'] < -100 ||
                    $arguments['green'] > 100 ||
                    $arguments['blue'] < -100 ||
                    $arguments['blue'] > 100
                ) {
                    throw new FilterException(
                        'De hoeveelheid kleur kan alleen een waarde ' +
                            'van -100% tot en met 100% zijn.',
                        32
                    );
                }

                $image->filter(
                    new ImageFilter\ColorizeFilter(
                        $arguments['red'],
                        $arguments['green'],
                        $arguments['blue']
                    )
                );

                break;
            case 'filter-grayscale':
                $image->filter(new ImageFilter\GrayscaleFilter());

                break;
            case 'filter-negative':
                $image->filter(new ImageFilter\NegativeFilter());

                break;
            case 'filter-sharpen':
                if (!isset($arguments['amount'])) {
                    throw new FilterException(
                        'U heeft geen scherpte ingevuld.',
                        37
                    );
                }

                $arguments['amount'] = (int)$arguments['amount'];

                if (
                    $arguments['amount'] < 0 ||
                    $arguments['amount'] > 100
                ) {
                    throw new FilterException(
                        'De scherpte kan alleen een waarde ' +
                            'van 0% tot en met 100% zijn.',
                        38
                    );
                }

                $image->filter(
                    new ImageFilter\SharpenFilter($arguments['amount'])
                );

                break;
            case 'filter-emboss':
                $image->filter(new ImageFilter\EmbossFilter());

                break;
            default:
                throw new InvalidActionException(
                    'De gekozen bewerking "' . $_POST['action'] .
                    '" bestaat niet.',
                    4
                );

                break;
        }

        $path = '/' . $path;

        $result['width'] = $image->width();
        $result['height'] = $image->height();

        $image->save(
            $config['applicationPath'] . $path,
            $config['imageQuality']
        )->destroy();
    }

    $result['path'] = $path;
} catch (\Exception $e) {
    $result['error']['message'] = $e->getMessage();

    if ($e->getCode() === 0) {
        $result['error']['code'] = 1;
    } else {
        $result['error']['code'] = $e->getCode();
    }
}

jsonResponse($result);
