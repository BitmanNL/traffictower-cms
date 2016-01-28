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
 * @package   CMS\Config
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// define the layouts the user can choose from
$config['admin_layouts'] = array(
							'default' => 'Standaard',
							'sidebar_left' => 'Zijbalk links',
							'sidebar_right' => 'Zijbalk rechts',
							'two_sidebars' => 'Twee zijbalken',
							'no_elements' => 'Geen elementen',
							'home' => 'Homepagina',
						   );

// define element positions per layout
$config['admin_element_positions'] = array(
									'default' => array(
										'content' => 'Inhoud'
									),
									'sidebar_left' => array(
										'sidebar' => 'Zijbalk',
										'content' => 'Inhoud hoofd'
									),
									'sidebar_right' => array(
										'content' => 'Inhoud hoofd',
										'sidebar' => 'Zijbalk'
									),
									'two_sidebars' => array(
										'sidebar_left' => 'Zijbalk links',
										'content' => 'Inhoud hoofd',
										'sidebar_right' => 'Zijbalk rechts'
									),
									'no_elements' => array(),
									'home' => array(
										'content' => 'Inhoud'
									)
						   		 );

// define custom friendly names for elements
$config['elements_friendly_names'] = array(
	'text' => 'Tekst',
	'image' => 'Afbeelding'
);

// define secondary navigations
$config['secondary_navigations'] = array(
								//'navigation_key' => 'Topnavigatie'
							);

// define custom types for text element (empty is no dropdown)
$config['element_text_types'] = array();
