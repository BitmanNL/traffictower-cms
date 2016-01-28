/**
 * TABLE STRUCTURE FOR: help_page.
 *
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
 * @package   CMS\Install
 * @author    Jeroen de Graaf
 * @author    Daan Porru
 * @copyright 2014-2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

DROP TABLE IF EXISTS `help_page`;

CREATE TABLE `help_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `controller` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_visible` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `date_created` datetime NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `module` (`module`),
  KEY `controller` (`controller`),
  KEY `is_visible` (`is_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO help_page (`id`, `parent_id`, `module`, `controller`, `title`, `content`, `user_id`, `is_visible`, `date_created`, `date_modified`) VALUES (1, 0, 'core', 'site', '2. Web instellingen', '<p>Het onderdeel web instellingen is de plaats voor algemene website gegevens als e-mailadres, website naam, omschrijving en afbeelding.</p>', 1, 'yes', '2014-01-17 13:48:33', '2014-03-28 12:12:36');
INSERT INTO help_page (`id`, `parent_id`, `module`, `controller`, `title`, `content`, `user_id`, `is_visible`, `date_created`, `date_modified`) VALUES (2, 0, 'core', '', '1. Welkom!', '<p>Welkom bij het TrafficTower CMS van Bitman!</p>', 1, 'yes', '2014-01-17 14:07:50', '2014-03-28 12:16:36');


#
# TABLE STRUCTURE FOR: help_paragraph
#

DROP TABLE IF EXISTS `help_paragraph`;

CREATE TABLE `help_paragraph` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_id` int(11) NOT NULL,
  `order` int(11) NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `popover_content` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_visible` enum('yes','no') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'no',
  `date_created` datetime NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `page_id` (`page_id`),
  KEY `order` (`order`),
  KEY `key` (`key`),
  KEY `is_visible` (`is_visible`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO help_paragraph (`id`, `page_id`, `order`, `key`, `title`, `content`, `popover_content`, `user_id`, `is_visible`, `date_created`, `date_modified`) VALUES (1, 1, 1, 'omschrijving', 'Omschrijving', '<p>De omschrijving wordt ingesteld in de <em>head</em> van de website. Deze omschrijving wordt onder andere gebruikt door Facebook en LinkedIn bij het delen van de URL. Daarnaast wordt de omschrijving gebruikt door Google in de zoekresultaten.</p>\n<p>De omschrijving is in te stellen voor de gehele website onder <em>website instellingen</em>, maar ook per pagina individueel. Indien niet op de pagina individueel een omschrijving is ingesteld, wordt de algemene omschrijving gebruikt.</p>\n<p>Er geldt een maximum van 150 tekens voor de omschrijving. HTML code en enters worden automatisch uit de tekst gefilterd.</p>\n<p><strong>Omschrijving en keywords m.b.t. zoekmachines:</strong><br />Tegenwoordig worden deze beide niet meer gebruikt in de zoekmachine ranking. Daarom zijn keywords niet meer nodig. De omschrijving wordt nog wel gebruikt voor weergaven in de zoekresultaten (<a href=\"http://googlewebmastercentral.blogspot.nl/2009/09/google-does-not-use-keywords-meta-tag.html\" target=\"_blank\">bron</a>).</p>', '<p>Default omschrijving, wordt ingesteld als OpenGraph omschrijving (zichtbaar op o.a. Facebook en LinkedIn)</p>', 1, 'yes', '2014-01-17 13:49:26', '2014-03-28 11:59:46');
INSERT INTO help_paragraph (`id`, `page_id`, `order`, `key`, `title`, `content`, `popover_content`, `user_id`, `is_visible`, `date_created`, `date_modified`) VALUES (2, 1, 2, 'afbeelding', 'Afbeelding', '<p>De afbeelding wordt ingesteld in de <em>head</em> van de website. Deze afbeelding wordt voornamelijk gebruikt door Facebook en LinkedIn bij het delen van de URL.</p>', '<p>Default afbeelding, wordt ingesteld als OpenGraph afbeelding (zichtbaar op o.a. Facebook en LinkedIn)</p>', 1, 'yes', '2014-01-17 13:49:49', '2014-03-28 12:10:30');
INSERT INTO help_paragraph (`id`, `page_id`, `order`, `key`, `title`, `content`, `popover_content`, `user_id`, `is_visible`, `date_created`, `date_modified`) VALUES (3, 1, 3, 'apple-touch-icon', 'Apple Touch icoon', '<p><a href=\"/file=assets/admin/help/iphone-screen.png\"><img style=\"float: right;\" src=\"/file=assets/admin/help/iphone-screen.png\" alt=\"\" width=\"30%\" /></a>De Apple Touch icoon wordt gebruikt als icoon op het <em>Bureaublad</em> van de iPad en iPhone indien de website gebookmarked wordt.</p>', '<p>Bureaublad-icoon voor op de iPad en iPhone. Restricties: PNG-formaat, hoogte-breedte gelijk (vierkant).</p>', 1, 'yes', '2014-01-17 13:50:09', '2014-03-28 12:09:14');


