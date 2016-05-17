<?php

class URL {

    public static function base($index = FALSE, $protocol = FALSE) {
        if ($protocol === TRUE) {
            $protocol = Request::$protocol;
        }
        
        $base_url = Kohana::$base_url;
        
        if ($index === TRUE and !empty(Kohana::$index_file)) {
            $base_url .= Kohana::$index_file . '/';
        }
        
        if (is_string($protocol)) {
            if (parse_url($base_url, PHP_URL_HOST)) {
                $base_url = parse_url($base_url, PHP_URL_PATH);
            }
            $base_url = $protocol . '://' . $_SERVER['HTTP_HOST'] . $base_url;
        }
        
        return $base_url;
    }

    public static function site($uri = '', $protocol = FALSE) {
        $path = trim(parse_url($uri, PHP_URL_PATH), '/');
        $query = parse_url($uri, PHP_URL_QUERY);
        $fragment = parse_url($uri, PHP_URL_FRAGMENT);
        
        if ($query) {
            $query = '?' . $query;
        }
        
        if ($fragment) {
            $fragment = '#' . $fragment;
        }
        
        return URL::base(TRUE, $protocol) . $path . $query . $fragment;
    }

    public static function query(array $params = NULL) {
        if ($params === NULL) {
            $params = $_GET;
        } else {
            $params = array_merge($_GET, $params);
        }
        $params = array_filter($params, 'strlen');
        if (empty($params)) {
            return '';
        }
        return '?' . http_build_query($params, '', '&');
    }
    
    public static function curr() {
        return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    }
}
