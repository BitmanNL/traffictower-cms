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
class FlipRotateFilter implements Image\Filters\FilterInterface
{
    /** @var string Mode to flip the image horizontally. */
    const FLIP_HORIZONTAL = 'horizontal';

    /** @var string Mode to flip the image vertically. */
    const FLIP_VERTICAL = 'vertical';

    /** @var string Mode to rotate the image counterclockwise. */
    const ROTATE_LEFT = 'left';

    /** @var string Mode to rotate the image clockwise. */
    const ROTATE_RIGHT = 'right';

    private $mode;

    /**
     * Construct a new FlipRotateFilter
     *
     * Flip or rotate an image depending on the mode specified. Use
     * FlipRotateFilter's constants to choose a mode:
     * - FLIP_HORIZONTAL to flip the image horizontally.
     * - FLIP_VERTICAL to flip the image vertically.
     * - ROTATE_LEFT to rotate the image counterclockwise.
     * - ROTATE_RIGHT to rotate the image clockwise.
     *
     * @param string $mode The mode in which the filter will execute.
     */
    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Applies filter effects to the given image
     *
     * @param Image\Image $image The image to filter.
     *
     * @return Image\Image The filtered image.
     *
     * @throws FilterException if an invalid mode has been given.
     */
    public function applyFilter(Image\Image $image)
    {
        switch ($this->mode) {
            case self::FLIP_HORIZONTAL:
                return $image->flip('h');
            case self::FLIP_VERTICAL:
                return $image->flip('v');
            case self::ROTATE_LEFT:
                return $image->rotate(90);
            case self::ROTATE_RIGHT:
                return $image->rotate(-90);
            default:
                throw new FilterException(
                    'De bewerking die u gekozen heeft bestaat niet.',
                    1
                );
        }
    }
}
