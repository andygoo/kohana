<?php

class OAuth2_Client_QQ extends OAuth2_Client {

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
        $user_info = Cookie::get('qq_user_info');
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
        if(strpos($response, "callback") !== false) {
            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response, true);
            $openid = $msg['openid'];

            $params = array(
                'access_token' => $access_token,
                'oauth_consumer_key' => $this->_client_id,
                'openid' => $openid,
            );
            $url = 'https://graph.qq.com/user/get_user_info';
            $response = $this->fetch($url, $params);
            if(strpos($response, "callback") !== false) {
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response = substr($response, $lpos + 1, $rpos - $lpos -1);
            }
            
            $ret_array = json_decode($response, true);
            Cookie::set('qq_user_info', $response, 86400);
            
            return $ret_array;
        }
        
        return false;
    }

}

