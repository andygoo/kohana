<?php

class Controller_Github extends Controller {

    public function action_index() {
        $github_client = OAuth2_Client::factory('github');
        $user_info = $github_client->get_user_data();
        if (empty($user_info)) {
            $callback_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $this->redirect('oauth/github/login?callback_url=' . urlencode($callback_url));
        }
        var_dump($user_info);
        exit;
    }
    
    public function action_login() {
        $github_client = OAuth2_Client::factory('github');
        $redirect_uri = $_GET['callback_url'];
        $auth_url = $github_client->get_authentication_url($redirect_uri, array(
            'scope' => 'user'
        ));
        $this->redirect($auth_url);
    }
}
