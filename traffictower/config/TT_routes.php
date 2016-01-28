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
 * @package   CMS\Core\Config
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Load custom routes for modules
| -------------------------------------------------------------------------
|
| Loads all routes.php per module from the application modules folder.
|
*/

if (glob(APPPATH . 'modules/*'))
{
	foreach (glob(APPPATH . 'modules/*') as $module_path)
	{
		$module_routes = FCPATH . $module_path . '/config/routes' . EXT;
		if (file_exists($module_routes))
		{
			require_once $module_routes;
		}
	}
}

/*
| -------------------------------------------------------------------------
| TrafficTower routing rules
| -------------------------------------------------------------------------
|
| Custom TrafficTower routing rules for all controllers in TrafficTower
| core (1), application controllers (2) and module controllers (3).
| This is needed to handle page by slug afterwards.
|
| Order of overwrite: TT controllers, application controllers, modules
| controllers.
|
*/

// 1.a. Route controllers in TrafficTower folder
if (glob(TTPATH . 'controllers/*' . EXT))
{
	foreach (glob(TTPATH . 'controllers/*' . EXT) as $controller_path)
	{
		$controller_name = basename($controller_path, EXT);
		$route[str_replace('TT_', '', $controller_name)] = 'traffictower/' . $controller_name . '/index';
	    $route[str_replace('TT_', '', $controller_name) . '/(.+)'] = 'traffictower/' . $controller_name . '/$1';
	}
}

// 1.b. Route controller in TrafficTower subfolder
if (glob(TTPATH . 'controllers/*/*' . EXT))
{
	foreach (glob(TTPATH . 'controllers/*/*' . EXT) as $controller_path)
	{
		$controller_folder_name = preg_replace('/.*\/(.*)/', '$1', dirname($controller_path));
		$controller_name = basename($controller_path, EXT);
		$route[$controller_folder_name] = 'traffictower/' . $controller_folder_name . '/' . $admin_default_controller;
		$route[$controller_folder_name . '/' . str_replace('TT_', '', $controller_name)] = 'traffictower/' . $controller_folder_name . '/' . $controller_name . '/index';
	    $route[$controller_folder_name . '/' . str_replace('TT_', '', $controller_name) . '/(.+)'] = 'traffictower/' . $controller_folder_name . '/' . $controller_name . '/$1';
	}
}

// 2.a. Route all existing controllers
if (glob(APPPATH . 'controllers/*' . EXT))
{
	foreach (glob(APPPATH . 'controllers/*' . EXT) as $controller_path)
	{
		$controller_name = basename($controller_path, EXT);
		$route[$controller_name] = $controller_name . '/index';
	    $route[$controller_name . '/(.+)'] = $controller_name . '/$1';
	}
}

// 2.b. Route controllers in subfolder
if (glob(APPPATH . 'controllers/*/*' . EXT))
{
	foreach (glob(APPPATH . 'controllers/*/*' . EXT) as $controller_path)
	{
		$controller_folder_name = preg_replace('/.*\/(.*)/', '$1', dirname($controller_path));
		$controller_name = basename($controller_path, EXT);
		$route[$controller_folder_name] = $controller_folder_name.'/'.$admin_default_controller;
		$route[$controller_folder_name . '/' . $controller_name] = $controller_folder_name . '/' . $controller_name . '/index';
	    $route[$controller_folder_name . '/' . $controller_name . '/(.+)'] = $controller_folder_name . '/' . $controller_name . '/$1';
	}
}

// 3. Route existing modules controllers
if (glob(APPPATH . 'modules/*/controllers/*' . EXT))
{
	foreach (glob(APPPATH .'modules/*/controllers/*' .EXT) as $controller_path)
	{
		$controller_name = basename($controller_path, EXT);
		$module_name = preg_replace('/.*\/(.*?)\/.*/', '$1', dirname($controller_path));

	    if ($controller_name === 'admin')
	    {
	    	// harde check omdat anders overlap in zit
	    	$route[$controller_name . '/' . $module_name] = $module_name . '/' . $controller_name . '/index';
	    	$route[$controller_name . '/' . $module_name.'/(.+)'] = $module_name . '/' . $controller_name . '/$1';
	    }
	    else
	    {
	    	// beschouw als gewone controller, met module name ervoor (hoeft niet mee)
	    	$route[$controller_name] = $controller_name . '/index';
	    	$route[$controller_name . '/(.+)'] = $controller_name . '/$1';

	    	// andere controllers mogelijk naast de hoofdcontroller met dezelfde naam als module
			$route[$module_name . '/' . $controller_name] = $module_name . '/' . $controller_name . '/index';
			$route[$module_name . '/' . $controller_name . '/(.+)'] = $module_name . '/' . $controller_name . '/$1';
	    }
	}
}

// Route other to page/view
$route['(:any)'] = 'page/show_page/$1';
