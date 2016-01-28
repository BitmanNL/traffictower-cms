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
 * @author    Andreas Heigl <andreas@heigl.org>
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013, 2016 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   1.0
 * @since     22.03.13
 * @link      https://github.com/heiglandreas/
 */

require 'hue2rgb.php';

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
public function hsl2rgb($h, $s, $l)
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

        $rgb[0] = 255 * \hue2rgb($var1, $var2, $h + (1 / 3));
        $rgb[1] = 255 * \hue2rgb($var1, $var2, $h);
        $rgb[2] = 255 * \hue2rgb($var1, $var2, $h - (1 / 3));
    }

    return $rgb;
}
