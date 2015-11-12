<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Database {

	const SELECT =  1;
	const INSERT =  2;
	const UPDATE =  3;
	const DELETE =  4;
	
	public $last_query;
	public static $instances = array();
	
	protected $_conn;
	protected $_conn_master;
	protected $_conn_slave;

	protected $_config;
	protected $_config_curr;
	
	protected $_instance;
	protected $_instance_curr;
	
	public static function instance($name = 'default', array $config = NULL) {
		if (!isset(Database::$instances[$name])) {
			if ($config === NULL) {
				$config = Kohana::config('database.'.$name);
			}

			$driver = 'Database_'.ucfirst($config['type']);
			Database::$instances[$name] = new $driver($name, $config);
		}
		
		return Database::$instances[$name];
	}
	
	protected function __construct($name, array $config) {
        $this->_config = $config;
        $this->_instance = $name;

        $this->_config_curr = $config;
        $this->_instance_curr = $name;
	}
	
	public function connect($type='master') {
		$r_type = 'default';
		if ($type=='slave') {
		    if (isset($this->_config_curr['slave'])) {
		    	$r_type = 'slave';
		    } elseif (isset($this->_config_curr['master'])) {
		    	$r_type = 'master';
		    }
		} elseif ($type=='master') {
		    if (isset($this->_config_curr['master'])) {
		    	$r_type = 'master';
		    } elseif (isset($this->_config_curr['slave'])) {
		    	$r_type = 'slave';
		    }
		}

		if ($r_type=='slave') {
		   	$this->_config = $this->_config_curr['slave'];
		    if ($this->_instance_curr != $this->_instance.'_slave') {
		        $this->_conn = $this->_conn_slave;
		        $this->_instance_curr = $this->_instance.'_slave';
		    }
		    $this->_conn OR $this->_conn_slave = $this->_connect();
		} elseif ($r_type=='master') {
		   	$this->_config = $this->_config_curr['master'];
		    if ($this->_instance_curr != $this->_instance.'_master') {
		        $this->_conn = $this->_conn_master;
		        $this->_instance_curr = $this->_instance.'_master';
		    }
		    $this->_conn OR $this->_conn_master = $this->_connect();
		} else {
			$this->_conn OR $this->_connect();
		}
		return $this->_conn;
	}

	abstract protected function _connect();
	abstract public function escape($value);
	abstract public function query($sql);

	public function select($table, $columns='*', $where='') {
		$columns = is_array($columns) ? implode(', ', $columns) : $columns;
		$sql = "SELECT $columns FROM $table $where";
		return $this->query($sql);
	}
	
	public function insert($table, $data) {
		$data = array_map(array($this, 'escape'), $data);

		$fields = implode('`,`', array_keys($data));
		$values = implode(',', array_values($data));

		$sql = "INSERT INTO {$table}(`$fields`) VALUES($values)";
		return $this->query($sql);
	}
	
	public function replace_into($table, $data) {
		$data = array_map(array($this, 'escape'), $data);

		$fields = implode('`,`', array_keys($data));
		$values = implode(',', array_values($data));

		$sql = "REPLACE INTO {$table}(`$fields`) VALUES($values)";
		return $this->query($sql);
	}

	public function update($table, $data, $where) {
		foreach ($data as $key => $value) {
			$value = $this->escape($value);
			$fields[] = "`$key`=$value";
		}
		$sql = "UPDATE $table SET ".implode(',', $fields)." $where";
		return $this->query($sql);
	}

	public function __call($method, $args) {
	    $this->_conn OR $this->_conn = $this->connect();
	    return call_user_func_array(array($this->_conn, $method), $args);
	}
	
	public function disconnect() {
		$this->_conn = NULL;
		$this->_conn_master = NULL;
		$this->_conn_slave = NULL;
	    unset(Database::$instances[$this->_instance]);
	    return TRUE;
	}
	
	final public function __destruct() {
	    $this->disconnect();
	}
	
	final public function __toString() {
	    return $this->_instance;
	}
}
	