<?php defined('SYSPATH') OR die('No direct script access.');

abstract class Database {

	public $last_query;
	public static $instances = array();
	
	protected $_conn;
	protected $_config;
	protected $_instance;
	
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
	}

	abstract public function connect();
	abstract public function query($sql);
	abstract public function escape($value);

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

	public function multi_insert($table, $datas, $size=500) {
	    $chunks = array_chunk($datas, $size);
	    foreach ($chunks as $chunk) {
            $fields = implode('`,`', array_keys($chunk[0]));
    	    $values = array();
    	    foreach($chunk as $data) {
    		    $data = array_map(array($this, 'escape'), $data);
    	        $values[] = implode(',', array_values($data));
    	    }
    	    $values = implode('),(', $values);

    	    $sql = "INSERT INTO {$table}(`$fields`) VALUES($values)";
    	    $this->query($sql);
	    }
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
	