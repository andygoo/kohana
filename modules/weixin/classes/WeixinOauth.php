<?php

class WeixinOauth {

    private $appid;
    private $appsecret;
    
    public function __construct() {
        $config = Kohana::config('weixin');
        $this->appid = $config['appid'];
        $this->appsecret = $config['appsecret'];
    }
    
    public function get_login_url($redirect_uri, $scope='snsapi_base', $state=NULL) {
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize';
        $param = array(
            'appid' => $this->appid,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'scope' => $scope,
        );
        if ($state) $param['state'] = $state;
        $url .= '?' . http_build_query($param) . '#wechat_redirect';
        return $url;
    }

    public function get_access_token($code) {
        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token';
        $param = array(
            'appid' => $this->appid,
            'secret' => $this->appsecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        );
        $ret_json = CURL::get($url, $param);
        return $ret_json;
    }

    public function get_user_info($access_token, $openid) {
        $url = 'https://api.weixin.qq.com/sns/userinfo';
        $param = array(
            'access_token' => $access_token,
            'openid' => $openid,
            'lang' => 'zh_CN',
        );
        $ret_json = CURL::get($url, $param);
        return $ret_json;
    }

}
