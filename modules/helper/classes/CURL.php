<?php

class CURL {

    public static function get($url, $data='', $ext_options=array()) {
        if(!empty($data)) {
            $content = is_array($data) ? http_build_query($data) : $data;
            $url .= ((strpos($url, '?') === false) ? '?' : '&') . $content;
        }
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,

            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,

            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; Kohana +http://kohanaframework.org/)',
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5,
        );
        if (!empty($ext_options)) {
            $options = $ext_options + $options;
        }
        return self::_exec($options);
    }

    public static function post($url, $data='', $ext_options=array()) {
        $content = is_array($data) ? http_build_query($data) : $data;
        $options = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            //CURLOPT_HTTPHEADER => $header,

            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $content,
                
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,

            CURLOPT_SSL_VERIFYPEER => false, //不 进行SSL证书认证
            CURLOPT_SSL_VERIFYHOST => false, //不 1.检查证书中是否设置域名 2.是否与提供的主机名匹配
            //CURLOPT_SSLCERT => 'apiclient_cert.pem',
            //CURLOPT_SSLKEY => 'apiclient_key.pem',
            //CURLOPT_CAINFO => 'rootca.pem',//CA根证书（用来验证的网站证书是否是CA颁布）  

            CURLOPT_USERAGENT => 'Mozilla/5.0 (compatible; Kohana +http://kohanaframework.org/)',
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 5,
        );
        if (!empty($ext_options)) {
            $options = $ext_options + $options;
        }
        return self::_exec($options);
    }

    private static function _exec($options) {
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        if($result === false) {
            throw new Kohana_Exception('Curl error: ' . curl_error($ch));
            //echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        return $result;
    }
}
