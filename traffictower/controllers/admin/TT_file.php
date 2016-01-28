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
 * @package   CMS\Core\Admin
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2013-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Adds a file manager view to admin.
 */
class TT_file extends Admin_controller
{
    /**
     * @var array $methods An array of overloaded methods.
     */
    private $methods;

    /**
     * Set overloaded methods.
     *
     * @param string $name The name of the method to set.
     * @param callable $method The method to set.
     *
     * @return void
     */
    public function set_method($name, callable $method)
    {
        $this->methods[$name] = $method;
    }

    /**
     * Call overloaded methods.
     *
     * @param string $name The name of the method to set.
     * @param array $arguments The arguments to pass to the method.
     *
     * @return void
     *
     * @throws BadMethodCallException if an overloaded method is called which
     *                                has not been previously set.
     */
    public function __call($name, $arguments)
    {
        if (isset($this->methods[$name])) {
            $this->methods[$name]();
        } else {
            throw new BadMethodCallException(
                'Call to undefined method ' . __CLASS__ . '::' . $name . '().'
            );
        }
    }

    /**
     * Display the file manager.
     *
     * @return void
     */
    public function index()
    {
        $this->views['content'] = $this->load->view(
            'admin/file/index',
            null,
            true
        );

        $this->javascript_files[] = asset_url(
            'assets/admin/grocery_crud/texteditor/tinymce4/plugins/file-manager/js/file-manager.min.js'
        );

        $this->javascript[] = $this->load->view(
            'admin/file/js/file-manager.js',
            null,
            true
        );

        $this->css[] = $this->load->view(
            'admin/file/css/index.css',
            null,
            true
        );

        $this->_layout();
    }
}
