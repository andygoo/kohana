<?php defined('SYSPATH') OR die('No direct script access.');

class Log {

	// Log message levels - Windows users see PHP Bug #18090
	const EMERGENCY = 0;//LOG_EMERG;    // 0 1
	const ALERT     = 1;//LOG_ALERT;    // 1 1
	const CRITICAL  = 2;//LOG_CRIT;     // 2 1
	const ERROR     = 3;//LOG_ERR;      // 3 4
	const WARNING   = 4;//LOG_WARNING;  // 4 5
	const NOTICE    = 5;//LOG_NOTICE;   // 5 6
	const INFO      = 6;//LOG_INFO;     // 6 6
	const DEBUG     = 7;//LOG_DEBUG;    // 7 6

	public static $write_on_add = FALSE;
	protected static $_instance;
	protected $_messages = array();
	protected $_writers = array();

	public static function instance() {
		if (Log::$_instance === NULL) {
			Log::$_instance = new Log;
			register_shutdown_function(array(Log::$_instance, 'write'));
		}

		return Log::$_instance;
	}

	public function attach(Log_Writer $writer, $levels = array(), $min_level = 0) {
		if ( ! is_array($levels)) {
			$levels = range($min_level, $levels);
		}
		
		$this->_writers["{$writer}"] = array(
			'object' => $writer,
			'levels' => $levels
		);

		return $this;
	}

	public function detach(Log_Writer $writer) {
		unset($this->_writers["{$writer}"]);
		return $this;
	}

	/**
	 * $log->add(Log::ERROR, 'Could not locate user: :user', array(
	 *      ':user' => $username,
	 * ));
	 */
	public function add($level, $message, array $values = NULL) {
		if ($values) {
			$message = strtr($message, $values);
		}

		$this->_messages[] = array(
			'time'       => time(),
			'level'      => $level,
			'body'       => $message,
		);

		if (Log::$write_on_add) {
			$this->write();
		}

		return $this;
	}

	public function write() {
		if (empty($this->_messages)) {
			return;
		}

		$messages = $this->_messages;
		$this->_messages = array();

		foreach ($this->_writers as $writer) {
			if (empty($writer['levels'])) {
				$writer['object']->write($messages);
			} else {
				$filtered = array();
				foreach ($messages as $message) {
					if (in_array($message['level'], $writer['levels'])) {
						$filtered[] = $message;
					}
				}
				$writer['object']->write($filtered);
			}
		}
	}
}
