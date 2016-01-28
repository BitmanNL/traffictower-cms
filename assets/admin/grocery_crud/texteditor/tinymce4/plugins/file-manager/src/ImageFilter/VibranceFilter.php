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
 * @copyright 2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace Bitman\Cms\FileManager\ImageFilter;

use Bitman\Cms\FileManager\Exception\Edit\FilterException;
use Intervention\Image;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

/**
 * An Intervention Image filter for changing the vibrance of images
 */
class VibranceFilter implements Image\Filters\FilterInterface
{
    /** @var int $level The vibrance level (-100-100) to apply to the image. */
    private $level;

    /**
     * Construct a new VibranceFilter
     *
     * @param int $level The vibrance level (-100-100) to apply to the image.
     */
    public function __construct($level)
    {
        $this->level = $level;
    }

    /**
     * Applies filter effects to the given image
     *
     * @param Image\Image $image The image to filter.
     *
     * @return Image\Image The filtered image.
     *
     * @throws FilterException if the image filter algorithm fails.
     */
    public function applyFilter(Image\Image $image)
    {
        if ($this->level <= 0) {
            $gd = $image->getCore();

            $width = imagesx($gd);
            $height = imagesy($gd);

            for ($x = 0; $x < $width; ++$x) {
                for ($y = 0; $y < $height; ++$y) {
                    $rgba = imagecolorsforindex($gd, imagecolorat($gd, $x, $y));

                    $r = $rgba['red'];
                    $g = $rgba['green'];
                    $b = $rgba['blue'];
                    $a = $rgba['alpha'];

                    $level = $this->level * -1;

                    $max = max($r, $g, $b);
                    $avg = ($r + $g + $b) / 3;
                    $amt = (((abs($max - $avg) * 2) / 255) * $level) / 100;

                    if ($r !== $max) {
                        $r += ($max - $r) * $amt;
                    }

                    if ($g !== $max) {
                        $g += ($max - $g) * $amt;
                    }

                    if ($b !== $max) {
                        $b += ($max - $b) * $amt;
                    }

                    imagesetpixel(
                        $gd,
                        $x,
                        $y,
                        imagecolorallocatealpha($gd, $r, $g, $b, $a)
                    );
                }
            }

            $image->setCore($gd);
        } else {
            $image->filter(new SaturateFilter($this->level));
        }

        return $image;
    }
}
