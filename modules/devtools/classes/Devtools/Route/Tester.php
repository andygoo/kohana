<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Kohana 3 Route tester class.  Use it by calling [Route::test]
 *
 * @package	   Bluehawk/Devtools
 * @author     Michael Peters
 */
class Devtools_Route_Tester {
    
    // The url for this test
    public $url;
    
    // The route this url matched
    public $route = FALSE;
    
    // The params the route returned
    public $params;
    
    // The optional expected params from the config
    public $expected_params = FALSE;

    /**
	 * Get an array of Route_Tester objects from the config settings
	 *
	 * @param  $tests  A URL to test, or an array of URLs to test.
	 * @returns array  An array of Route_Tester objects
	 */
    public static function create_tests($tests) {
        if (is_string($tests)) {
            $tests = array($tests);
        }
        
        $array = array();
        
        // Get the url and optional expected_params from the config
        foreach($tests as $key => $value) {
            $current = new Devtools_Route_Tester();
            
            if (is_array($value)) {
                $url = $key;
                $current->expected_params = $value;
            } else {
                $url = $value;
            }
            
            $current->url = trim($url, '/');
            
            // Test each route, and save the route and params if it matches
            foreach(Route::all() as $route) {
                $params = $route->matches($current->url);
                if ($params) {
                    $current->route = $route;
                    $current->params = $params;
                    $current->route_name = Route::name($route);
                    break;
                }
            }
            
            $array[] = $current;
        }
        
        return $array;
    }

    public function get_params() {
        $array = array();
        
        // Add the result and expected keys to the array
        foreach($this->params as $param => $value) {
            $array[$param]['result'] = $value;
        }
        
        foreach($this->expected_params as $param => $value) {
            $array[$param]['expected'] = $value;
        }
        
        // Not the prettiest code in the word (wtf arrays), but oh well
        foreach($array as $item => $options) {
            // Assume they don't match.
            $array[$item]['error'] = true;
            
            if (!isset($options['expected'])) {
                $array[$item]['expected'] = '[none]';
            } else if (!isset($options['result'])) {
                $array[$item]['result'] = '[none]';
            } else if ($options['result'] == $options['expected']) {
                $array[$item]['error'] = false;
            }
        }
        
        return $array;
    }
}

