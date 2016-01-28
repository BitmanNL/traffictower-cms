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
 * @author    Jeroen de Graaf
 * @author    Aram Nap <aram@bitman.nl>
 * @copyright 2014-2016 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['tinymce_minimal'] = array(
    'selector' => 'textarea.mini-texteditor',
    'plugins' => array('advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'anchor', 'searchreplace', 'visualblocks', 'code', 'table', 'insertdatetime', 'media', 'paste', 'file-manager', 'loremipsum', 'wordcount', 'fullscreen'),
    'toolbar' => array('undo', 'redo', '|', 'pastetext', '|', 'bold', 'italic', 'underline', '|', 'styleselect', 'fontsizeselect', '|', 'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|', 'table', '|', 'bullist', 'numlist', 'outdent', 'indent', '|', 'link', 'insertfile', 'image', '|', 'loremipsum', '|', 'code', '|', 'fullscreen', 'searchreplace'),
    'menubar' => FALSE,
    'style_formats' => array(
        (object)array('title' => 'Kop 1', 'block' => 'h1'),
        (object)array('title' => 'Kop 2', 'block' => 'h3'),
        (object)array('title' => 'Kop 3', 'block' => 'h3'),
        (object)array('title' => 'Kop 4', 'block' => 'h4'),
        (object)array('title' => 'Normaal', 'block' => 'p')
    ),
    'fontsize_formats' => '12px 13px 14px 16px 18px 20px',
    'document_base_url' => rtrim(base_url(), '/'),
    'image_advtab' => TRUE,
    'relative_urls' => FALSE,
    'language' => 'nl',
    'paste_as_text' => TRUE,
    'content_css' => base_url('assets/admin/css/tinymce.css'),
    'entity_encoding' => 'raw',
    'file_browser_callback' => 'top.FileManager.prototype.browse'
);

$config['tinymce_full'] = array(
    'selector' => 'textarea.texteditor',
    'plugins' => array('advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'anchor', 'searchreplace', 'visualblocks', 'code', 'table', 'insertdatetime', 'media', 'paste', 'file-manager', 'loremipsum', 'wordcount', 'fullscreen'),
    'toolbar' => array('undo', 'redo', '|', 'pastetext', '|', 'bold', 'italic', 'underline', '|', 'styleselect', 'fontsizeselect', '|', 'alignleft', 'aligncenter', 'alignright', 'alignjustify', '|', 'table', '|', 'bullist', 'numlist', 'outdent', 'indent', '|', 'link', 'insertfile', 'image', '|', 'loremipsum', '|', 'code', '|', 'fullscreen', 'searchreplace'),
    'menubar' => FALSE,
    'style_formats' => array(
        (object)array('title' => 'Kop 1', 'block' => 'h1'),
        (object)array('title' => 'Kop 2', 'block' => 'h3'),
        (object)array('title' => 'Kop 3', 'block' => 'h3'),
        (object)array('title' => 'Kop 4', 'block' => 'h4')
    ),
    'fontsize_formats' => '12px 13px 14px 16px 18px 20px',
    'document_base_url' => rtrim(base_url(), '/'),
    'image_advtab' => TRUE,
    'relative_urls' => FALSE,
    'language' => 'nl',
    'paste_as_text' => TRUE,
    'content_css' => base_url('assets/admin/css/tinymce.css'),
    'entity_encoding' => 'raw',
    'file_browser_callback' => 'top.FileManager.prototype.browse'
);

/* End of file tinymce.php */
/* Location: ./application/config/tinymce.php */
