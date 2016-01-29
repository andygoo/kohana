<?php

class WeixinOauth {

    protected $redis;
    protected $access_token;
    
    protected $appid = 'wxc5b1d86df49a2dc4';
    protected $appsecret = '50200b8e4eb49d9171835e6acea44955';
    
    public function get_login_url($redirect_uri, $scope='snsapi_userinfo') {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        $param = array(
            'appid' => $this->appid,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => $scope,
        );
        if ($scope == 'snsapi_base') {
            $param['state'] = 'a';
        } else if ($scope == 'snsapi_userinfo') {
            $param['state'] = 'b';
        }
        $url .= '?' . http_build_query($param) . '#wechat_redirect';
        return $url;
    }

    protected function get_access_token($code) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $param = array(
            'appid' => $this->appid,
            'secret' => $this->appsecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        );
        $ret_json = CURL::get($url, $param);
        $ret_array = json_decode($ret_json, true);
        return $ret_array;
    }

    public function get_user_openid() {
        if (empty($_GET['code'])) {
            return array();
        }
    
        $code = $_GET['code'];
        $ret_array = $this->get_access_token($code);
        return $ret_array;
    }
    
    public function get_user_info() {
        $wx_user_info = Cookie::get('wx_user_info');
        if (!empty($wx_user_info)) {
            return json_decode($wx_user_info, true);
        }
        
        if (empty($_GET['code'])) {
            return array();
        }
        
        $code = $_GET['code'];
        $ret_array = $this->get_access_token($code);
        if (isset($ret_array['errcode'])) {
            return $ret_array;
        }
        
        $openid = $ret_array['openid'];
        $access_token = $ret_array['access_token'];
        $url = 'https://api.weixin.qq.com/sns/userinfo';
        $param = array(
            'access_token' => $access_token,
            'openid' => $openid,
            'lang' => 'zh_CN',
        );
        $ret_json = CURL::get($url, $param);
        $ret_array = json_decode($ret_json, true);
        Cookie::set('wx_user_info', $ret_json, 86400);
        return $ret_array;
    }

}
