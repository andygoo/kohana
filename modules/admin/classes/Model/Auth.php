<?php

class Model_Auth extends Model {
    protected $_table_name = 'admin';
    protected $_primary_key = 'id';
    protected $_session;
    protected $_session_key = 'auth_user!';

    public function __construct() {
        parent::__construct();
        $this->_session = Session::instance();
    }

    public function logged_in($role = NULL) {
        $status = FALSE;
        $user = $this->get_user();
        if (is_array($user) and $user[$this->_primary_key] > 0) {
            $status = TRUE;
        }
        return $status;
    }

    public function login($username, $password) {
        $where = array(
            'username' => $username,
            'status' => 'normal' 
        );
        $user = $this->getRow($where);
        $password = $this->hash($password);
        if ($user['password'] == $password) {
            return $this->complete_login($user);
        }
        return FALSE;
    }

    public function logout($destroy = FALSE) {
        if ($destroy === TRUE) {
            $this->_session->destroy();
        } else {
            $this->_session->delete($this->_session_key);
        }
        return !$this->logged_in();
    }

    public function check_password($password) {
        $user = $this->get_user();
        if (empty($user)) {
            return FALSE;
        }
        
        $hash = $this->hash($password);
        return $hash == $user['password'];
    }

    public function change_password($password) {
        $user = $this->get_user();
        if (empty($user)) {
            return FALSE;
        }
        
        $password = $this->hash($password);
        $data = array(
            'password' => $password 
        );
        $ret = $this->update($data, $user[$this->_primary_key]);
        return $ret;
    }

    public function hash($str) {
        return hash_hmac('sha256', $str, '%@#$!^*&');
    }

    public function get_user() {
        return $this->_session->get($this->_session_key, NULL);
    }

    protected function complete_login($user) {
        $data = array(
            'client_ip' => Request::$client_ip, 
            'last_login' => strtotime('now'), 
        );
        $this->update($data, $user[$this->_primary_key]);
        
        $this->_session->set($this->_session_key, $user);
        return TRUE;
    }
} 
