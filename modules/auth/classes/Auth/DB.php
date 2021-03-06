<?php

class Auth_DB extends Auth {

    public function _login($username, $password, $remember) {
        $where = array(
            'username' => $username,
            'status' => 'normal' 
        );
        $m_admin = Model::factory($this->_config['tb'], $this->_config['db']);
        $user = $m_admin->getRow($where);

        $password = $this->hash($password);
        //var_dump($user, $password);exit;
        if (!empty($user) && $user['password'] == $password) {
            return $this->complete_login($user);
        }
        return FALSE;
    }

    public function force_login($user) {
        return $this->force_complete_login($user);
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
        $m_admin = Model::factory($this->_config['tb'], $this->_config['db']);
        $ret = $m_admin->updateById($data, $user['id']);
        return $ret;
    }

    protected function complete_login($user) {
        if (!empty($user['id'])) {
            $data = array(
                'client_ip' => Request::$client_ip,
                'last_login' => strtotime('now'),
            );
            $m_admin = Model::factory($this->_config['tb'], $this->_config['db']);
            $m_admin->updateById($data, $user['id']);
        }
    
        unset($user['password']);
        return parent::complete_login($user);
    }
    
    protected function force_complete_login($user) {
        return parent::complete_login($user);
    }
} 
