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
 * @package   CMS\Maintenance
 * @author    Jeroen de Graaf
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if (MAINTENANCE)
{
	// Make website available for preconfigured IPs.

	// CodeIgniter isn't available in this file so we have to load the
	// maintenance configuration manually.
	require APPPATH . 'config/maintenance.php';

	if (!in_array($_SERVER['REMOTE_ADDR'], $config['excluded_maintenance_ips']))
	{
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
		header('Retry-After: 300');
		require_once 'index.html';
		die;
	}
	else
	{
		require_once 'excluded_maintenance_ips.html';
	}
}
