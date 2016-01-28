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
 * An Intervention Image filter for performing gamma correction on images
 */
class GammaFilter implements Image\Filters\FilterInterface
{
    /** @var float $level The gamma correction (0-2) to apply to the image. */
    private $correction;

    /**
     * Construct a new GammaFilter
     *
     * @param float $correction The gamma correction (0-2) to apply to the image.
     */
    public function __construct($correction)
    {
        $this->correction = $correction;
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
        // The Intervention Image gamma method uses gamma backwards. A gamma > 1
        // must result in darker images and a gamma < 1 will make the image
        // lighter. So we have to flip the value around.
        if ($this->correction > 0.0) {
            $correction = 1 / $this->correction;
        } else {
            $correction = 100.0;
        }

        return $image->gamma($correction);
    }
}
