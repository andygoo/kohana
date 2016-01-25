<?php

class WeixinJSAPI extends Weixin {

    private $jsapi_ticket;
    
    public function __construct() {
        parent::__construct();
        
        $jsapi_ticket = $this->redis->get('jsapi_ticket');
        //var_dump($jsapi_ticket);
        if (empty($jsapi_ticket)) {
            $ret_json = $this->get_jsapi_ticket();
            $ret_array = json_decode($ret_json, true);
            if (isset($ret_array['ticket'])) {
                $jsapi_ticket = $ret_array['ticket'];
                $this->redis->setex('jsapi_ticket', $ret_array['expires_in']-60, $jsapi_ticket);
            }
        }
        $this->jsapi_ticket = $jsapi_ticket;
    }

    public function get_jsapi_config() {
        $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $jsapi_config = array(
            'jsapi_ticket' => $this->jsapi_ticket,
            'nonceStr' => $this->random(16),
            'timestamp' => strtotime('now'),
            'url' => $url,
        );
        $signature = sha1(urldecode(http_build_query(array_change_key_case($jsapi_config))));
        unset($jsapi_config['jsapi_ticket'], $jsapi_config['url']);
        $jsapi_config['appId'] = $this->appid;
        $jsapi_config['jsApiList'] = array();
        $jsapi_config['signature'] = $signature;
        //$jsapi_config['debug'] = true;
        return json_encode($jsapi_config);
    }

    protected function get_jsapi_ticket() {
        $url = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=' . $this->access_token . '&type=jsapi';
        $param = array(
            'appid' => $this->appid,
            'secret' => $this->appsecret,
        );
        $ret = CURL::get($url, $param);
        return $ret;
    }
    
    protected function random($length = 8) {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pool = str_split($pool, 1);
        $max = count($pool) - 1;

        $str = '';
        for($i = 0; $i < $length; $i++) {
            $str .= $pool[mt_rand(0, $max)];
        }
        return $str;
    }
}
