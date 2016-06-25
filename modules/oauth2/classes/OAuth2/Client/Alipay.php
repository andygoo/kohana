<?php

class OAuth2_Client_Alipay extends OAuth2_Client {

    public function get_authorization_endpoint() {
        return 'https://graph.qq.com/oauth2.0/authorize';
    }

    public function get_access_token_endpoint() {
        return 'https://graph.qq.com/oauth2.0/token';
    }

    public function get_user_profile_service_url() {
        return 'https://graph.qq.com/oauth2.0/me';
    }

    public function get_user_data($redirect_uri) {
        $user_info = Cookie::get('ali_user_info');
        if (!empty($user_info)) {
            return json_decode($user_info, true);
        }
        
        if (empty($_GET['code'])) {
            return array();
        }
        $params = array(
            'code' => $_GET['code'],
            'redirect_uri' => $redirect_uri 
        );
        $access_token = $this->get_access_token(OAuth2_Client::GRANT_TYPE_AUTHORIZATION_CODE, $params);
        $this->set_access_token($access_token);

        $url = $this->get_user_profile_service_url();
        $response = $this->fetch($url);
        
        $ret_array = json_decode($response, true);
        Cookie::set('ali_user_info', $response, 86400);
        
        return $ret_array;
    }

}

