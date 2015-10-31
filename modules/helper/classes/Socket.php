<?php

class Socket {

    public static function get($url) {
        
        $url = parse_url($url);
        
        $port = isset($url['port']) ? $url['port'] : '80';
        $host = ($port == 443) ? 'ssl://' . $url['host'] : $url['host'];
        
        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            throw new Kohana_Exception('failed to open socket to :host, :errno - :errstr', array(
                ':host' => $host,
                ':errno' => $errno,
                ':errstr' => $errstr,
            ));
        }
        
        fputs($fp, "GET {$url['path']} HTTP/1.1\r\n");
        fputs($fp, "Host: {$url['host']}\r\n");
        fputs($fp, "Connection: close\r\n");
        while (!feof($fp)) {
            echo fgets($fp, 128);
        }
        var_dump($fp);exit;
        fclose($fp);
    }
    
    
    public static function post($url, $data) {
        $url = parse_url($url);
        
        $port = isset($url['port']) ? $url['port'] : '80';
        $host = ($port == 443) ? 'ssl://' . $url['host'] : $url['host'];
        
        $fp = fsockopen($host, $port, $errno, $errstr);
        if (!$fp) {
            throw new Kohana_Exception('failed to open socket to :host', array(
                ':host' => $host 
            ));
        }
        
        fputs($fp, "POST {$url['path']} HTTP/1.1\r\n");
        fputs($fp, "Host: {$url['host']}\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($data) . "\r\n");
        fputs($fp, "Connection: close\r\n");
        fputs($fp, "$data\r\n");
        
        $line = fgets($fp, 1024);
        if (!preg_match('#^HTTP/1\\.. 200#', $line)) {
            return;
        }
        
        $content = "";
        $header = "not yet";
        while(!feof($fp)) {
            $line = fgets($fp, 128);
            if ($line == "\r\n" && $header == "not yet") {
                $header = "passed";
            }
            if ($header == "passed") {
                $content .= $line;
            }
        }
        return $content;
    }
}
