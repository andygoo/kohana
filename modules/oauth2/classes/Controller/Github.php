<?php

class Controller_Github extends Controller {

    public function action_index() {
        $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $github_client = OAuth2_Client::factory('github');
        $user_info = $github_client->get_user_data($redirect_uri);
        if (empty($user_info)) {
            $this->redirect('oauth/github/login?redirect_uri=' . urlencode($redirect_uri));
        }
        var_dump($user_info);
        exit;
    }
    
    public function action_login() {
        $github_client = OAuth2_Client::factory('github');
        $redirect_uri = $_GET['redirect_uri'];
        $auth_url = $github_client->get_authentication_url($redirect_uri, array(
            'scope' => 'user'
        ));
        $this->redirect($auth_url);
    }
}
