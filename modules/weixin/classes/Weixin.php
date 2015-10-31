<?php

class Weixin {

    protected $cache;

    private $wx_api = 'https://api.weixin.qq.com/cgi-bin';
    private $appid;
    private $appsecret;
    private $access_token;
    
    public function __construct() {
        $config = Kohana::config('weixin');
        $this->appid = $config['appid'];
        $this->appsecret = $config['appsecret'];
        
        $this->cache = Cache::instance('sqlite');
        $access_token = $this->cache->get('wx_access_token');
        var_dump($access_token);
        if (empty($access_token)) {
            $ret = $this->get_access_token();
            $ret = json_decode($ret, true);
            if (isset($ret['access_token'])) {
                $access_token = $ret['access_token'];
                $this->cache->set('wx_access_token', $access_token, $ret['expires_in']-60);
            }
        }
        $this->access_token = $access_token;
    }

    public function get_access_token() {
        $url = $this->wx_api . '/token';
        $param = array(
            'appid' => $this->appid,
            'secret' => $this->appsecret,
            'grant_type' => 'client_credential',
        );
        $ret = CURL::get($url, $param);
        return $ret;
    }

    public function __call($method, $args) {
        $method = explode('_', $method);
        $method = array_reverse($method);
        $method = implode('/', $method);
        $url = $this->wx_api . '/' . $method . '?access_token=' . $this->access_token;
        $param = json_encode($args[0], JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        return $ret_json;
    }
    
    public function get_weixin_jsconfig() {
        $jsapi_ticket = $this->cache->get('jsapi_ticket');
        var_dump($jsapi_ticket);
        if (empty($jsapi_ticket)) {
            $url = $this->wx_api . '/ticket/getticket?access_token=' . $this->access_token . '&type=jsapi';
            $ret_json = CURL::get($url);
            $ret_array = json_decode($ret_json, true);
            if ($ret_array['errcode']==0) {
                $jsapi_ticket = $ret_array['ticket'];
                $this->cache->set('jsapi_ticket', $jsapi_ticket, $ret_array['expires_in']-60);
            }
        }
        
        $noncestr = Text::random('alnum', 16);
        $timestamp = strtotime('now');
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $jsconfig = array(
            'jsapi_ticket' => $jsapi_ticket, 
            'nonceStr' => $noncestr,
            'timestamp' => $timestamp,
            'url' => $url, 
        );
        $signature = sha1(http_build_query(array_change_key_case($jsconfig)));
        unset($jsconfig['jsapi_ticket'], $jsconfig['url']);
        $jsconfig['appId'] = $this->appid;
        ksort($jsconfig);
        $jsconfig['signature'] = $signature;
        return json_encode($jsconfig);
    }
    
    public function create_menu($menu) {
        $url = $this->wx_api . '/menu/create?access_token=' . $this->access_token;
        $menu = array('button' => $menu);
        $param = json_encode($menu, JSON_UNESCAPED_UNICODE);
        var_dump($param);
        return CURL::post($url, $param);
    }
    
    public function send_template_message($data) {
        $url = $this->wx_api . '/message/template/send?access_token=' . $this->access_token;
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        return $ret_json;
    }

    public function create_qrcode($data) {
        $url = $this->wx_api . '/qrcode/create?access_token=' . $this->access_token;
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        return $ret_json;
    }

    public function send_custom_message($touser, $content) {
        $param = array(
            'touser' => $touser,
            'msgtype' => 'text',
            'text' => array('content' => $content),
        );
        $url = $this->wx_api . '/message/custom/send?access_token=' . $this->access_token;
        $param = json_encode($param, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        return $ret_json;
    }
}
