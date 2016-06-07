<?php

class HTML {

    public static function chars($value, $double_encode = TRUE) {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'utf-8', $double_encode);
    }

    public static function entities($value, $double_encode = TRUE) {
        return htmlentities((string)$value, ENT_QUOTES, 'utf-8', $double_encode);
    }

    public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL) {
        if (strpos($uri, '://') !== FALSE) {
            //$attributes['target'] = '_blank';
        } elseif ($uri[0] !== '#') {
            $uri = URL::site($uri, $protocol);
        }
    
        $attributes['href'] = $uri;
    
        return '<a' . HTML::attributes($attributes) . '>' . $title . '</a>';
    }
    
    public static function style($file, array $attributes = NULL, $index = FALSE) {
        if (strpos($file, '://') === FALSE) {
            $file = URL::base($index) . $file;
        }
        $attributes['href'] = $file;
        $attributes['rel'] = isset($attributes['rel']) ? $attributes['rel'] : 'stylesheet';
        return '<link' . HTML::attributes($attributes) . ' />';
    }

    public static function script($file, array $attributes = NULL, $index = FALSE) {
        if (strpos($file, '://') === FALSE) {
            $file = URL::base($index) . $file;
        }
        $attributes['src'] = $file;
        return '<script' . HTML::attributes($attributes) . '></script>';
    }

    public static function image($file, array $attributes = NULL, $index = FALSE) {
        if (strpos($file, '://') === FALSE) {
            $file = URL::site($file);
        }
        $attributes['src'] = $file;
        return '<img' . HTML::attributes($attributes) . '>';
    }

    public static function attributes(array $attributes = NULL) {
        if (empty($attributes)) return '';
        
        $compiled = '';
        foreach($attributes as $key => $value) {
            if ($value === NULL) {
                continue;
            }
			if (is_int($key)) {
				$key = $value;
			}
            $compiled .= ' ' . $key . '="' . HTML::chars($value) . '"';
        }
        return $compiled;
    }
}
