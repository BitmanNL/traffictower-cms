<?php
/**
 * CodeIgniter CMS Session by database.
 *
 * @package   CMS\Core\Libraries\Session
 * @author    Bo-Yi Wu <appleboy.tw@gmail.com>
 * @author    Marko Martinović <marko@techytalk.info>
 * @author    Daan Porru
 * @author    Jeroen de Graaf
 * @copyright 2012-2014 Bo-Yi Wu
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class TT_Session
{
    protected $sess_expiration = '';
	protected $sess_garbage_collection_probability = '';
    protected $ci;
    protected $store = array();
    protected $flashdata_key = 'flash';

    /**
     * Constructor
     */
    public function __construct($config = array())
    {
        $this->ci = get_instance();

        $this->ci->load->helper('cookie');

        // needed for session update and delete
        $this->ci->load->database();

        // load configuration and merge with $config
        $this->_load_config($config);

        // garbage collection
        if (rand(1, $this->sess_garbage_collection_probability) === 1) {
            $this->_garbage_collection();
        }

        // start a session
        $this->sess_create();

        // Delete 'old' flashdata (from last request)
        $this->_flashdata_sweep();

        // Mark all new flashdata as old (data will be deleted before next request)
        $this->_flashdata_mark();
    }

    /**
     * Load configuration. Merge with $config.
     *
     * @param mixed $config Manually set config array, that overwrite config-file options
     */
     protected function _load_config($config)
     {
        $this->ci->load->config('session');

        $config = array_merge(
            array(
                'sess_expiration' => $this->ci->config->item('sess_expiration')
            ),
            $config
        );

        foreach ($config as $key => $val)
        {
            if (method_exists($this, 'set_'.$key))
            {
                $this->{'set_'.$key}($val);
            }
            elseif (isset($this->$key))
            {
                $this->$key = $val;
            }
        }

        // If the session expiration is set to zero,
        // we'll set the expiration two years from now.
        if ($this->sess_expiration == 0) {
            $this->sess_expiration = (60*60*24*365*2);
        }
    }

    /**
     * Delete all expired sessions.
     */
    protected function _garbage_collection()
    {
        $this->ci->db->where('updated_at <=', date('Y-m-d H:i:s', time() - $this->sess_expiration));
        $this->ci->db->delete($this->ci->config->item('sess_table_name'));
    }

    /**
     * Start a Session for this user
     */
    public function sess_create()
    {
        // Set the session length.
        $expire_time = time() + intval($this->sess_expiration);

        // get session id
        if (isset($_COOKIE[$this->ci->config->item('sess_cookie_name')])) {
            // get session data from the DB
            $session_data = $this->_get_session_data($_COOKIE[$this->ci->config->item('sess_cookie_name')]);
        }

        if (empty($session_data))
        {
            // create a new session-row in the DB
            $session_data = $this->_create_session_data();
        }

        // get session data
        $this->store = !empty($session_data) ? json_decode($session_data['data'], TRUE) : array();

        // save cookie with expire time for whole domain
        $cookie = array(
            'name'   => $this->ci->config->item('sess_cookie_name'),
            'value'  => $session_data['session_id'],
            'expire' => $expire_time
        );
        set_cookie($cookie);

        // update expire time session data store
        $this->store['session_id'] = $session_data['session_id'];
        $this->store['expire_at'] = $expire_time;
        $this->_update_session_data($this->store);
    }

    /**
     * Get session data from de database
     *
     * @param  integer $session_id
     * @return mixed Session-data-row
     */
    protected function _get_session_data($session_id)
    {
        // get session data
        $this->ci->db->where('session_id', $session_id);
        $this->ci->db->where('updated_at >', date('Y-m-d H:i:s', time() - $this->sess_expiration));
        $this->ci->db->order_by('updated_at');
        $this->ci->db->limit(1);
        $result = $this->ci->db->get($this->ci->config->item('sess_table_name'))->row_array();

        return $result;
    }

    /**
     * Create a session in the database
     *
     * @return mixed Session-data-row
     */
    protected function _create_session_data()
    {
       // new session
        $session_id = $this->_generate_session_id();

        // save empty session to database
        $sess_data['session_id'] = $session_id;
        $sess_data['data'] = json_encode(array());
        $sess_data['created_at'] = date('Y-m-d H:i:s');
        $this->ci->db->insert($this->ci->config->item('sess_table_name'), $sess_data);


        return $this->_get_session_data($session_id);
    }

     /**
     * Update a session in the database
     *
     * @param mixed $session_data Session data array
     */
    protected function _update_session_data($session_data)
    {
       // new session
        $session_id = $session_data['session_id'];

        // save empty session to database
        $sess_data['session_id'] = $session_id;
        $sess_data['data'] = json_encode($session_data);
        $this->ci->db->where('session_id', $session_id);
        $this->ci->db->update($this->ci->config->item('sess_table_name'), $sess_data);
    }

    /**
     * Delete a session in the database
     *
     * @return mixed Session-data-row
     */
    protected function _delete_session_data($session_id)
    {
        $this->ci->db->where('session_id', $session_id);
        $this->ci->db->delete($this->ci->config->item('sess_table_name'));
    }

    /**
     * Check if session is expired
     *
     * @return boolean
     */
    public function is_expired()
    {
        if ( ! isset($this->store['expire_at']))
        {
            return TRUE;
        }

        return (time() > $this->store['expire_at']);
    }

    /**
     * Destroy session
     *
     * @access  public
     */
    public function sess_destroy()
    {
        // remove old session cookie and db
        if (isset($this->store['session_id']))
        {
            $this->_delete_session_data($this->store['session_id']);

            // delete the session cookie
            delete_cookie($this->ci->config->item('sess_cookie_name'));
        }
    }

    /**
     * Get specific user data element
     */
    public function userdata($value)
    {
        if (isset($this->store[$value]))
        {
            return $this->store[$value];
        }
        else
        {
            return FALSE;
        }
    }

    /**
     * Set value for specific user data element
     *
     * @param   array  list of data to be stored
     * @param   object  value to be stored if only one element is passed
     */
    public function set_userdata($data = array(), $value = '')
    {
        if (is_string($data))
        {
            $data = array($data => $value);
        }

        foreach ($data as $key => $val)
        {
            $this->store[$key] = $val;
        }

        // save to db
        $this->_update_session_data($this->store);
    }

    /**
     * remove array value for specific user data element
     *
     * @param   array  list of data to be removed
     * @return void
     */
    public function unset_userdata($data = array())
    {
        if (is_string($data))
        {
            $data = array($data => '');
        }

        if (count($data) > 0)
        {
            foreach ($data as $key => $val)
            {
                unset($this->store[$key]);
            }
        }

        // save to db
        $this->_update_session_data($this->store);
    }

    /**
     * Fetch all session data
     *
     * @return array
     */
    public function all_userdata()
    {
        return $this->store;
    }

    /**
     * Add or change flashdata, only available
     * until the next request
     *
     * @param   mixed $newdata
     * @param   string $newval
     * @return void
     */
    public function set_flashdata($newdata = array(), $newval = '')
    {
        if (is_string($newdata))
        {
            $newdata = array($newdata => $newval);
        }

        if (count($newdata) > 0)
        {
            foreach ($newdata as $key => $val)
            {
                $flashdata_key = $this->flashdata_key.':new:'.$key;
                $this->set_userdata($flashdata_key, $val);
            }
        }
    }

    /**
     * Keeps existing flashdata available to next request.
     *
     * @param   string $key
     * @return void
     */
    public function keep_flashdata($key)
    {
        // 'old' flashdata gets removed.  Here we mark all
        // flashdata as 'new' to preserve it from _flashdata_sweep()
        // Note the function will return FALSE if the $key
        // provided cannot be found
        $old_flashdata_key = $this->flashdata_key.':old:'.$key;
        $value = $this->userdata($old_flashdata_key);

        $new_flashdata_key = $this->flashdata_key.':new:'.$key;
        $this->set_userdata($new_flashdata_key, $value);
    }

    /**
     * Fetch a specific flashdata item from the session array
     *
     * @param   string $key
     * @return string
     */
    public function flashdata($key)
    {
        $flashdata_key = $this->flashdata_key.':old:'.$key;
        return $this->userdata($flashdata_key);
    }

    /**
     * Identifies flashdata as 'old' for removal
     * when _flashdata_sweep() runs.
     *
     * @return void
     */
    private function _flashdata_mark()
    {
        $userdata = $this->all_userdata();

        foreach ($userdata as $name => $value)
        {
            $parts = explode(':new:', $name);

            if (is_array($parts) && count($parts) === 2)
            {
                $new_name = $this->flashdata_key.':old:'.$parts[1];
                $this->set_userdata($new_name, $value);
                $this->unset_userdata($name);
            }
        }
    }

    /**
     * Removes all flashdata marked as 'old'
     *
     * @return void
     */
    private function _flashdata_sweep()
    {
        $userdata = $this->all_userdata();
        foreach ($userdata as $key => $value)
        {
            if (strpos($key, ':old:'))
            {
                $this->unset_userdata($key);
            }
        }
    }

    private function _generate_session_id($count = 128)
    {
        $bytes = '';

        // OpenSSL slow on Win
        if(function_exists('openssl_random_pseudo_bytes') && (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))
        {
            $bytes = openssl_random_pseudo_bytes($count);
        }

        if($bytes === '' && @is_readable('/dev/urandom') && ($hRand = @fopen('/dev/urandom', 'rb')) !== FALSE)
        {
            $bytes = fread($hRand, $count);
            fclose($hRand);
        }

        if(strlen($bytes) < $count)
        {
            $bytes = '';

            if($this->randomState === NULL)
            {
                $this->randomState = microtime();
                if(function_exists('getmypid'))
                {
                    $this->randomState .= getmypid();
                }
            }

            for($i = 0; $i < $count; $i += 16)
            {
                $this->randomState = md5(microtime() . $this->randomState);

                if (PHP_VERSION >= '5')
                {
                    $bytes .= md5($this->randomState, true);
                }
                else
                {
                    $bytes .= pack('H*', md5($this->randomState));
                }
            }

            $bytes = substr($bytes, 0, $count);
        }

        return substr(hash('sha512', $bytes), 0, $count);
    }

}

/* End of file CMS_Session.php */
/* Location: ./application/libraries/CMS_Session.php */
