<?php 

class Cache_Redis extends Cache {
    
	protected $_redis = null;
	
	public function __construct($config) {
		parent::__construct($config);
		
		$servers = $this->_config['servers'];
		$server = array_shift($servers);
		$this->_redis = new Redis();
		$this->_redis->connect($server['host'], $server['port']);
	}

	public function __call($method, $args) {
		return call_user_func_array(array($this->_redis, $method), $args);
	}

	public function get($id, $default = null) {
		$value = $this->_redis->get($this->_sanitize_id($id));
		if ($value === false) {
			$value = (null === $default) ? null : $default;
		}
		return $value;
	}
	
	public function set($id, $data, $lifetime = 3600) {
		$id = $this->_sanitize_id($id);
		$this->_redis->setex($id, $lifetime, $data);
		return true;
	}
	
	public function delete($id) {
		$id = $this->_sanitize_id($id);
		return $this->_redis->del($id);
	}

	public function delete_all() {
		return $this->_redis->flushdb();
	}
}
