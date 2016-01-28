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
 * @package   CMS\Core\Libraries
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Event
{

	/**
	 * @var  object $ci CodeIgniter instance
	 */
	protected $ci;

	/**
	 * @var array $listeners Registered event listeners
	 */
	protected $listeners = array();

	/**
	 * Constructor. Loads CodeIgniter instance.
	 */
	public function __construct()
	{
		$this->ci =& get_instance();
	}

	/**
	 * Trigger an event. Run all listeners.
	 *
	 * @param  string $event  Trigger event name
	 * @param  array  $params Parameters (optional)
	 */
	public function trigger($event, $params = array())
	{
		$this->_load_config(APPPATH.'events/');

		if (isset($this->listeners[$event]))
		{
			foreach ($this->listeners[$event] as $library => $method)
			{
				$this->ci->load->library($library);
				$this->ci->{basename($library)}->{$method}($params);
			}
		}
	}

	/**
	 * Load all event config (scan all files) if not loaded already.
	 */
	protected function _load_config($dir)
	{
		if (empty($this->listeners))
		{
			$files = scandir($dir);
			foreach($files as $file)
			{
				if (pathinfo($file, PATHINFO_EXTENSION) === 'php')
				{
					$this->_load_file($dir.$file);
				}
			}
		}
	}

	/**
	 * Load event file and register.
	 *
	 * @param  string $file File path
	 */
	protected function _load_file($file)
	{
		require_once $file;

		if (isset($events))
		{
			$this->_register_events($events);
		}
	}

	/**
	 * Register events: insert events to the listener.
	 *
	 * @param  array $events Events to add to the listener
	 */
	protected function _register_events($events)
	{
		if (isset($events['library']) && !empty($events['library']) && isset($events['events']) && is_array($events['events']))
		{
			foreach ($events['events'] as $event => $method)
			{
				$this->listeners[$event][$events['library']] = $method;
			}
		}
	}

}

/* End of file Event.php */
/* Location: ./application/libraries/Event.php */
