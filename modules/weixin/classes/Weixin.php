<?php

class Weixin {

    protected $redis;
    protected $access_token;
    
    protected $appid;
    protected $appsecret;
    
    public function __construct($name = 'default') {
        $weixin_config = Kohana::config('weixin.' . $name);
        $this->appid = $weixin_config['appid'];
        $this->appsecret = $weixin_config['appsecret'];
        
        $redis_config = Kohana::config('redis.default');
        $this->redis = new Redis();
        $this->redis->connect($redis_config['host'], $redis_config['port']);
        
        $access_token = $this->redis->get('wx_access_token');
        if (empty($access_token)) {
            $ret_array = $this->get_access_token();
            if (isset($ret_array['access_token'])) {
                $access_token = $ret_array['access_token'];
                $this->redis->setex('wx_access_token', $ret_array['expires_in']-60, $access_token);
            }
        }
        $this->access_token = $access_token;
    }

    public function get_access_token() {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
        $param = array(
            'appid' => $this->appid,
            'secret' => $this->appsecret,
        );
        $ret_json = CURL::get($url, $param);
        $ret_array = json_decode($ret_json, true);
        return $ret_array;
    }

    public function create_menu($data) {
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $this->access_token;
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        $ret_array = json_decode($ret_json, true);
        return $ret_array;
    }
    
    public function create_qrcode($data) {
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $this->access_token;
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        $ret_array = json_decode($ret_json, true);
        return $ret_array;
    }

    public function send_template_message($data) {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $this->access_token;
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        $ret_array = json_decode($ret_json, true);
        return $ret_array;
    }

    public function send_custom_message($data) {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $this->access_token;
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        $ret_array = json_decode($ret_json, true);
        return $ret_array;
    }

    public function __call($method, $args) {
        $method = explode('_', $method);
        $method = array_reverse($method);
        $method = implode('/', $method);
        $data = $args[0];
        $url = 'https://api.weixin.qq.com/cgi-bin/' . $method . '?access_token=' . $this->access_token;
        $param = json_encode($data, JSON_UNESCAPED_UNICODE);
        $ret_json = CURL::post($url, $param);
        $ret_array = json_decode($ret_json, true);
        return $ret_array;
    }
}
