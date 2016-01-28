/**
 * File manager plugin for CodeIgniter applications using TinyMCE.
 *
 * Requires at least PHP 5.3.6 (for DirectoryIterator::getExtension()). This
 * file just loads the required files. The connection between the TinyMCE and
 * the FileManager object is set in the init file_browser_callback
 * configuration. Example:
 * tinymce.init(
 *     {
 *         ...
 *         file_browser_callback: top.FileManager.prototype.getInstance().browse
 *         ...
 *     }
 * );
 *
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
 * @author    Aram Nap
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

'use strict';

if (typeof top.tinymce === 'object') {
    top.tinymce.PluginManager.add('file-manager', function(editor, url) {
        //var head = document.getElementsByTagName('head')[0];
        var body = top.document.getElementsByTagName('body')[0];


        // Load jQuery if it's not already available.
        if (typeof $ === 'undefined') {
            var script = top.document.createElement('script');
            script.type = 'text/javascript';
            script.src = '//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js';

            body.appendChild(script);
        }
    });
}
