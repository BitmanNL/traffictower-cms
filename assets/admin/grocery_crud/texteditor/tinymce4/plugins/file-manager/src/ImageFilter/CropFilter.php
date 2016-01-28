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

namespace Bitman\Cms\FileManager\ImageFilter;

use Bitman\Cms\FileManager\Exception\Edit\FilterException;
use Intervention\Image;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed.');
}

/**
 * An Intervention Image filter for cropping images
 */
class CropFilter implements Image\Filters\FilterInterface
{
    /** @var int $x The x coordinate of the crop selection. */
    private $x;

    /** @var int $y The y coordinate of the crop selection. */
    private $y;

    /** @var int $width The width of the crop selection. */
    private $width;

    /** @var int $height The height of the crop selection. */
    private $height;

    /**
     * Construct a new CropFilter
     *
     * @param int $x      The x coordinate of the crop selection.
     * @param int $y      The y coordinate of the crop selection.
     * @param int $width  The width of the crop selection.
     * @param int $height The height of the crop selection.
     */
    public function __construct($x, $y, $width, $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Applies filter effects to the given image
     *
     * @param Image\Image $image The image to filter.
     *
     * @return Image\Image The filtered image.
     */
    public function applyFilter(Image\Image $image)
    {
        return $image->crop($this->width, $this->height, $this->x, $this->y);
    }
}
