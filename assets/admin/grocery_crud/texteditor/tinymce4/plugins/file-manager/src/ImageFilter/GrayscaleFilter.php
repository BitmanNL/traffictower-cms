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
 * An Intervention Image filter for blurring images
 */
class GrayscaleFilter implements Image\Filters\FilterInterface
{
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
        return $image->greyscale();
    }
}
