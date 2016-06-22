<?php

class Controller_QQ extends Controller {

    public function action_index() {
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $client = OAuth2_Client::factory('QQ');
        $user_info = $client->get_user_data($redirect_uri);
        if (empty($user_info)) {
            $this->redirect('oauth/qq/login?redirect_uri=' . urlencode($redirect_uri));
        }
        var_dump($user_info);
        exit;
    }
    
    public function action_login() {
        $client = OAuth2_Client::factory('QQ');
        $redirect_uri = $_GET['redirect_uri'];
        
        $state = md5(uniqid(rand(), TRUE));
        $auth_url = $client->get_authentication_url($redirect_uri, array(
            'state' => $state,
            'scope' => 'get_user_info',
        ));
        //var_dump($auth_url);exit;
        $this->redirect($auth_url);
    }
}
