<?php

class Encrypt {
    
    public static $instances = array();
    protected static $_rand;

    public static function instance($name = 'default') {
        $encrypt_config = array(
            'default' => array(
                'key' => 'trwQwVXX96TIJoKxyBHB9AJkwAOHixuV1ENZmIWyanI0j1zNgSVvqywy044Agaj',
                'cipher' => MCRYPT_RIJNDAEL_128,
                'mode' => MCRYPT_MODE_NOFB 
            ),
            'blowfish' => array(
                'key' => '7bZJJkmNrelj5NaKoY6h6rMSRSmeUlJuTeOd5HHka5XknyMX4uGSfeVolTz4IYy',
                'cipher' => MCRYPT_BLOWFISH,
                'mode' => MCRYPT_MODE_ECB 
            ),
            'tripledes' => array(
                'key' => 'a9hcSLRvA3LkFc7EJgxXIKQuz1ec91J7P6WNq1IaxMZp4CTj5m31gZLARLxI1jD',
                'cipher' => MCRYPT_3DES,
                'mode' => MCRYPT_MODE_CBC 
            ) 
        );
        
        if (!isset(Encrypt::$instances[$name])) {
            $config = $encrypt_config[$name];
            Encrypt::$instances[$name] = new Encrypt($config['key'], $config['mode'], $config['cipher']);
        }
        
        return Encrypt::$instances[$name];
    }

    public function __construct($key, $mode, $cipher) {
        // Find the max length of the key, based on cipher and mode
        $size = mcrypt_get_key_size($cipher, $mode);
        
        if (isset($key[$size])) {
            // Shorten the key to the maximum size
            $key = substr($key, 0, $size);
        }
        
        $this->_key = $key;
        $this->_mode = $mode;
        $this->_cipher = $cipher;
        
        // Store the IV size
        $this->_iv_size = mcrypt_get_iv_size($this->_cipher, $this->_mode);
    }

    public function encode($data) {
        // Set the rand type if it has not already been set
        if (Encrypt::$_rand === NULL) {
            if (Kohana::$is_windows) {
                // Windows only supports the system random number generator
                Encrypt::$_rand = MCRYPT_RAND;
            } else {
                if (defined('MCRYPT_DEV_URANDOM')) {
                    // Use /dev/urandom
                    Encrypt::$_rand = MCRYPT_DEV_URANDOM;
                } elseif (defined('MCRYPT_DEV_RANDOM')) {
                    // Use /dev/random
                    Encrypt::$_rand = MCRYPT_DEV_RANDOM;
                } else {
                    // Use the system random number generator
                    Encrypt::$_rand = MCRYPT_RAND;
                }
            }
        }
        
        if (Encrypt::$_rand === MCRYPT_RAND) {
            // The system random number generator must always be seeded each
            // time it is used, or it will not produce true random results
            mt_srand();
        }
        
        // Create a random initialization vector of the proper size for the current cipher
        $iv = mcrypt_create_iv($this->_iv_size, Encrypt::$_rand);
        
        // Encrypt the data using the configured options and generated iv
        $data = mcrypt_encrypt($this->_cipher, $this->_key, $data, $this->_mode, $iv);
        
        // Use base64 encoding to convert to a string
        return base64_encode($iv . $data);
    }

    public function decode($data) {
        // Convert the data back to binary
        $data = base64_decode($data, TRUE);
        
        if (!$data) {
            // Invalid base64 data
            return FALSE;
        }
        
        // Extract the initialization vector from the data
        $iv = substr($data, 0, $this->_iv_size);
        
        if ($this->_iv_size !== strlen($iv)) {
            // The iv is not the expected size
            return FALSE;
        }
        
        // Remove the iv from the data
        $data = substr($data, $this->_iv_size);
        
        // Return the decrypted data, trimming the \0 padding bytes from the end of the data
        return rtrim(mcrypt_decrypt($this->_cipher, $this->_key, $data, $this->_mode, $iv), "\0");
    }
}
