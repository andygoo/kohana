<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Log_Writer {

	/**
	 * Numeric log level to string lookup table.
	 * @var array 
	 */
	/*
	protected $_log_levels = array(
		LOG_EMERG   => 'EMERGENCY',
		LOG_ALERT   => 'ALERT',
		LOG_CRIT    => 'CRITICAL',
		LOG_ERR     => 'ERROR',
		LOG_WARNING => 'WARNING',
		LOG_NOTICE  => 'NOTICE',
		LOG_INFO    => 'INFO',
		LOG_DEBUG   => 'DEBUG',
	);*/

	protected $_log_levels = array(
	        0  => 'EMERGENCY',
	        1  => 'ALERT',
	        2  => 'CRITICAL',
	        3  => 'ERROR',
	        4  => 'WARNING',
	        5  => 'NOTICE',
	        6  => 'INFO',
	        7  => 'DEBUG',
	);
	
	/**
	 * @var  int  Level to use for stack traces
	 */
	public static $strace_level = LOG_DEBUG;

	abstract public function write(array $messages);

	/**
	 * Allows the writer to have a unique key when stored.
	 *
	 *     echo $writer;
	 *
	 * @return  string
	 */
	final public function __toString() {
		return spl_object_hash($this);
	}

	public function format_message(array $message, $format = "time --- level: body") {
		$message['time'] = Date::formatted_time('@'.$message['time']);
		$message['level'] = $this->_log_levels[$message['level']];

		$string = strtr($format, $message);
		return $string;
	}
}
