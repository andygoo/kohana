<?php defined('SYSPATH') or die('No direct script access.');

class Session {
    protected static $instance;
    protected $_name = 'session';
    protected $_data = array();
    protected $_destroyed = FALSE;

    public static function instance() {
        if (!isset(Session::$instance)) {
            Session::$instance = $session = new Session();
            register_shutdown_function(array($session, 'write'));
        }
        
        return Session::$instance;
    }

    public function __construct() {
        $data = Cookie::get($this->_name, NULL);
        $data = base64_decode($data);
        $data = unserialize($data);
        
        if (is_array($data)) {
            $this->_data = $data;
        }
    }

    public function __toString() {
        $data = serialize($this->_data);
        $data = base64_encode($data);
        return $data;
    }

    public function get($key, $default = NULL) {
        return array_key_exists($key, $this->_data) ? $this->_data[$key] : $default;
    }

    public function set($key, $value) {
        $this->_data[$key] = $value;
        return $this;
    }

    public function delete($key) {
        $args = func_get_args();
        foreach($args as $key) {
            unset($this->_data[$key]);
        }
        return $this;
    }

    public function write() {
        if (headers_sent() or $this->_destroyed) {
            return FALSE;
        }
        
        $this->_data['last_active'] = time();
        return Cookie::set($this->_name, $this->__toString(), 0);
    }

    public function destroy() {
        if ($this->_destroyed === FALSE) {
            $this->_destroyed = Cookie::delete($this->_name);
            if ($this->_destroyed) {
                $this->_data = array();
            }
        }
        
        return $this->_destroyed;
    }
}