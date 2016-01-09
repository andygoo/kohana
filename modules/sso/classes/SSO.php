<?php defined('SYSPATH') or die('No direct script access.');

class SSO {
    
    private static $sso = NULL;
    private $_config;

    public static function init() {
        if (self::$sso !== NULL) return;
        
        self::$sso = new self();
        self::$sso->_config = Kohana::config('sso');
    }
    
    public static function __callStatic($method, $args) {
        $key = self::$sso->_config['key'];
        $token = md5(uniqid(rand(), true));
        $checksum = self::get_checksum($key, $token);
        
        $ret = "(function(){\n";
        $sites = self::$sso->_config['sites'];
        foreach ($sites as $url) {
            $request_url = "{$url}{$method}/{$key}/{$token}/{$checksum}";
            $ret .= "var script=document.createElement('script');script.src='$request_url';document.body.appendChild(script);\n";
        }
        $ret .= "})();";
        return $ret;
    }
    
    private static function get_checksum($broker, $token, $clientip = null) {
        if ($clientip === null) {
            $clientip = Arr::get($_SERVER, 'REMOTE_ADDR');
        }
        $password = self::$sso->_config['password'];
        
        return sha1("{$token}{$clientip}{$password}");
    }

    private static function check($broker, $token, $checksum) {
        $_checksum = SSO::get_checksum($broker, $token);
        if ($_checksum != $checksum) {
            return false;
        }
        return true;
    }
}
