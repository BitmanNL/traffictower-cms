<?php
/**
 * Copyright (c)2013-2013 heiglandreas
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIBILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package   CMS\Core\Admin\FileManager
 * @author    Andreas Heigl <andreas@heigl.org>
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013, 2016 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @since     22.03.13
 * @link      https://github.com/heiglandreas/
 */

namespace Bitman\Cms\FileManager\ImageFilter;

use Bitman\Cms\FileManager\Exception\Edit\FilterException;
use Intervention\Image;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

/**
 * An Intervention Image filter for changing the saturation of images
 */
class SaturateFilter implements Image\Filters\FilterInterface
{
    /** @var int $level The saturation level to apply to the image. */
    private $level;

    /**
     * Construct a new SaturateFilter
     *
     * @param int $level The saturation level (-100-100) to apply to the image.
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

                list($h, $s, $l) = ColorConverter::rgbToHsl($r, $g, $b);

                $s = $s * (100 + $this->level) / 100;

                if ($s > 1.0) {
                    $s = 1.0;
                }

                list($r, $g, $b) = ColorConverter::hslToRgb($h, $s, $l);

                imagesetpixel(
                    $gd,
                    $x,
                    $y,
                    imagecolorallocatealpha($gd, $r, $g, $b, $a)
                );
            }
        }

        $image->setCore($gd);

        return $image;
    }
}
