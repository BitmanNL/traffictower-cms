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

/**
 * Convert RGB to HSL
 *
 * @param int $r The red component (0-255) to convert.
 * @param int $g The green component (0-255) to convert.
 * @param int $b The blue component (0-255) to convert.
 *
 * @return array The converted color.
 *
 * @see http://www.easyrgb.com/index.php?X=MATH&H=19#text19
 */
public function rgb2hsl($r, $g, $b)
{
    $varR = ($r / 255);
    $varG = ($g / 255);
    $varB = ($b / 255);

    $varMin = min($varR, $varG, $varB);
    $varMax = max($varR, $varG, $varB);

    $delMax = $varMax - $varMin;
    $l = ($varMax + $varMin) / 2;

    if (0.0 === $delMax) {
        $h = 0.0;
        $s = 0.0;
    } else {
        if ( $l < 0.5 ) {
            $s = $delMax / ($varMax + $varMin);
        } else {
            $s = $delMax / (2 - $varMax - $varMin);
        }

        $delR = ((($varMax - $varR) / 6) + ($delMax / 2)) / $delMax;
        $delG = ((($varMax - $varG) / 6) + ($delMax / 2)) / $delMax;
        $delB = ((($varMax - $varB) / 6) + ($delMax / 2)) / $delMax;

        if ($varR === $varMax) {
            $h = $delB - $delG;
        } elseif ($varG === $varMax) {
            $h = (1 / 3) + $delR - $delB;
        } elseif ($varB == $varMax) {
            $h = (2 / 3) + $delG - $delR;
        }
        if ($h < 0) {
            $h += 1;
        }
        if ($h > 1) {
            $h -= 1;
        }
    }
    return [$h, $s, $l];
}
