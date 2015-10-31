<?php defined('SYSPATH') or die('No direct script access.');

class Taoke {
    protected $_config;

    public function __construct() {
        $this->_config = array(
            'api_key' => '21274271',
            'api_secret' => '0db2063ce674762e38c7f447636f0fb3',
            'api_serverURL' => 'http://gw.api.taobao.com/router/rest',
            'api_version' => '2.0',
            'api_format' => 'json', //xml or json
            'api_signMethod' => 'md5'  //md5 or Hmac
        );
    }

    public function request($params) {
        $default = array(
            'api_key' => $this->_config['api_key'],
            'format' => $this->_config['api_format'],
            'sign_method' => $this->_config['api_signMethod'],
            'timestamp' => date('Y-m-d H:i:s'),
            'v' => $this->_config['api_version'] 
        );
        $params = array_merge($default, $params);
        $params['sign'] = $this->createSign($params);
        
        $url = $this->_config['api_serverURL'] . '?' . http_build_query($params);
        return file_get_contents($url);
    }

    public function createSign($params) {
        $sign = $this->_config['api_secret'];
        ksort($params);
        foreach($params as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= $key . $val;
            }
        }
        
        return $sign = strtoupper(md5($sign . $this->_config['api_secret']));
    }
}
