<?php

class Controller_Auth extends Controller {

    public function action_login() {
        $submit = !empty($_POST) ? true : false;
        if ($submit) {
            $token = $_POST['csrf'];
            if (!Security::check($token)) {
                Security::token(true);
                exit('非法提交');
            }
            
            $username = $_POST['username'];
            $password = $_POST['password'];
            $return_url = !empty($_GET['return_url']) ? $_GET['return_url'] : '/';
            
            $auth = Auth::instance();
            if ($auth->login($username, $password)) {
                $this->redirect($return_url);
            } else {
                Security::token(true);
                exit('登录失败');
            }
        }
        
        $this->template = View::factory('login');
    }
}
