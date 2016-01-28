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

/**
 * Convert RGB colors to HSL and the other way around
 */
class ColorConverter
{
    /**
     * Convert RGB to HSL
     *
     * @param int $r The red component (0-255) to convert.
     * @param int $g The green component (0-255) to convert.
     * @param int $b The blue component (0-255) to convert.
     *
     * @return array The converted color.
     *
     * @see http://www.easyrgb.com/index.php?X=MATH&H=20#text20
     */
    public static function rgbToHsl($r, $g, $b)
    {
        $varR = (float)($r / 255);
        $varG = (float)($g / 255);
        $varB = (float)($b / 255);

        $varMin = min($varR, $varG, $varB);
        $varMax = max($varR, $varG, $varB);
        $delMax = $varMax - $varMin;

        $l = ($varMax + $varMin) / 2;

        if ($delMax === 0.0) {
            $h = 0.0;
            $s = 0.0;
        } else {
            if ($l < 0.5) {
                $s = $delMax / ($varMax + $varMin);
            } else {
                $s = $delMax / (2.0 - $varMax - $varMin);
            }

            $delR = ((($varMax - $varR) / 6.0) + ($delMax / 2.0)) / $delMax;
            $delG = ((($varMax - $varG) / 6.0) + ($delMax / 2.0)) / $delMax;
            $delB = ((($varMax - $varB) / 6.0) + ($delMax / 2.0)) / $delMax;

            if ($varR === $varMax) {
                $h = $delB - $delG;
            } elseif ($varG === $varMax) {
                $h = (1.0 / 3.0) + $delR - $delB;
            } elseif ($varB == $varMax) {
                $h = (2.0 / 3.0) + $delG - $delR;
            }

            if ($h < 0.0) {
                $h += 1.0;
            }

            if ($h > 1.0) {
                $h -= 1.0;
            }
        }

        return [$h, $s, $l];
    }

    /**
     * Convert HSL to RGB
     *
     * @param float $h The hue component (0-1) to convert.
     * @param float $s The saturation component (0-1) to convert.
     * @param float $l The lightness component (0-1) to convert.
     *
     * @return array The converted color.
     *
     * @see http://www.easyrgb.com/index.php?X=MATH&H=19#text19
     */
    public static function hslToRgb($h, $s, $l)
    {
        $rgb = [];

        if ($s === 0) {
            $rgb[0] = $l * 255;
            $rgb[1] = $l * 255;
            $rgb[2] = $l * 255;
        } else {
            if ($l < 0.5 ) {
                $var2 = $l * (1 + $s);
            } else {
                $var2 = ($l + $s) - ($s * $l);
            }

            $var1 = 2 * $l - $var2;

            $rgb[0] = (int)round(
                255 * self::hueToRgb($var1, $var2, $h + (1 / 3))
            );

            $rgb[1] = (int)round(
                255 * self::hueToRgb($var1, $var2, $h)
            );

            $rgb[2] = (int)round(
                255 * self::hueToRgb($var1, $var2, $h - (1 / 3))
            );
        }

        return $rgb;
    }

    /**
     * Convert a hue to a RGB-value
     *
     * @param float $v1
     * @param float $v2
     * @param float $vH
     *
     * @return float
     */
    private static function hueToRgb($v1, $v2, $vH)
    {
        if ($vH < 0) {
            $vH += 1;
        }

        if ($vH > 1) {
            $vH -= 1;
        }

        if ((6 * $vH ) < 1) {
            return ($v1 + ($v2 - $v1) * 6 * $vH);
        }

        if ((2 * $vH) < 1) {
            return $v2;
        }

        if ((3 * $vH) < 2) {
            return ($v1 + ($v2 - $v1) * ((2 / 3) - $vH) * 6);
        }

        return $v1;
    }
}
