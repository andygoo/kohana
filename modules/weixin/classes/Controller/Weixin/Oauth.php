<?php

class Controller_Weixin_Oauth extends Controller {

    public function action_index() {
        $wx = new WeixinOauth();
        $user_info = $wx->get_user_info();
        if (empty($user_info)) {
            $this->redirect('weixin/oauth/login');
        }
        var_dump($user_info);
        exit;
    }
    
    public function action_login() {
        $wx = new WeixinOauth();
        $callback_url = URL::site('weixin/oauth', true);
        $login_url = $wx->get_login_url($callback_url);
        $this->redirect($login_url);
    }
}
