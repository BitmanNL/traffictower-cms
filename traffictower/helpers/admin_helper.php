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
 * @package   CMS\Core\Helpers
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if(!function_exists('get_dir_folders'))
{
	function get_dir_folders($dir)
	{
		$root_folder = rtrim($dir, '/') . '/';
		$scandir = scandir($root_folder);

		$folders = array();

		foreach ($scandir as $folder)
		{
			if (substr($folder, 0, 1) !== '.' && is_dir($root_folder.$folder))
			{
				$folders[] = $folder;
			}
		}

		return $folders;
	}
}
