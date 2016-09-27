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

    public function down_media($media_id) {
        $file_type = 'jpg';
        $filename = strtotime('now').str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT) . '.' . $file_type;
        $directory = realpath(APPPATH . '../data/upload');
        $sub_directory = date('Y/m/d');
        $sub_directory = str_replace('/', DIRECTORY_SEPARATOR, $sub_directory);
        $upload_dir = $directory.DIRECTORY_SEPARATOR.$sub_directory;
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, TRUE);
        }
        if (!is_writable($upload_dir)) {
            chmod($upload_dir, 0755);
        }
        
        $url = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=' . $this->access_token . '&media_id=' . $media_id;
        //error_log($url . "\n", 3, '/tmp/wx_upload.log');
        $data = CURL::get($url);
        $file_path = $upload_dir . DIRECTORY_SEPARATOR . $filename;
        file_put_contents($file_path, $data);
    
        list($width, $height) = getimagesize($file_path);
        $file_src = str_replace($directory, '', $file_path);
        $file_src = str_replace('\\', '/', $file_src);
        $file_src = trim($file_src, '/');
    
        return $file_src . '?' . $width . 'x' . $height;
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
