<?php

class Request {
    
    public static $protocol = 'http';
    public static $method = 'GET';
    public static $is_ajax = FALSE;
    public static $user_agent = '';
    public static $referrer = '';
    public static $client_ip;
    public static $instance;
    public static $trusted_proxies = array('127.0.0.1', 'localhost', 'localhost.localdomain');
    public static $theme = 'views';
    
    public $route;
    public $status = 200;
    public $response = '';
    public $headers = array();
    public $directory = '';
    public $controller;
    public $action;
    public $uri;
    protected $_params;

    public static function instance(& $uri = TRUE) {
        if (!Request::$instance) {
            if (!empty($_SERVER['HTTPS']) and filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN)) {
                Request::$protocol = 'https';
            }
            if (isset($_SERVER['REQUEST_METHOD'])) {
                Request::$method = $_SERVER['REQUEST_METHOD'];
            }
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                Request::$is_ajax = TRUE;
            }
            
            Request::$client_ip = self::get_client_ip();
            
            if (Request::$method !== 'GET' and Request::$method !== 'POST') {
                parse_str(file_get_contents('php://input'), $_POST);
            }
            
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                Request::$user_agent = $_SERVER['HTTP_USER_AGENT'];
            }
            
            if (isset($_SERVER['HTTP_REFERER'])) {
                Request::$referrer = $_SERVER['HTTP_REFERER'];
            }
            
            if ($uri === TRUE) {
                $uri = Request::detect_uri();
            }
            
            $uri = preg_replace('#//+#', '/', $uri);
            $uri = preg_replace('#\.[\s./]*/#', '', $uri);
            
            Request::$instance = new Request($uri);
        }
        
        return Request::$instance;
    }

    public static function detect_uri() {
        if (isset($_SERVER['PATH_INFO'])) {
            $uri = $_SERVER['PATH_INFO'];
        } else {
            if (isset($_SERVER['REQUEST_URI'])) {
                $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            } elseif (isset($_SERVER['PHP_SELF'])) {
                $uri = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['REDIRECT_URL'])) {
                $uri = $_SERVER['REDIRECT_URL'];
            } else {
                throw new Kohana_Exception('Unable to detect the URI using PATH_INFO, REQUEST_URI, or PHP_SELF');
            }
            
            $base_url = parse_url(Kohana::$base_url, PHP_URL_PATH);
            
            if (strpos($uri, $base_url) === 0) {
                $uri = substr($uri, strlen($base_url));
            }
            
            if (Kohana::$index_file and strpos($uri, Kohana::$index_file) === 0) {
                $uri = substr($uri, strlen(Kohana::$index_file));
            }
        }
        return $uri;
    }

    public static function factory($uri) {
        return new Request($uri);
    }

    public function __construct($uri) {
        $uri = trim($uri, '/');
        
        $routes = Route::all();
        foreach($routes as $name => $route) {
            $params = $route->matches($uri);
            if ($params) {
                $this->uri = $uri;
                $this->route = $route;
                
                if (isset($params['directory'])) {
                    $this->directory = $params['directory'];
                }
                
                $this->controller = $params['controller'];
                
                if (isset($params['action'])) {
                    $this->action = $params['action'];
                }
                
                unset($params['controller'], $params['action'], $params['directory']);
                
                $this->_params = $params;
                break;
            }
        }
    }

    public function __toString() {
        return (string)$this->response;
    }

    public function param($key = NULL, $default = NULL) {
        if ($key === NULL) {
            return $this->_params;
        }
        return isset($this->_params[$key]) ? $this->_params[$key] : $default;
    }

    public function uri(array $params = NULL) {
        if (!isset($params['directory'])) {
            $params['directory'] = $this->directory;
        }
        
        if (!isset($params['controller'])) {
            $params['controller'] = $this->controller;
        }
        
        if (!isset($params['action'])) {
            $params['action'] = $this->action;
        }
        
        $params += $this->_params;
        return $this->route->uri($params);
    }

    public function send_headers() {
        if (!headers_sent()) {
            foreach($this->headers as $name => $value) {
                if (is_string($name)) {
                    $value = "{$name}: {$value}";
                }
                header($value, TRUE, $this->status);
            }
        }
        return $this;
    }

    public function redirect($url, $code = 302) {
        if (strpos($url, '://') === FALSE) {
            $url = URL::site($url);
        }
        
        $this->status = $code;
        $this->headers['Location'] = $url;
        $this->send_headers();
        exit();
    }

    public function execute() {
        $prefix = 'Controller_';
        if (!empty($this->directory)) {
            $dirs = explode('/', trim($this->directory, '/'));
            $dirs = array_map('ucfirst', $dirs);
            $prefix .= implode('_', $dirs) . '_';
        }
        
        $class = new ReflectionClass($prefix . ucfirst($this->controller));
        $controller = $class->newInstance($this);
        $class->getMethod('before')->invoke($controller);
        
        $action = empty($this->action) ? 'action_index' : 'action_' . $this->action;
        if (!method_exists($controller, $action)) {
            $this->status = 404;
            throw new Kohana_Exception('The requested URL :uri was not found on this server.', array(
                ':uri' => $this->uri 
            ));
        }
        $class->getMethod($action)->invokeArgs($controller, $this->_params);
        $class->getMethod('after')->invoke($controller);
        
        return $this;
    }

    public static function user_agent($value) {
        return Text::user_agent(Request::$user_agent, $value);
    }

    public static function get_client_ip() {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) and isset($_SERVER['REMOTE_ADDR']) and in_array($_SERVER['REMOTE_ADDR'], Request::$trusted_proxies)) {
            // Use the forwarded IP address, typically set when the
            // client is using a proxy server.
            // Format: "X-Forwarded-For: client1, proxy1, proxy2"
            $client_ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $client_ip = array_shift($client_ips);
            unset($client_ips);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP']) and isset($_SERVER['REMOTE_ADDR']) and in_array($_SERVER['REMOTE_ADDR'], Request::$trusted_proxies)) {
            // Use the forwarded IP address, typically set when the
            // client is using a proxy server.
            $client_ips = explode(',', $_SERVER['HTTP_CLIENT_IP']);
            $client_ip = array_shift($client_ips);
            unset($client_ips);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            // The remote IP address
            $client_ip = $_SERVER['REMOTE_ADDR'];
        }
        return $client_ip;
    }
} 
