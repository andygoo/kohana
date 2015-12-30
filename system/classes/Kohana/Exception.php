<?php defined('SYSPATH') OR die('No direct access');

class Kohana_Exception extends Exception {

	/**
	 * @var  array  PHP error code => human readable name
	 */
	public static $php_errors = array(
		E_ERROR              => 'Fatal Error',		//1
		E_USER_ERROR         => 'User Error',		//256
		E_PARSE              => 'Parse Error',		//4
		E_WARNING            => 'Warning',			//2
		E_USER_WARNING       => 'User Warning',		//512
		E_STRICT             => 'Strict',			//2048
		E_NOTICE             => 'Notice',			//8
		E_RECOVERABLE_ERROR  => 'Recoverable Error',//4096
		E_DEPRECATED         => 'Deprecated',		//8192
	);

	public static $error_view = 'kohana/error';

	public function __construct($message = "", array $variables = NULL, $code = 0, Exception $previous = NULL) {
		$message = __($message, $variables);
		parent::__construct($message, (int) $code, $previous);
		$this->code = $code;
	}

	public function __toString() {
		return Kohana_Exception::text($this);
	}

	public static function handler($e) {
	    try {
	        Kohana_Exception::log($e);
	        if (PHP_SAPI == 'cli') {
	            $response = Kohana_Exception::text($e);
	        } else {
	            $response = Kohana_Exception::response($e);
	        }
	        echo $response;
	        exit(1);
	    } catch (Exception $e) {
	        ob_get_level() AND ob_clean();
	        header('Content-Type: text/plain; charset=utf-8', TRUE, 500);
	        echo Kohana_Exception::text($e);
	        exit(1);
	    }
	}

	public static function log($e, $level = Log::EMERGENCY) {
		if (is_object(Kohana::$log)) {
			$error = Kohana_Exception::text($e);
			Kohana::$log->add($level, $error);
			// Make sure the logs are written
			Kohana::$log->write();
		}
	}

	public static function text($e) {
		$code = $e->getCode();
		if (isset(Kohana_Exception::$php_errors[$code])) {
			$code = Kohana_Exception::$php_errors[$code];
		}
		return sprintf('%s [ %s ]: %s ~ %s [ %d ]',
			get_class($e), $code, strip_tags($e->getMessage()), Debug::path($e->getFile()), $e->getLine());
	}

	public static function response($e) {
		try {
			$class   = get_class($e);
			$code    = $e->getCode();
			$message = $e->getMessage();
			$file    = $e->getFile();
			$line    = $e->getLine();
			$trace   = $e->getTrace();

			if ($e instanceof ErrorException) {
				if (isset(Kohana_Exception::$php_errors[$code])) {
					$code = Kohana_Exception::$php_errors[$code];
				}
			}
			
			$response = View::factory(Kohana_Exception::$error_view, get_defined_vars());
			return $response;
		} catch (Exception $e) {
			ob_get_level() AND ob_clean();
			header('Content-Type: text/plain; charset=utf-8', TRUE, 500);
			echo Kohana_Exception::text($e);
			exit(1);
		}
	}
} 
