<?php

class CLI {

    /**
	 * Returns one or more command-line options. Options are specified using
	 * standard CLI syntax:
	 *
	 *     php index.php --username=john.smith --password=secret --var="some value with spaces"
	 *
	 *     // Get the values of "username" and "password"
	 *     $auth = CLI::options('username', 'password');
	 *
	 * @param   string  option name
	 * @param   ...
	 * @return  array
	 */
    public static function options($options) {
        $options = func_get_args();
        
        // Found option values
        $values = array();
        
        // Skip the first option, it is always the file executed
        for($i = 1; $i < $_SERVER['argc']; $i++) {
            if (!isset($_SERVER['argv'][$i])) {
                // No more args left
                break;
            }
            
            // Get the option
            $opt = $_SERVER['argv'][$i];
            
            if (substr($opt, 0, 2) !== '--') {
                // This is not an option argument
                continue;
            }
            
            // Remove the "--" prefix
            $opt = substr($opt, 2);
            
            if (strpos($opt, '=')) {
                list($opt, $value) = explode('=', $opt, 2);
            } else {
                $value = NULL;
            }
            
            if (in_array($opt, $options)) {
                $values[$opt] = $value;
            }
        }
        
        return $values;
    }
}
