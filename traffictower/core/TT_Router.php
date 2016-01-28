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

/* load the MX_Router class */
require VENDORPATH . "bitmannl/codeigniter-modular-extensions/third_party/MX/Router.php";

class TT_Router extends MX_Router
{

	public function _parse_routes()
	{
		if (in_array($this->uri->segments[0], $this->config->config['languages']))
		{
			$this->uri->uri_string = substr($this->uri->uri_string, (strlen($this->uri->segments[0])+1));
			$this->config->config['language'] = array_shift($this->uri->segments);
		}

		parent::_parse_routes();
	}

}
