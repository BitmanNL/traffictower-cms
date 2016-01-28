<?php defined('BASEPATH') OR exit('No direct script access allowed');

// Base URL to the Piwik Install
$config['piwik_url'] = 'http://stats.example.com';

// HTTPS Base URL to the Piwik Install (not required)
//$config['piwik_url_ssl'] = 'https://stats.example.com';

// Piwik Site ID for the website you want to retrieve stats for
$config['site_id'] = 0;

// Piwik API token, you can find this on the API page by going to the API link from the Piwik Dashboard
$config['token'] = '';

// Controls whether piwik_tag helper function outputs tracking tag (for production, set to TRUE)
$config['tag_on'] = TRUE;
