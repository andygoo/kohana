<?php

class Controller_Weixin_Oauth extends Controller {

    public function action_index() {
        $wx = new WeixinOauth();
        $user_info = $wx->get_user_info();
        if (empty($user_info)) {
            $callback_url = URL::site('weixin/oauth', true);
            $this->redirect('weixin/oauth/login?callback_url=' . urlencode($callback_url));
        }
        var_dump($user_info);
        exit;
    }
    
    public function action_login() {
        $wx = new WeixinOauth();
        $callback_url = $_GET['callback_url'];
        $login_url = $wx->get_login_url($callback_url);
        $this->redirect($login_url);
    }
}
