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
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * The first page of admin, with dashboard (Piwik).
 */
class TT_home extends Admin_controller
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Index
	 *
	 * The main method for your controller. In this example is
	 * shown how to load the grocerycrud object, css and javascript for
	 * your controller.
	 *
	 * Base other methods for your controller on this design.
	 */
	public function index()
	{
		$this->load->model('shared/app_settings_model');
		$data['site_name'] = $this->app_settings_model->get('site_name');

		// Load dashboard panels
		$panels = array();
		$panels = array_merge($panels, $this->_panel_piwik());
		$panels[] = $this->_panel_logs();

		// Add panels to output
		$data['dashboard_panels'] = $this->_panels($panels);

		// Output
		$this->views['content'] = $this->load->view('admin/home/index', $data, TRUE);
		$this->_layout();
	}

	/**
	 * Divide all given panels to a 2 columns output array.
	 *
	 * @param array $panels Panels
	 * @return array 2 column array of HTML panels
	 */
	protected function _panels($panels)
	{
		$dashboard_panels = array();

		$tab = 'odd';
		foreach ($panels as $panel)
		{
			if (!isset($dashboard_panels[$tab]))
			{
				$dashboard_panels[$tab] = '';
			}
			$dashboard_panels[$tab] .= $panel;

			$tab = ($tab == 'odd') ? 'even' : 'odd';
		}

		return $dashboard_panels;
	}

	/**
	 * Add Piwik statistics to dashboard panels.
	 *
	 * @return string Piwik panels
	 */
	protected function _panel_piwik()
	{
		$this->load->config('piwik');

		$panels = array();
		if ($this->config->item('tag_on'))
		{
			$this->load->library('piwik');

			// visits
	        $unique = $this->piwik->unique_visitors('day', 30);

	        if (!is_null($unique))
	        {
		        foreach($unique as $date => $visit)
		        {
		            $date_arr = explode('-', $date);
		            $year = $date_arr[0];
		            $month = $date_arr[1];
		            $day = $date_arr[2];

		            $utc = mktime(date('h') + 1, NULL, NULL, $month, $day, $year) * 1000;

		            $flot_visits[] = array($date, $visit);
		        }

		        $data['visits'] = json_encode($flot_visits);

		        // top pages
		        $data['top_pages'] = $this->piwik->page_titles('range', '1');

		        $data['websites'] = $this->piwik->websites('range', '1');

		        $this->load->config('piwik');
		        $data['piwik_url'] = $this->config->item('piwik_url');
		        $data['site_id'] = $this->config->item('site_id');

		        $this->css_files[] = asset_url('assets/admin/css/jquery.jqplot.min.css');
		        $this->javascript_files[] = asset_url('assets/admin/js/jquery.jqplot.min.js');
		        $this->javascript_files[] = asset_url('assets/admin/js/jqplot.dateAxisRenderer.min.js');

		        $this->javascript[] = $this->load->view('admin/home/panels/js/piwik.js', $data, TRUE);

				$panels[] = $this->load->view('admin/home/panels/piwik_graphic', NULL, TRUE);
				$panels[] = $this->load->view('admin/home/panels/piwik_top_pages', NULL, TRUE);
				$panels[] = $this->load->view('admin/home/panels/piwik_top_websites', NULL, TRUE);
			}
			else
			{
				$panels[] = $this->load->view('admin/home/panels/piwik_no_connection', NULL, TRUE);

				$this->load->library('app_log');
				$this->app_log->log('piwik_no_connection', 'Statistieken server '.$this->config->item('piwik_url').' onbereikbaar');
			}
    	}

    	return $panels;
	}

	/**
	 * Get log panel.
	 *
	 * @return string Log panel
	 */
	protected function _panel_logs()
	{
		$this->load->model('shared/log_model');
		$this->load->model('shared/user_model');

		$data['logs'] = $this->log_model->get_logs(array(
			'order_field' => 'date_created',
			'order' => 'desc',
			'limit' => 10
		));

		foreach ($data['logs'] as $key => $log)
		{
			$data['logs'][$key]['user'] = $this->user_model->get_user($log['user_id'], TRUE);
		}

		return $this->load->view('admin/home/panels/logs', $data, TRUE);
	}

}
