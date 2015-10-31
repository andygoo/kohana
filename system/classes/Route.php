<?php defined('SYSPATH') or die('No direct script access.');

class Route {
    
    const REGEX_KEY = '<([a-zA-Z0-9_]++)>';
    const REGEX_SEGMENT = '[^/.,;?]++';
    const REGEX_ESCAPE = '[.\\+*?[^\\]${}=!|]';
    
    protected $_uri = '';
    protected $_regex = array();
    protected $_route_regex;
    protected $_defaults = array('action' => 'index');
    protected static $_routes = array();

    public function __construct($uri = NULL, array $regex = NULL) {
        if (!empty($regex)) {
            $this->_regex = $regex;
        }
        
        $this->_uri = $uri;
        $this->_route_regex = $this->_compile();
    }

    public static function set($name, $uri, array $regex = NULL) {
        return Route::$_routes[$name] = new Route($uri, $regex);
    }

    public static function get($name) {
        if (!isset(Route::$_routes[$name])) {
            throw new Kohana_Exception('The requested route does not exist: :route', array(
                ':route' => $name 
            ));
        }
        
        return Route::$_routes[$name];
    }

    public static function all() {
        return Route::$_routes;
    }

    public static function name(Route $route) {
        return array_search($route, Route::$_routes);
    }
    
    public function defaults(array $defaults = NULL) {
        if ($defaults === NULL) {
            return $this->_defaults;
        }
        
        $this->_defaults = $defaults;
        
        return $this;
    }

    public function matches($uri) {
        if (!preg_match($this->_route_regex, $uri, $matches)) return FALSE;
        
        $params = array();
        foreach($matches as $key => $value) {
            if (is_int($key)) continue;
            
            $params[$key] = $value;
        }
        
        foreach($this->_defaults as $key => $value) {
            if (!isset($params[$key]) or $params[$key] === '') {
                $params[$key] = $value;
            }
        }
        
        return $params;
    }

    public function uri(array $params = NULL) {
        if ($params === NULL) {
            $params = $this->_defaults;
        } else {
            $params += $this->_defaults;
        }
        
        $uri = $this->_uri;
        
        if (strpos($uri, '<') === FALSE and strpos($uri, '(') === FALSE) {
            // This is a static route, no need to replace anything
            return $uri;
        }
        
        while(preg_match('#\([^()]++\)#', $uri, $match)) {
            $search = $match[0];
            $replace = substr($match[0], 1, -1);
            while(preg_match('#' . Route::REGEX_KEY . '#', $replace, $match)) {
                list($key, $param) = $match;
                if (isset($params[$param])) {
                    $replace = str_replace($key, $params[$param], $replace);
                } else {
                    $replace = '';
                    break;
                }
            }
            $uri = str_replace($search, $replace, $uri);
        }
        
        while(preg_match('#' . Route::REGEX_KEY . '#', $uri, $match)) {
            list($key, $param) = $match;
            if (!isset($params[$param])) {
                throw new Kohana_Exception('Required route parameter not passed: :param', array(
                    ':param' => $param 
                ));
            }
            $uri = str_replace($key, $params[$param], $uri);
        }
        
        $uri = preg_replace('#//+#', '/', rtrim($uri, '/'));
        return $uri;
    }

    /**
     * Create a URL from a route name. This is a shortcut for:
     *
     *     echo URL::site(Route::get($name)->uri($params), $protocol);
     *
     * @param   string  $name       route name
     * @param   array   $params     URI parameters
     * @param   mixed   $protocol   protocol string or boolean, adds protocol and domain
     * @return  string
     * @since   3.0.7
     * @uses    URL::site
     */
    public static function url($name, array $params = NULL, $protocol = NULL)
    {
        $route = Route::get($name);
        return URL::site($route->uri($params), $protocol);
    }
    
    protected function _compile() {
        // The URI should be considered literal except for keys and optional parts
        // Escape everything preg_quote would escape except for : ( ) < >
        $regex = preg_replace('#' . Route::REGEX_ESCAPE . '#', '\\\\$0', $this->_uri);
        
        if (strpos($regex, '(') !== FALSE) {
            // Make optional parts of the URI non-capturing and optional
            $regex = str_replace(array('(', ')'), array('(?:', ')?'), $regex);
        }
        
        // Insert default regex for keys
        $regex = str_replace(array('<', '>'), array('(?P<', '>' . Route::REGEX_SEGMENT . ')'), $regex);
        
        if (!empty($this->_regex)) {
            $search = $replace = array();
            foreach($this->_regex as $key => $value) {
                $search[] = "<$key>" . Route::REGEX_SEGMENT;
                $replace[] = "<$key>$value";
            }
            $regex = str_replace($search, $replace, $regex);
        }
        
        return '#^' . $regex . '$#uD';
    }
}
