<?php

class Cookie {
    public static $salt = 'kooky';
    public static $expiration = 0;
    public static $path = '/';
    public static $domain = NULL;
    public static $secure = FALSE;
    public static $httponly = FALSE;

    public static function get($key, $default = NULL) {
        if (!isset($_COOKIE[$key])) {
            return $default;
        }
        
        $cookie = $_COOKIE[$key];
        $split = strlen(Cookie::salt($key, NULL));
        
        if (isset($cookie[$split]) and $cookie[$split] === '~') {
            list($hash, $value) = explode('~', $cookie, 2);
            if (Cookie::salt($key, $value) === $hash) {
                return $value;
            }
            Cookie::delete($key);
        }
        
        return $default;
    }

    public static function set($name, $value, $lifetime = null, $domain = null) {
        if ($lifetime === null) {
            $lifetime = Cookie::$expiration;
        }
        
        if ($lifetime !== 0) {
            $lifetime += time();
        }
        
        if ($domain !== null) {
            Cookie::$domain = $domain;
        }
        
        $value = Cookie::salt($name, $value) . '~' . $value;
        return setcookie($name, $value, $lifetime, Cookie::$path, Cookie::$domain, Cookie::$secure, Cookie::$httponly);
    }

    public static function delete($name) {
        unset($_COOKIE[$name]);
        return setcookie($name, NULL, -86400, Cookie::$path, Cookie::$domain, Cookie::$secure, Cookie::$httponly);
    }

    public static function salt($name, $value) {
        //$agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : 'unknown';
        $agent = 'unknown';
        
        return sha1($agent . $name . $value . Cookie::$salt);
    }
}