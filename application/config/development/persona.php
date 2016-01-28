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
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Configuratie voor de inlogdienst van Mozilla
 */

// Basis voor de hostname waarmee de site te bereiken is.
// bv. bitman.nl als de site via www.bitman.nl of bitman.nl
// te bereiken is.
// Dit wordt gebruikt om te checken of de $_SERVER['HOST'] niet
// gemanipuleerd is door de client, anders is het mogelijk om
// op accounts in te loggen zonder via onze site aan te melden.
$config['host_base'] = '';
