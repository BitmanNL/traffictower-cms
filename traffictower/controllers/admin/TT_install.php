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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TT_install extends CI_Controller
{

	protected $allowed_ips;

	protected $data = array();

	public function __construct()
	{
		// Always turn on error reporting for the installation process.
		ini_set('display_errors', 1);

		parent::__construct();

		$this->config->load('maintenance', true);

		$this->allowed_ips = $this->config->item(
			'excluded_maintenance_ips',
			'maintenance'
		);

		if (!in_array($this->input->ip_address(), $this->allowed_ips))
		{
			show_error('Forbidden', 403);
		}
	}

	public function index()
	{
		$this->data['checks'] = array();

		// actual checks
		//$this->data['checks'][] = $this->_check_mod_rewrite();
		//$this->data['checks'][] = $this->_check_imagemagick();
		$this->data['checks'][] = $this->_check_environment();
		$this->data['checks'][] = $this->_check_error_reporting();
		$this->data['checks'][] = $this->_check_upload_folder_writable();
		$this->data['checks'][] = $this->_check_database_connection();
		$this->data['checks'][] = $this->_check_timezone();
		$this->data['checks'][] = $this->_check_db_php_times();
		$this->data['checks'][] = $this->_check_persona();
		$this->data['checks'][] = $this->_check_site_name();
		$this->data['checks'][] = $this->_check_email();
		$this->data['checks'][] = $this->_check_description();
		$this->data['checks'][] = $this->_check_piwik();
		$this->data['checks'][] = $this->_check_maximum_upload_size();
		$this->data['checks'][] = $this->_check_maximum_post_size();
		$this->data['checks'][] = $this->_check_php_version();
		$this->data['checks'][] = $this->_check_gd_library();
		$this->data['checks'][] = $this->_check_mb_string();
		$this->data['checks'][] = $this->_check_curl_library();
		$this->data['checks'][] = $this->_check_tmp_dir_writable();
		//$this->data['checks'][] = $this->_check_mod_expires();
		//$this->data['checks'][] = $this->_check_mod_headers();


		$successes = 0;
		$failures = 0;

		foreach ($this->data['checks'] as $check)
		{
			if ($check['success'])
			{
				$successes++;
			}
			else
			{
				$failures++;
			}
		}

		$this->data['success_perc'] = ceil(100 * $successes / ($successes + $failures));

		$this->_layout();
	}

	protected function _check_environment()
	{
		if (ENVIRONMENT === 'production')
		{
			return array('success' => TRUE, 'message' => 'ENVIRONMENT is ingesteld op production');
		}
		else
		{
			return array('success' => FALSE, 'message' => 'ENVIRONMENT is niet ingesteld op <u>production</u>', 'instruction' => 'De constante variabele ENVIRONMENT kan ingesteld worden in <code>/index.php</code> onder APPLICATION ENVIRONMENT');
		}
	}

	protected function _check_error_reporting()
	{
		if (error_reporting() == 0)
		{
			return array('success' => TRUE, 'message' => 'PHP en database error reporting staat uit');
		}
		else
		{
			return array('success' => FALSE, 'message' => 'PHP en database error reporting staat aan', 'instruction' => 'De error reporting kan ingesteld worden in <code>/index.php</code> onder ERROR REPORTING d.m.v. error_reporting(0)');
		}
	}

	protected function _check_database_connection()
	{
		$db = $this->load->database('default', TRUE);
		$this->load->dbutil();

		if (!$db->conn_id)
		{
			// geen mysql connection

			return array('success' => FALSE, 'message' => 'Geen verbinding met MySQL host <u>'.$this->db->hostname.'</u>', 'instruction' => 'Controlleer de database-gegevens in <code>/application/config/production/database.php</code>');
		}
		else if (!$this->dbutil->database_exists($this->db->database))
		{
			// database bestaat niet

			return array('success' => FALSE, 'message' => 'Database <u>'.$this->db->database.'</u> niet gevonden', 'instruction' => 'Controlleer de database-gegevens in <code>/application/config/production/database.php</code>');
		}
		else
		{

			return array('success' => TRUE, 'message' => 'Database <u>'.$this->db->database.'</u> met succes verbonden');
		}
	}

	protected function _check_persona()
	{
		$this->config->load('persona', TRUE);

		if ($this->config->item('host_base', 'persona') != '')
		{

			return array('success' => TRUE, 'message' => 'Persona host_base is ingesteld op <u>'.$this->config->item('host_base', 'persona').'</u>');
		}
		else
		{

			return array('success' => FALSE, 'message' => 'Persona host_base is niet ingesteld', 'instruction' => 'Set persona host_base in <code>/application/config/production/persona.php</code> (= domein zonder www)');
		}
	}

	protected function _check_piwik()
	{
		$this->config->load('piwik', TRUE);

		if ($this->config->item('tag_on', 'piwik'))
		{
			if($this->config->item('token', 'piwik') != '' && intval($this->config->item('site_id', 'piwik')) > 0)
			{
				return array('success' => TRUE, 'message' => 'Piwik is ingesteld met site_id <u>'. $this->config->item('site_id', 'piwik') .'</u> en token <u>'.$this->config->item('token', 'piwik').'</u>');
			}
			else
			{
				return array('success' => FALSE, 'type' => 'warning', 'message' => 'Piwik is niet ingesteld', 'instruction' => 'Set Piwik site_id en token in <code>/application/config/production/piwik.php</code>');
			}
		}

		return NULL;
	}

	protected function _check_site_name()
	{
		$this->load->database();

		if (!$this->db->table_exists('app_settings'))
		{
			return array('success' => FALSE, 'message' => 'Tabel app_settings is niet aanwezig.');
		}

		$this->load->model('shared/app_settings_model');
		$app_settings = $this->app_settings_model->get_app_settings();

		if (!empty($app_settings['site_name']) && $app_settings['site_name'] != 'Bitman')
		{

			return array('success' => TRUE, 'message' => 'Website naam is ingesteld op <u>'.$app_settings['site_name'].'</u>');
		}
		else
		{

			if ($app_settings['site_name'] == 'Bitman')
			{
				return array('success' => FALSE, 'message' => 'Website naam is ingesteld op <u>'.$app_settings['site_name'].'</u>', 'instruction' => 'Stel de website naam in het CMS onder <em>Site</em> > <em>Website instellingen</em>');
			}
			else
			{
				return array('success' => FALSE, 'message' => 'Website naam is niet ingesteld', 'instruction' => 'Stel de website naam in het CMS onder <em>Site</em> > <em>Website instellingen</em>');
			}
		}
	}

	protected function _check_email()
	{
		$this->load->database();

		if ($this->db->table_exists('app_settings'))
		{
			$this->load->model('shared/app_settings_model');
			$app_settings = $this->app_settings_model->get_app_settings();

			if (!empty($app_settings['email']) && !strstr($app_settings['email'], '@bitman.nl'))
			{
				return array('success' => TRUE, 'message' => 'E-mailadres is ingesteld op <u>'.$app_settings['email'].'</u>');
			}
			else
			{
				if (!empty($app_settings['email']))
				{
					return array('success' => FALSE, 'message' => 'E-mailadres is ingesteld op <u>'.$app_settings['email'].'</u>', 'instruction' => 'Stel het e-mailadres in het CMS onder <em>Site</em> > <em>Website instellingen</em>');
				}
				else
				{
					return array('success' => FALSE, 'message' => 'E-mailadres is niet ingesteld', 'instruction' => 'Stel het e-mailadres in het CMS onder <em>Site</em> > <em>Website instellingen</em>');
				}
			}
		}
	}

	protected function _check_description()
	{
		$this->load->database();

		if ($this->db->table_exists('app_settings'))
		{
			$this->load->model('shared/app_settings_model');
			$app_settings = $this->app_settings_model->get_app_settings();

			if (!empty($app_settings['description']) && $app_settings['description'] != 'Binnenkort in uw huiskamer!')
			{
				return array('success' => TRUE, 'message' => 'Hoofd meta description is ingesteld op <u>'.$app_settings['description'].'</u>');
			}
			else if(!empty($app_settings['description']))
			{
				return array('success' => FALSE, 'message' => 'Hoofd meta description is ingesteld op <u>'.$app_settings['description'].'</u>', 'instruction' => 'Stel de meta description in het CMS onder <em>Site</em> > <em>Website instellingen</em>');
			}
			else
			{
				return array('success' => FALSE, 'message' => 'Hoofd meta description is niet ingesteld', 'instruction' => 'Stel de meta description in het CMS onder <em>Site</em> > <em>Website instellingen</em>');
			}
		}
	}

	protected function _check_upload_folder_writable()
	{
		if (is_writable(FCPATH.'assets/upload'))
		{

			return array('success' => TRUE, 'message' => 'Upload-directory is schrijfbaar');
		}
		else
		{

			return array('success' => FALSE, 'message' => 'Upload-directory is niet schrijfbaar', 'instruction' => 'Maak de directory <code>/assets/upload</code> schrijfbaar met chmod 777');
		}
	}

	protected function _check_mod_rewrite()
	{
		if (getenv('HTTP_MOD_REWRITE') == 'On')
		{

			return array('success' => TRUE, 'message' => 'Mod_rewrite is geïnstalleerd op de server');
		}
		else
		{

			return array('success' => FALSE, 'message' => 'Mod_rewrite is niet geïnstalleerd op de server');
		}
	}

	protected function _check_imagemagick()
	{
		if (extension_loaded('imagick'))
		{

			return array('success' => TRUE, 'message' => 'Imagemagick is geïnstalleerd op de server');
		}
		else
		{

			return array('success' => FALSE, 'message' => 'Imagemagick is niet geïnstalleerd op de server');
		}
	}

	protected function _check_maximum_upload_size()
	{
		if( ini_get('upload_max_filesize') == "10M" || ini_get('upload_max_filesize') > 10 )
		{
			return array('success' => TRUE, 'message' => 'Maximum upload size ingesteld op ' . intval(ini_get('upload_max_filesize')) . ' mb');
		}
		else
		{
			return array('success' => FALSE, 'message' => 'Maximum upload size is minder dan 10MB', 'instruction' => 'Zet de waarde upload_max_filesize in php.ini op minimaal 10M of direct in PHP met <code>ini_set(\'upload_max_filesize\', \'10M\');</code>');
		}
	}

	protected function _check_maximum_post_size()
	{
		if( intval(ini_get('post_max_size')) >= intval(ini_get('upload_max_filesize')))
		{
			return array('success' => TRUE, 'message' => 'Maximum post size ingesteld op ' . intval(ini_get('post_max_size')) . ' mb');
		}
		else
		{
			return array('success' => FALSE, 'message' => 'Maximum post size (' . intval(ini_get('post_max_size')) . ' mb) is minder dan de maximum upload size (' . intval(ini_get('upload_max_filesize')) . ' mb)', 'instruction' => 'Zet de waarde post_max_size in php.ini op minimaal hetzelfde als upload_max_filesize of direct in PHP met <code>ini_set(\'post_max_size\', \'' . ini_get('upload_max_filesize') . '\');</code>');
		}
	}

	protected function _check_php_version()
	{
		preg_match("/(\d+)\.(\d+)\.(\d+)/", phpversion(), $phpVersion);

		if ($phpVersion[1] >= 5 && $phpVersion[2] >= 3)
		{
		    return array('success' => TRUE, 'message' => 'PHP heeft versie ' . phpversion());
		}
		else
		{
		    return array('success' => FALSE, 'message' => 'PHP heeft versie ' . phpversion(), 'instruction' => 'TrafficTower heeft minimaal PHP versie 5.3 nodig');
		}
	}

	protected function _check_gd_library()
	{
		if (function_exists('gd_info'))
		{
		    $gdInfo = gd_info();
		    return array('success' => TRUE, 'message' => 'GD image library aanwezig, versie ' . $gdInfo['GD Version']);
		}
		else
		{
		    return array('success' => FALSE, 'message' => 'GD image library niet aanwezig', 'instruction' => 'De bestandsmanager edit functionaliteit en sommige custom componenten hebben de GD Library nodig');
		}
	}

	protected function _check_mb_string()
	{
		if (function_exists('mb_get_info'))
		{
		    return array('success' => TRUE, 'message' => 'MB String Library aanwezig');
		}
		else
		{
		    return array('success' => FALSE, 'message' => 'MB String Library niet aanwezig', 'instruction' => 'De MB String Library is nodig om UTF-8 strings te kunnen manipuleren');
		}
	}

	protected function _check_curl_library()
	{
		if (function_exists('curl_version'))
		{
			$curlInfo = curl_version();
		    return array('success' => TRUE, 'message' => 'Curl Library aanwezig, versie ' . $curlInfo['version']);
		}
		else
		{
		    return array('success' => FALSE, 'message' => 'Curl Library niet aanwezig', 'instruction' => 'De Curl library wordt gebruikt voor Persona, SSO-login en het Twitterelement');
		}
	}

	protected function _check_tmp_dir_writable()
	{
		if (is_writable('/tmp'))
		{
			return array('success' => TRUE, 'message' => 'De tijdelijke systeemmap (/tmp) is schrijfbaar');
		}else
		{
			return array('success' => FALSE, 'message' => 'De tijdelijke systeemmap (/tmp) is niet schrijfbaar', 'instruction' => 'De /tmp map wordt soms gebruikt om tijdelijk bestanden in op te slaan. Als deze niet schrijfbaar is kan dit vreemde fouten opleveren');
		}
	}

	protected function _check_mod_expires()
	{
		$modules = apache_get_modules();

		if (in_array('mod_expires', $modules))
		{
			return array('success' => TRUE, 'message' => 'De Apache module <u>mod_expires</u> is geladen.');
		}else
		{
			return array('success' => FALSE, 'message' => 'De Apache module <u>mod_expires</u> is niet geladen', 'instruction' => 'De <u>mod_expires</u> module wordt gebruikt om caching headers in te stellen in .htaccess');
		}
	}

	protected function _check_mod_headers()
	{
		$modules = apache_get_modules();

		if (in_array('mod_headers', $modules))
		{
			return array('success' => TRUE, 'message' => 'De Apache module <u>mod_headers</u> is geladen.');
		}else
		{
			return array('success' => FALSE, 'message' => 'De Apache module <u>mod_headers</u> is niet geladen', 'instruction' => 'De <u>mod_headers</u> module wordt gebruikt om caching headers in te stellen in .htaccess');
		}
	}

	protected function _check_timezone()
	{
		$timezone = date_default_timezone_get();

		if ($timezone == 'Europe/Amsterdam')
		{
			return array('success' => TRUE, 'message' => 'PHP tijdzone is ingesteld op <u>'.$timezone.'</u>');
		}
		else
		{
			return array('success' => FALSE, 'message' => 'PHP tijdzone is ingesteld op <u>'.$timezone.'</u>');
		}
	}

	protected function _check_db_php_times()
	{
		// Check mysql and php timezones
		$db = $this->load->database('default', TRUE);
		$result = $this->db->query('SELECT NOW() AS timezone')->row_array();
		$db_time = $result['timezone'];
		$php_time = date('Y-m-d H:i:s');

		if ($php_time == $db_time)
		{
			return array('success' => TRUE, 'message' => 'Tijd is ingesteld op MySQL en PHP op <u>'.$php_time.'</u>');
		}
		else
		{
			return array('success' => FALSE, 'message' => 'Tijd is niet gelijk ingesteld op MySQL ('.$db_time.') en PHP ('.$php_time.')');
		}
	}

	protected function _layout()
	{
		$this->load->view('admin/install/index', $this->data);
	}

}

/* End of file install.php */
/* Location: ./application/controllers/admin/install.php */
