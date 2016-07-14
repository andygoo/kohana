<?php defined('SYSPATH') or die('No direct script access.');

class Kohana {
	
	public static $is_windows = FALSE;
	public static $magic_quotes = FALSE;

	public static $base_url = '/';
	public static $index_file = 'index.php';
	
	public static $profiling = TRUE;
	public static $errors = TRUE;
	public static $shutdown_errors = array(E_PARSE, E_ERROR, E_USER_ERROR);
	public static $log;
	
	protected static $_init = FALSE;
	protected static $_modules = array();
	protected static $_paths = array(APPPATH, SYSPATH);

	public static function init(array $settings = NULL) {
		if (Kohana::$_init) {
			return;
		}
		Kohana::$_init = TRUE;
		if (isset($settings['profile'])) {
			Kohana::$profiling = (bool) $settings['profile'];
		}

		ob_start();

		if (isset($settings['errors'])) {
			Kohana::$errors = (bool) $settings['errors'];
		}
		if (Kohana::$errors === TRUE) {
			set_exception_handler(array('Kohana_Exception', 'handler'));
			set_error_handler(array('Kohana', 'error_handler'));
		}
		register_shutdown_function(array('Kohana', 'shutdown_handler'));
		
		if (ini_get('register_globals')) {
			Kohana::globals();
		}
		
		Kohana::$is_windows = (DIRECTORY_SEPARATOR === '\\');
		
		if (function_exists('mb_internal_encoding')) {
			mb_internal_encoding('utf-8');
		}
		if (isset($settings['base_url'])) {
			Kohana::$base_url = rtrim($settings['base_url'], '/').'/';
		}
		if (isset($settings['index_file'])) {
			Kohana::$index_file = trim($settings['index_file'], '/');
		}
		
		Kohana::$magic_quotes = get_magic_quotes_gpc();
		
		$_GET    = Kohana::sanitize($_GET);
		$_POST   = Kohana::sanitize($_POST);
		$_COOKIE = Kohana::sanitize($_COOKIE);
		
		if ( ! Kohana::$log instanceof Log) {
			Kohana::$log = Log::instance();
		}
	}

	public static function deinit() {
		if (Kohana::$_init) {
			spl_autoload_unregister(array('Kohana', 'auto_load'));
			
			if (Kohana::$errors) {
				restore_error_handler();
				restore_exception_handler();
			}
			
			Kohana::$log = NULL;
			Kohana::$_modules = array();
			Kohana::$_paths   = array(APPPATH, SYSPATH);
			Kohana::$_init = FALSE;
		}
	}

	public static function globals() {
		if (isset($_REQUEST['GLOBALS']) OR isset($_FILES['GLOBALS'])) {
			echo "Global variable overload attack detected! Request aborted.\n";
			exit(1);
		}
		
		$global_variables = array_keys($GLOBALS);
		$global_variables = array_diff($global_variables, array('_COOKIE', '_ENV', '_GET', '_FILES', '_POST', '_REQUEST', '_SERVER', '_SESSION', 'GLOBALS'));
		
		foreach ($global_variables as $name) {
			unset($GLOBALS[$name]);
		}
	}

	public static function sanitize($value) {
		if (is_array($value) OR is_object($value)) {
			foreach ($value as $key => $val) {
				$value[$key] = Kohana::sanitize($val);
			}
		} elseif (is_string($value)) {
			if (Kohana::$magic_quotes === TRUE) {
				$value = stripslashes($value);
			}
			if (strpos($value, "\r") !== FALSE) {
				$value = str_replace(array("\r\n", "\r"), "\n", $value);
			}
		}
		return $value;
	}
	
	public static function auto_load($class, $dir = 'classes') {
	    $class     = ltrim($class, '\\');
	    
	    $file      = '';
	    $last_namespace_position = strripos($class, '\\');
	    if ($last_namespace_position) {
	        $namespace = substr($class, 0, $last_namespace_position);
	        $class     = substr($class, $last_namespace_position + 1);
	        $file      = str_replace('\\', DIRECTORY_SEPARATOR, $namespace).DIRECTORY_SEPARATOR;
	    }
	    
		$file = str_replace('_', DIRECTORY_SEPARATOR, $class);
		$path = Kohana::find_file($dir, $file);
		if ($path) {
			require_once $path;
			return TRUE;
		}
		return FALSE;
	}

	public static function modules(array $modules = NULL) {
		if ($modules === NULL)
			return Kohana::$_modules;

		$paths = array(APPPATH);
		foreach ($modules as $name => $path) {
			if (is_dir($path)) {
				$paths[] = realpath($path).DIRECTORY_SEPARATOR;
			} else  {
				unset($modules[$name]);
			}
		}
		$paths[] = SYSPATH;
		
		Kohana::$_paths = $paths;
		Kohana::$_modules = $modules;
		
		foreach (Kohana::$_modules as $path) {
			$init = $path.'/init.php';
			if (is_file($init)) {
				include $init;
			}
		}
		
		return Kohana::$_modules;
	}

	public static function find_file($dir, $file, $ext = NULL) {
		$ext = ($ext === NULL) ? '.php' : '.'.$ext;
		$path = $dir.DIRECTORY_SEPARATOR.$file.$ext;

		$found = FALSE;
		foreach (Kohana::$_paths as $dir) {
			if (is_file($dir.$path)) {
				$found = $dir.$path;
				break;
			}
		}
		return $found;
	}
	
	public static function config($group) {
		$group = explode('.', $group, 2);
		$file = $group[0];
		$path = Kohana::find_file('config', $file);
		if ($path) {
			$config = include $path;
			return (isset($group[1])) ? Arr::path($config, $group[1]) : $config;
		}
		return array();
	}
	
	public static function error_handler($code, $error, $file = NULL, $line = NULL) {
		if (error_reporting() & $code) {
			throw new ErrorException($error, $code, 0, $file, $line);
		}

		return TRUE;
	}

	public static function shutdown_handler() {
		if ( ! Kohana::$_init)
			return;

		if (Kohana::$errors AND $error = error_get_last() AND in_array($error['type'], Kohana::$shutdown_errors)) {
			ob_get_level() AND ob_clean();
			Kohana_Exception::handler(new ErrorException($error['message'], $error['type'], 0, $error['file'], $error['line']));
			exit(1);
		}
	}
}

if ( ! function_exists('__')) {
    function __($string, array $values = NULL) {
        return empty($values) ? $string : strtr($string, $values);
    }
}
