<?php 

class Upload {
    
    public static $remove_spaces = TRUE;
    public static $default_directory = 'upload';

    public static function save(array $file, $filename = NULL, $directory = NULL, $chmod = 0644) {
        if (!isset($file['tmp_name']) or !is_uploaded_file($file['tmp_name'])) {
            return FALSE;
        }
        
        if ($filename === NULL) {
            $filename = uniqid() . $file['name'];
        }
        
        if (Upload::$remove_spaces === TRUE) {
            $filename = preg_replace('/\s+/', '_', $filename);
        }
        
        if ($directory === NULL) {
            $directory = Upload::$default_directory;
        }
        
        if (!is_dir($directory) or !is_writable(realpath($directory))) {
            throw new Kohana_Exception('Directory :dir must be writable', array(
                ':dir' => Debug::path($directory) 
            ));
        }
        
        $filename = realpath($directory) . DIRECTORY_SEPARATOR . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filename)) {
            if ($chmod !== FALSE) {
                chmod($filename, $chmod);
            }
            
            return $filename;
        }
        
        return FALSE;
    }

    public static function valid($file) {
        return (isset($file['error']) and isset($file['name']) and isset($file['type']) and isset($file['tmp_name']) and isset($file['size']));
    }

    public static function not_empty(array $file) {
        return (isset($file['error']) and isset($file['tmp_name']) and $file['error'] === UPLOAD_ERR_OK and is_uploaded_file($file['tmp_name']));
    }

    public static function type(array $file, array $allowed) {
        if ($file['error'] !== UPLOAD_ERR_OK) return TRUE;
        
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        return in_array($ext, $allowed);
    }

    public static function size(array $file, $size) {
        if ($file['error'] === UPLOAD_ERR_INI_SIZE) {
            return FALSE;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) return TRUE;
        
        $size = strtoupper($size);
        
        if (!preg_match('/[0-9]++[BKMG]/', $size)) return FALSE;
        
        switch(substr($size, -1)) {
            case 'G':
                $size = intval($size) * pow(1024, 3);
                break;
            case 'M':
                $size = intval($size) * pow(1024, 2);
                break;
            case 'K':
                $size = intval($size) * pow(1024, 1);
                break;
            default:
                $size = intval($size);
                break;
        }
        
        return ($file['size'] <= $size);
    }

    public static function image(array $file, $max_width = NULL, $max_height = NULL, $exact = FALSE) {
        if (Upload::not_empty($file)) {
            try {
                list($width, $height) = getimagesize($file['tmp_name']);
            } catch(ErrorException $e) {
                // Ignore read errors
            }
            
            if (empty($width) or empty($height)) {
                return FALSE;
            }
            
            if (!$max_width) {
                $max_width = $width;
            }
            
            if (!$max_height) {
                $max_height = $height;
            }
            
            if ($exact) {
                // Check if dimensions match exactly
                return ($width === $max_width and $height === $max_height);
            } else {
                // Check if size is within maximum dimensions
                return ($width <= $max_width and $height <= $max_height);
            }
        }
        
        return FALSE;
    }
} 
