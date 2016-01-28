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
 * @package   CMS\Core
 * @author    Jeroen de Graaf
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

(defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require VENDORPATH . "bitmannl/codeigniter-modular-extensions/third_party/MX/Loader.php";

class TT_Loader extends MX_Loader
{

	/**
	 * @var array $tt_libraries List of TrafficTower libraries.
	 */
	protected $tt_libraries;

	/**
	 * @var array $app_libraries Memory if app library is schecked before.
	 */
	protected $app_libraries;

	/**
	 * @var array $tt_helpers List of TrafficTower helpers.
	 */
	protected $tt_helpers;

	/**
	 * @var array $app_helpers Memory if app helper is schecked before.
	 */
	protected $app_helpers;

	/**
	 * @var string $app_prefix Prefix for extended TT libraries.
	 */
	protected $app_prefix = 'TTCMS_';


	/**
	* Initialize the loader variables
	* MX versie overschrijven omdat in de testomgeving CI te vroeg wordt geladen
	**/
	public function initialize($controller = NULL) {
		new CI;
		/* set the module name */
		$this->_module = CI::$APP->router->fetch_module();

		if (is_a($controller, 'MX_Controller')) {

			/* reference to the module controller */
			$this->controller = $controller;

			/* references to ci loader variables */
			foreach (get_class_vars('CI_Loader') as $var => $val) {
				if ($var != '_ci_ob_level') {
					$this->$var =& CI::$APP->load->$var;
				}
			}

		} else {
			parent::initialize();

			/* autoload module items */
			$this->_autoloader(array());
		}

		/* add this module path to the loader variables */
		$this->_add_module_paths($this->_module);
	}

	/**
	 * Load repository method.
	 * Based on the load model.
	 *
	 * @param string The name of the repository class
  	 * @param string Name for the repository
  	 * @param bool Database connection
	 */
	public function repository($repository = '', $name = '', $db_conn = FALSE)
	{
		$this->model('../repositories/' . $repository, $name, $db_conn);
	}

	/**
	 * Load library, check for TrafficTower core library.
	 *
	 * @param string $library Library name
	 * @param array $params Optional library configuration
	 * @param string $object_name Optional library alias
	 * @return mixed Library instance
	 */
	public function library($library = '', $params = NULL, $object_name = NULL)
	{
		if (is_array($library))
		{
			return $this->libraries($library);
		}

		$tt_libraries = $this->_tt_libraries();

		// Check if is TT library
		$library = trim($library, '/');
		if (in_array(strtolower($library), $tt_libraries))
		{
			// First of all: still a check if it is overwritten

			// Condition 1: no subfolder or module libraries + condition 2: check if file exists
			if (strpos('/', $library) === FALSE && $this->_app_library_exists($library))
			{
				$app_object_name = !is_null($object_name) ? $object_name : strtolower($library);
				return parent::library($this->app_prefix . ucfirst($library), $params, $app_object_name);
			}

			// Not overwritten, use TT library
			$library = '../../traffictower/libraries/' . $library;
		}

		return parent::library($library, $params, $object_name);
	}

	/**
	 * Load helper, check TrafficTower core helpers.
	 *
	 * @param array $helper Helper name (or list of helpers)
	 */
	public function helper($helper = array())
	{
		if (is_array($helper))
		{
			return $this->helpers($helper);
		}

		$tt_helpers = $this->_tt_helpers();

		// Check if is TT helper
		$helper = trim($helper, '/');
		if (in_array(strtolower($helper), $tt_helpers))
		{
			// Check for app helper
			if (strpos('/', $helper) === FALSE && $this->_app_helper_exists($helper))
			{
				// Manual add (due to lowerize capitals in CI core)
				$app_helper = (strpos($helper, '.php') === FALSE) ? $helper . '_helper.php' : $helper;
				include_once APPPATH . 'helpers/' . $this->app_prefix . $app_helper;
			}

			$helper = '../../traffictower/helpers/' . $helper;
		}

		parent::helper($helper);
	}

	/**
	 * Single check if TT library is overwritten.
	 *
	 * @param string $library Library name
	 * @return boolean Library is overwritten
	 */
	protected function _app_library_exists($library)
	{
		$library = $this->app_prefix . ucfirst($library);

		$path = APPPATH . 'libraries/' . $library;
		if (strpos($path, '.php') === FALSE)
		{
			$path .= '.php';
		}

		if (is_null($this->app_libraries))
		{
			$this->app_libraries = array();
		}

		// Check if it is checked before
		if (!isset($this->app_libraries[$library]))
		{
			$this->app_libraries[$library] = file_exists($path);
		}

		return $this->app_libraries[$library];
	}

	/**
	 * Single check if TT helper is overwritten.
	 *
	 * @param string $helper Helper name
	 * @return boolean Helper is overwritten
	 */
	protected function _app_helper_exists($helper)
	{
		$helper = $this->app_prefix . $helper;

		$path = APPPATH . 'helpers/' . $helper;
		$path .= (strpos($path, '.php') === FALSE) ? '_helper.php' : '';

		if (is_null($this->app_helpers))
		{
			$this->app_helpers = array();
		}

		// Check if it is checked before
		if (!isset($this->app_helpers[$helper]))
		{
			$this->app_helpers[$helper] = file_exists($path);
		}

		return $this->app_helpers[$helper];
	}

	/**
	 * Get TrafficTower core libraries.
	 *
	 * @return array List of TrafficTower libraries
	 */
	protected function _tt_libraries()
	{
		if (is_null($this->tt_libraries))
		{
			$this->tt_libraries = array();

			$dir = FCPATH . 'traffictower/libraries';
			$scandir = scandir($dir);
			foreach ($scandir as $file)
			{
				$fileinfo = pathinfo($file);
				if ($fileinfo['extension'] === 'php')
				{
					$this->tt_libraries[] = strtolower($fileinfo['filename']);
				}
			}

		}

		return $this->tt_libraries;
	}

	/**
	 * Get TrafficTower core helpers.
	 *
	 * @return array List of TrafficTower helpers
	 */
	protected function _tt_helpers()
	{
		if (is_null($this->tt_helpers))
		{
			$this->tt_helpers = array();

			$dir = FCPATH . 'traffictower/helpers';
			$scandir = scandir($dir);
			foreach ($scandir as $file)
			{
				$fileinfo = pathinfo($file);
				if ($fileinfo['extension'] === 'php' && strpos($fileinfo['filename'], '_helper') !== FALSE)
				{
					$this->tt_helpers[] = strtolower(str_replace('_helper', '', $fileinfo['filename']));
				}
			}

		}

		return $this->tt_helpers;
	}

}
