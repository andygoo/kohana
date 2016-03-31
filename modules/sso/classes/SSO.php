<?php

class SSO {
    private static $sso = NULL;
    private $_config;

    public static function init() {
        if (self::$sso !== NULL) return;
        
        self::$sso = new self();
        self::$sso->_config = Kohana::config('sso');
    }

    public static function __callStatic($method, $args) {
        $key = self::$sso->_config['key'];
        $token = md5(uniqid(rand(), true));
        $checksum = self::get_checksum($key, $token);
        
        $ret = array();
        $sites = self::$sso->_config['sites'];
        foreach($sites as $url) {
            $ret[] = "{$url}{$method}/{$key}/{$token}/{$checksum}";
        }
        return $ret;
    }

    public static function check($broker, $token, $checksum) {
        $_checksum = SSO::get_checksum($broker, $token);
        if ($_checksum != $checksum) {
            return false;
        }
        return true;
    }

    private static function get_checksum($broker, $token, $clientip = null) {
        if ($clientip === null) {
            $clientip = Arr::get($_SERVER, 'REMOTE_ADDR');
        }
        $password = self::$sso->_config['password'];
        
        return sha1("{$token}{$clientip}{$password}");
    }

    protected static $_instance;

    public static function instance() {
        if (empty(SSO::$_instance)) {
            $config = Kohana::$config->load('sso');
            SSO::$_instance = new SSO($config);
        }
        
        return SSO::$_instance;
    }
    
    protected $_session;
    protected $_user_key = 'auth_user';
    protected $_driver_key = 'auth_driver';
    protected $_autologin_key = 'auth_auto_login';
    protected $_forced_key = 'auth_forced';

    protected $_drivers = array();

    protected function __construct($config = NULL) {
        $this->_config = $config;
        $this->_session = Session::instance();
    }

    public function get_user($refresh = FALSE) {
        $driver = $this->_session->get($this->_driver_key);
        if (!$driver and $this->_session->get($this->_forced_key) !== TRUE) {
            if (!$this->auto_login()) {
                return FALSE;
            }
        }
        
        $user = $this->_session->get($this->_user_key);
        if ($user) {
            if ($refresh) {
                $user = $this->orm()->get_user($user);
                $this->_session->set($this->_user_key, $user);
            }
            return $user;
        }
        
        return $this->driver($driver)->get_user();
    }

    public function login() {
        $this->logout();
        
        $params = func_get_args();
        $driver_name = array_shift($params);
        $driver = $this->driver($driver_name);
        $user = call_user_func_array(array($driver, 'login'), $params);
        if ($user) {
            $this->_complete_login($user, $driver_name);
            // check for autologin option
            $remember = $this->_config['lifetime'] > 0;
            if ($remember) {
                $token = $this->orm()->generate_token($user, $driver_name, $this->_config['lifetime']);
                Cookie::set($this->_autologin_key, $token->token);
            }
            return TRUE;
        }
        
        return FALSE;
    }

    public function force_login($user, $mark_as_forced = TRUE) {
        $user = $this->orm()->get_user($user);
        if (!$user) {
            return FALSE;
        }
        
        $this->_complete_login($user, NULL);
        
        if ($mark_as_forced) {
            $this->_session->set($this->_forced_key, TRUE);
        }
        
        return TRUE;
    }

    public function auto_login() {
        $token = Cookie::get($this->_autologin_key);
        if (!$token) {
            return FALSE;
        }
        
        $token_arr = $this->orm()->get_token($token);
        if (Token::is_valid($token_arr)) {
            // its a valid token
            $this->_complete_login($token->user, $token->driver);
            Token::generate($this->_config['lifetime']);
            Cookie::set($this->_autologin_key, $token_arr['token']);
            return $token['user'];
        } else {
            // delete cookie
            Cookie::delete($this->_autologin_key);
        }
        
        return FALSE;
    }

    public function logout() {
        $driver = $this->_session->get($this->_driver_key);
        if (!$driver) {
            return TRUE;
        }
        
        $this->driver($driver)->logout();
        $token = Cookie::get($this->_autologin_key);
        if ($token) {
            $this->orm()->delete_token($token);
            Cookie::delete($this->_autologin_key);
        }
        
        $this->_session->delete($this->_user_key)->delete($this->_driver_key)->delete($this->_forced_key);
    }

    public function driver($name = NULL) {
        if ($name === NULL and !$name = $this->_session->get($this->_driver_key)) {
            throw new SSO_Exception('SSO driver name required');
        }
        // OAuth.Google will be a OAuth_Google driver
        $name = str_replace('.', '_', $name);
        if (!isset($this->_drivers[$name])) {
            $class = 'SSO_Driver_' . $name;
            $driver = new $class($this);
            $driver->init();
            $this->_drivers[$name] = $driver;
        }
        
        return $this->_drivers[$name];
    }

    protected function _complete_login($user, $driver = NULL) {
        $this->_session->set($this->_driver_key, $driver);
        $this->_session->set($this->_user_key, $user);
    }
    
}
