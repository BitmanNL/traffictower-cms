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

/**
 * Format a size, in bytes, in a human friendly manner.
 *
 * @param number size The size to format in number of bytes.
 * @param int precision The amount of numbers after the decimal separator to
 *                      return.
 * @param bool si Return the size with a SI prefix, from kilo to tera, when true
 *                or with a binary prefix, from kibi to tebi.
 *
 * @return string The size formatted in the nearest unit starting from kilobyte
 *                up to terabyte.
 *
 * @throws string if the first argument is less than zero.
 * @throws string if the second argument is less than zero.
 */
function formatBytes(size, precision, si)
{
    if (size < 0) {
        throw 'formatBytes() requires the first argument to be zero or higher.';
    }

    if (precision === undefined) {
        precision = 1;
    } else if (precision < 0) {
        throw 'formatBytes() requires the second argument to be zero or higher.';
    }

    if (si === undefined) {
        si = false;
    }

    if (si) {
        suffixes = ['', 'Ki', 'Mi', 'Gi', 'Ti'];
    } else {
        suffixes = ['', 'k', 'M', 'G', 'T'];
    }

    var suffixesLength = suffixes.length;

    // Find the highest suitable unit.
    var step = 0;
    var unitSize = size;

    if (si) {
        var base = 1024;
    } else {
        var base = 1000;
    }

    while ((unitSize /= base) >= 1.0) {
        // Stop when there are no higher units available.
        if ((step + 1) === suffixesLength) {
            break;
        }

        ++step;
    }

    if (step === 0) {
        return size + ' ' + suffixes[step] + 'B';
    }

    if (si) {
        var rate = Math.pow(2, 10 * step);
    } else {
        var rate = Math.pow(10, 3 * step);
    }

    return (
        Math.round(
            (size / rate) * Math.pow(10, precision)
        ) / Math.pow(10, precision)
    ) + ' ' + suffixes[step] + 'B';
}
