<?php

abstract class OAuth2_Client {
    
    /**
     * Authorization types
     */
    const AUTH_TYPE_URI = 0;
    const AUTH_TYPE_AUTHORIZATION = 1;
    const AUTH_TYPE_FORM = 2;
    
    /**
     * Access token types
     */
    const TOKEN_TYPE_URI = 0;
    const TOKEN_TYPE_BEARER = 1;
    const TOKEN_TYPE_OAUTH = 2;
    const TOKEN_TYPE_MAC = 3;
    
    /**
    * Grant types
    */
    const GRANT_TYPE_AUTHORIZATION_CODE = 'authorization_code';
    const GRANT_TYPE_PASSWORD = 'password';
    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';
    const GRANT_TYPE_REFRESH_TOKEN = 'refresh_token';

    /**
     * HTTP Methods
     */
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_PATCH = 'PATCH';

    protected $_client_id = NULL;
    protected $_client_secret = NULL;
    protected $_client_auth_type = self::AUTH_TYPE_URI;
    protected $_access_token = NULL;
    protected $_access_token_type = self::TOKEN_TYPE_URI;
    
    protected $_access_token_secret = NULL;
    protected $_access_token_algorithm = NULL;
    protected $_access_token_param_name = 'access_token';
    
    protected $_certificate_file = NULL;

    protected $_required_params = array(
        'authorization_code' => array(
            'code',
            //'redirect_uri' 
        ),
        'password' => array(
            'username',
            'password' 
        ),
        'refresh_token' => array(
            'refresh_token' 
        ),
        'client_credentials' => array() 
    );

    abstract function get_authorization_endpoint();
    abstract function get_access_token_endpoint();
    abstract function get_user_profile_service_url();
    abstract function get_user_data();

    public function __construct($config, $client_auth_type = self::AUTH_TYPE_URI, $certificate_file = NULL) {
        $this->_client_id = $config['client_id'];
        $this->_client_secret = $config['client_secret'];
        $this->_client_auth_type = $client_auth_type;
        $this->_certificate_file = $certificate_file;
        
        if (!empty($this->_certificate_file) && !is_file($this->_certificate_file)) {
            throw new Kohana_Exception('The certificate file was not found.');
        }
    }

    public static function factory($provider, $client_auth_type = self::AUTH_TYPE_URI, $certificate_file = NULL) {
        $config = Kohana::config('oauth.' . $provider);
        $client_id = $config['client_id'];
        $client_secret = $config['client_secret'];
        
        $class_name = 'OAuth2_Client_' . ucfirst($provider);
        return new $class_name($config, $client_auth_type, $certificate_file);
    }
    
    public function get_authentication_url($redirect_uri, array $extra_parameters = array()) {
        $parameters = array_merge(array(
            'response_type' => 'code',
            'client_id' => $this->_client_id,
            'redirect_uri' => $redirect_uri 
        ), $extra_parameters);
        
        return $this->get_authorization_endpoint() . '?' . http_build_query($parameters, NULL, '&');
    }

    public function get_access_token($grant_type, $parameters) {
        if (!$grant_type) {
            throw new Kohana_Exception('The grant_type is mandatory.');
        }
        
        foreach($this->_required_params[$grant_type] as $param) {
            if (!isset($parameters[$param])) {
                throw new Kohana_Exception('The ":param" parameter must be defined for ":grant_type" grant type.', array(
                        ':param' => $param,
                        ':grant_type' => $grant_type
                ));
            }
        }
        
        $parameters['grant_type'] = $grant_type;
        
        $http_headers = array();
        
        switch($this->_client_auth_type) {
            case self::AUTH_TYPE_URI:
            case self::AUTH_TYPE_FORM:
                $parameters['client_id'] = $this->_client_id;
                $parameters['client_secret'] = $this->_client_secret;
                break;
        
            case self::AUTH_TYPE_AUTHORIZATION:
                $parameters['client_id'] = $this->_client_id;
                $http_headers['Authorization'] = 'Basic ' . base64_encode($this->_client_id . ':' . $this->_client_secret);
                break;
        
            default:
                throw new Kohana_Exception('Unknown client auth type ":client_auth_type".', array(
                ':client_auth_type' => $this->_client_auth_type
                ));
                break;
        }
        
        $result = CURL::post($this->get_access_token_endpoint(), $parameters);
        
        if (!is_array($result)) {
            // Make sure `$result` is an array
            parse_str($result, $result);
        }
        
        if (!isset($result[$this->_access_token_param_name])) {
            throw new Kohana_Exception('Unable to get the access token.');
        }
        
        return $result[$this->_access_token_param_name];
    }

    public function set_access_token($token) {
        $this->_access_token = $token;
    }

    public function set_access_token_type($type, $secret = NULL, $algorithm = NULL) {
        $this->_access_token_type = $type;
        $this->_access_token_secret = $secret;
        $this->_access_token_algorithm = $algorithm;
    }

    public function fetch($protected_resource_url, $parameters = array(), $http_method = self::HTTP_METHOD_GET) {
        if ($this->_access_token) {
            switch($this->_access_token_type) {
                case self::TOKEN_TYPE_URI:
                    $parameters[$this->_access_token_param_name] = $this->_access_token;
                    break;
                
                case self::TOKEN_TYPE_BEARER:
                    $http_headers['Authorization'] = 'Bearer ' . $this->_access_token;
                    break;
                
                case self::TOKEN_TYPE_OAUTH:
                    $http_headers['Authorization'] = 'OAuth ' . $this->_access_token;
                    break;
                
                case self::TOKEN_TYPE_MAC:
                    $http_headers['Authorization'] = 'MAC ' . $this->_generate_mac_signature($protected_resource_url, $parameters, $http_method);
                    break;
                
                default:
                    throw new Kohana_Exception('Unknown access token type: ":access_token_type".', array(
                        ':access_token_type' => $this->_access_token_type 
                    ));
                    break;
            }
            
        }

        $curl_options = array();
        if (!empty($http_headers)) {
            $header = array();
            foreach ($http_headers as $key => $parsed_url_value) {
                $header[] = "$key: $parsed_url_value";
            }
            $curl_options[CURLOPT_HTTPHEADER] = $header;
        }
        $response = CURL::get($protected_resource_url, $parameters, $curl_options);
        return $response;
    }

    protected function _generate_mac_signature($url, $parameters, $http_method) {
        $timestamp = time();
        $nonce = uniqid();
        $parsed_url = parse_url($url);
        
        if (!isset($parsed_url['port'])) {
            $parsed_url['port'] = ($parsed_url['scheme'] == 'https') ? 443 : 80;
        }
        
        if ($http_method == self::HTTP_METHOD_GET) {
            if (is_array($parameters)) {
                $parsed_url['path'] .= '?' . http_build_query($parameters, NULL, '&');
            } elseif ($parameters) {
                $parsed_url['path'] .= '?' . $parameters;
            }
        }

        $signature = base64_encode(hash_hmac($this->_access_token_algorithm,
                $timestamp."\n" .
                $nonce."\n" .
                $http_method."\n" .
                $parsed_url['path']."\n" .
                $parsed_url['host']."\n" .
                $parsed_url['port']."\n\n" .
                $this->_access_token_secret, TRUE));
        
        return 'id="'.$this->_access_token.'", ts="'.$timestamp.'", nonce="'.$nonce.'", mac="'.$signature.'"';
    }

}
