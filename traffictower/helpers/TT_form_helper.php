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
 * @package   CMS\Core\Helpers
 * @author    Jeroen de Graaf
 * @copyright 2015 Bitman
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

/**
 * Flash the error messages of the validation helper
 * So we can redirect the user and access the error-
 * messages.
 */
if(!function_exists('flash_error_messages'))
{
    function flash_error_messages()
    {
        if (FALSE === ($OBJ =& _get_validation_object()))
        {
            show_error('No validation object found, can not flash error messages!');
        }

        $messages = $OBJ->get_errors();
        $CI =& get_instance();

        if (!isset($CI->session))
        {
            show_error('No session object found, can not flash error messages!');
        }


        $CI->session->set_flashdata('validation_error_messages', $messages);
        $CI->session->set_flashdata('validation_post_array', $CI->input->post());
    }
}

/**
 * Returns the error for a specific form field from a session.
 * This is an altered helper for the form validation class.
 *
 * @access  public
 * @param   string $field form-veld waarvoor de errormessage moet worden opgehaald
 * @param   string $prefix string om voor de errormessage weer te geven
 * @param   string $suffix string om na de errormessage weet te geven
 *
 * @return  string errormessage voor gegeven veld
 */
if(!function_exists('cms_form_error'))
{
    function cms_form_error($field = '', $prefix = '', $suffix = '')
    {
        $error_array = _cms_get_error_array();

        if (is_array($error_array) AND isset($error_array[$field]))
        {
            return $prefix . $error_array[$field] . $suffix;
        }
        else
        {
            return '';
        }
    }
}

/**
 * Returns all the errors associated with a form submission.
 * This is an altered helper function for the form validation class.
 *
 * @access  public
 * @param   string $prefix
 * @param   string $suffix
 *
 * @return  string Alle errormessages van het hele formulier
 */
if(!function_exists('cms_validation_errors'))
{
    function cms_validation_errors($prefix = '', $suffix = '')
    {
        $error_array = _cms_get_error_array();

        if (!is_array($error_array))
        {
            return '';
        }

        // Generate the error string
        $str = '';
        foreach ($error_array as $val)
        {
            if ($val != '')
            {
                $str .= $prefix.$val.$suffix."\n";
            }
        }

        return $str;
    }
}

/**
 * Grabs a value from the session for the specified field so you can
 * re-populate an input field or textarea.  This is an altered version
 * of the form helper set_value
 *
 * @access  public
 * @param   string $field
 * @param   string $default
 *
 * @return  mixed
 */
if(!function_exists('cms_set_value'))
{
    function cms_set_value($field = '', $default = '')
    {
        $post_array = _cms_get_post_array();

        $response = $default;

        if (isset($post_array[$field]))
        {
            $response = $post_array[$field];
        }

        return htmlspecialchars($response);
    }
}

/**
 * Grabs a value from the POST array for the specified field so you can
 * re-populate an input field or textarea.  If Form Validation
 * is active it retrieves the info from the validation class
 * Does not use the form_prep function so HTML can be used.
 *
 * @access  public
 * @param   string $field
 * @param   string $default
 * @return  mixed
 */
if(!function_exists('set_value_html'))
{
    function set_value_html($field = '', $default = '')
    {
        if ( ! isset($_POST[$field]))
        {
            return $default;
        }

        return $_POST[$field];
    }
}

/**
 * Grabs a value from the session for the specified field so you can
 * re-populate an input field or textarea.
 * Doesn't perform the htmlspecialchars function.
 * This is an altered version of the form helper set_value_html
 *
 * @access  public
 * @param   string $field
 * @param   string $default
 * @return  mixed
 */
if(!function_exists('cms_set_value_html'))
{
    function cms_set_value_html($field = '', $default = '')
    {
        $post_array = _cms_get_post_array();

        $response = $default;

        if (isset($post_array[$field]))
        {
            $response = $post_array[$field];
        }

        return $response;
    }
}

/**
 * Let's you set the selected value of a checkbox via the value in the session.
 * This is an altered versionof the form helper set_checkbox
 *
 * @access  public
 * @param   string $field
 * @param   string $value
 * @param   bool $default
 * @return  string
 */
if(!function_exists('cms_set_checkbox'))
{
    function cms_set_checkbox($field = '', $value = '', $default = FALSE)
    {
        $post_array = _cms_get_post_array();

        if ( ! isset($post_array[$field]))
        {
            if (!$post_array AND $default == TRUE)
            {
                return ' checked="checked"';
            }
            return '';
        }

        $field = $post_array[$field];

        if (is_array($field))
        {
            if ( ! in_array($value, $field))
            {
                return '';
            }
        }
        else
        {
            if (($field == '' OR $value == '') OR ($field != $value))
            {
                return '';
            }
        }

        return ' checked="checked"';
    }
}

/**
 * Let's you set the selected value of a radio field via info in the session.
 * This is an altered versionof the form helper set_radio
 *
 * @access  public
 * @param   string $field
 * @param   string $value
 * @param   bool $default
 * @return  string
 */
if(!function_exists('cms_set_radio'))
{
    function cms_set_radio($field = '', $value = '', $default = FALSE)
    {
        $post_array = _cms_get_post_array();

        if ( ! isset($post_array[$field]))
        {
            if (!$post_array AND $default == TRUE)
            {
                return ' checked="checked"';
            }
            return '';
        }

        $field = $post_array[$field];

        if (is_array($field))
        {
            if ( ! in_array($value, $field))
            {
                return '';
            }
        }
        else
        {
            if (($field == '' OR $value == '') OR ($field != $value))
            {
                return '';
            }
        }

        return ' checked="checked"';
    }
}

/**
 * Let's you set the selected value of a <select> menu via data in the POST array.
 * If Form Validation is active it retrieves the info from the validation class
 *
 * @access  public
 * @param   string $field
 * @param   string $value
 * @param   bool $default
 * @return  string
 */
if(!function_exists('cms_set_select'))
{
    function cms_set_select($field = '', $value = '', $default = FALSE)
    {
        $post_array = _cms_get_post_array();

        if ( ! isset($post_array[$field]))
        {
            if (count($post_array) === 0 AND $default == TRUE)
            {
                return ' selected="selected"';
            }
            return '';
        }

        $field = $post_array[$field];

        if (is_array($field))
        {
            if ( ! in_array($value, $field))
            {
                return '';
            }
        }
        else
        {
            if (($field == '' OR $value == '') OR ($field != $value))
            {
                return '';
            }
        }

        return ' selected="selected"';
    }
}

// ------------------------------------------------------------------------


/**
 * Geef een post_array terug. Als deze in de flashdata zit, wordt die gebruikt.
 * Anders de daadwerkelijke post-velden.
 *
 * @return array post-velden
 */
if(!function_exists('_cms_get_post_array'))
{
    function _cms_get_post_array()
    {
        $CI =& get_instance();

        if (isset($CI->session))
        {
            $post_array = $CI->session->flashdata('validation_post_array');
        }

        // if not in flashdata, then try postdata (no redirect on failure)
        if(!isset($post_array) || $post_array === FALSE)
        {
            $post_array = ($CI->input->post() !== FALSE) ? $CI->input->post() : array();
        }

        return $post_array;
    }
}

/**
 * Geef een array terug met alle form-error messages.
 * Messages worden uit de flashdata gehaald indien aanwezig, anders uit de
 * formvalidator
 *
 * @return array error messages
 */
if(!function_exists('_cms_get_error_array'))
{
    function _cms_get_error_array()
    {
        $CI =& get_instance();

        if (isset($CI->session))
        {
            $error_array = $CI->session->flashdata('validation_error_messages');
        }

        // if not in flashdata, then try directly from validation object (no redirect on failure)
        if(!isset($error_array) || $error_array === FALSE)
        {
            if (FALSE === ($OBJ =& _get_validation_object()))
            {
                $error_array = array();
            }
            else
            {
                $error_array = $OBJ->get_errors();
            }
        }

        return $error_array;
    }
}

/**
 * Determines what the form validation class was instantiated as, fetches
 * the object and returns it.
 *
 * @access  private
 * @return  mixed
 */
if ( ! function_exists('_get_validation_object'))
{
    function &_get_validation_object()
    {
        $CI =& get_instance();

        // We set this as a variable since we're returning by reference.
        $return = FALSE;

        if (FALSE !== ($object = $CI->load->is_loaded('form_validation')))
        {
            if ( ! isset($CI->$object) OR ! is_object($CI->$object))
            {
                return $return;
            }

            return $CI->$object;
        }

        return $return;
    }
}
