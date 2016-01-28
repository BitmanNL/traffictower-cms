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
 * @package   CMS\Language
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2013-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

$lang['email_must_be_array'] = 'De email validatie methode moet een array als argument krijgen.';
$lang['email_invalid_address'] = 'Ongeldig e-mailadres: %s';
$lang['email_attachment_missing'] = 'De volgende bijlage kon niet worden gevonden: %s';
$lang['email_attachment_unreadable'] = 'Kan deze bijlage niet openen: %s';
$lang['email_no_recipients'] = 'U moet ontvangers specificeren in: To, Cc, of Bcc';
$lang['email_send_failure_phpmail'] = 'Kan geen email verzenden met PHP mail(). Uw server is vermoedelijk niet ingesteld om deze methode te kunnen gebruiken.';
$lang['email_send_failure_sendmail'] = 'Kan geen email verzenden met PHP sendmail(). Uw server is vermoedelijk niet ingesteld om deze methode te kunnen gebruiken.';
$lang['email_send_failure_smtp'] = 'Kan geen email verzenden met PHP SMTP. Uw server is vermoedelijk niet ingesteld om deze methode te kunnen gebruiken.';
$lang['email_sent'] = 'Uw bericht is succesvol verstuurd met behulp van het protocol: %s';
$lang['email_no_socket'] = 'Kon geen socket openen voor Sendmail. Kijkt u alstublieft uw instellingen na.';
$lang['email_no_hostname'] = 'U heeft geen SMTP hostnaam opgegeven.';
$lang['email_smtp_error'] = 'De volgende SMTP fout is opgetreden: %s';
$lang['email_no_smtp_unpw'] = 'U moet een SMTP gebruikersnaam en wachtwoord opgeven.';
$lang['email_failed_smtp_login'] = 'Het versturen van het AUTH LOGIN commando is mislukt. Foutmelding: %s';
$lang['email_smtp_auth_un'] = 'Gebruikersnaam niet gevonden. Foutmelding: %s';
$lang['email_smtp_auth_pw'] = 'Wachtwoord niet gevonden. Foutmelding: %s';
$lang['email_smtp_data_failure'] = 'Kon data niet versturen: %s';
$lang['email_exit_status'] = 'Afsluit status code: %s';

// App email
$lang['email_template_dear'] = 'Beste';
$lang['email_template_sir_madam'] = 'heer/mevrouw';
$lang['email_template_yours_sincerely'] = 'Met vriendelijke groet';

?>
