<?php

class HTTP {

    public static function get($url, $data='') {
        if(!empty($data)) {
            $content = is_array($data) ? http_build_query($data) : $data;
            $url .= ((strpos($url, '?') === false) ? '?' : '&') . $content;
        }
        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => "Content-Type: text/html; charset=utf-8",
                'timeout' => 5 
            ) 
        );
        
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    public static function post($url, $data='') {
        $content = is_array($data) ? http_build_query($data) : $data;
        $length = strlen($content);
        
        $opts = array(
            'http' => array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" . "Content-Length: " . $length . "\r\n",
                'content' => $content,
                'timeout' => 5
            ) 
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        return $result;
    }
}
