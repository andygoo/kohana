<?php

class OAuth2_Client_Github extends OAuth2_Client {

    public function get_authorization_endpoint() {
        return 'https://github.com/login/oauth/authorize';
    }

    public function get_access_token_endpoint() {
        return 'https://github.com/login/oauth/access_token';
    }

    public function get_user_profile_service_url() {
        return 'https://api.github.com/user';
    }

    public function get_user_data() {
        $wx_user_info = Cookie::get('wx_user_info');
        if (!empty($wx_user_info)) {
            return json_decode($wx_user_info, true);
        }
        
        if (empty($_GET['code'])) {
            return array();
        }
        $params = array(
            'code' => $_GET['code'],
            //'redirect_uri' => $redirect_uri 
        );
        $access_token = $this->get_access_token(OAuth2_Client::GRANT_TYPE_AUTHORIZATION_CODE, $params);
        $this->set_access_token($access_token);
        
        $url = $this->get_user_profile_service_url();
        $ret_json = $this->fetch($url);

        $ret_array = json_decode($ret_json, true);
        Cookie::set('wx_user_info', $ret_json, 86400);
        
        return $ret_array;
    }
}

