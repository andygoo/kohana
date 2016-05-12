<?php 

class Cache_Memcache extends Cache {
    
    const CACHE_CEILING = 2592000;
    
    protected $_memcache;
    
    /**
	 * Flags to use when storing values
	 *
	 * @var string
	 */
    protected $_flags;

    protected function __construct(array $config) {
        parent::__construct($config);
        
        $this->_memcache = new Memcache();
        $servers = $this->_config['servers'];
        
        $config = array(
            'host' => 'localhost',
            'port' => 11211,
            'persistent' => FALSE,
            'weight' => 1,
            'timeout' => 1,
            'retry_interval' => 15,
            'status' => TRUE,
            'failure_callback' => NULL 
        );
        
        foreach($servers as $server) {
            $server += $config;
            
            if (!$this->_memcache->addServer($server['host'], $server['port'], $server['persistent'], $server['weight'], $server['timeout'], $server['retry_interval'], $server['status'], $server['failure_callback'])) {
                throw new Kohana_Exception('Memcache could not connect to host \':host\' using port \':port\'', array(
                    ':host' => $server['host'],
                    ':port' => $server['port'] 
                ));
            }
        }
        
        $this->_flags = Arr::get($this->_config, 'compression', FALSE) ? MEMCACHE_COMPRESSED : FALSE;
    }
    
	public function __call($method, $args) {
		return call_user_func_array(array($this->_memcache, $method), $args);
	}

    public function get($id, $default = NULL) {
        $value = $this->_memcache->get($this->_sanitize_id($id));
        if ($value === FALSE) {
            $value = (NULL === $default) ? NULL : $default;
        }
        return $value;
    }

    public function set($id, $data, $lifetime = 3600) {
        if ($lifetime > Cache_Memcache::CACHE_CEILING) {
            $lifetime = Cache_Memcache::CACHE_CEILING + time();
        } elseif ($lifetime > 0) {
            $lifetime += time();
        } else {
            $lifetime = 0;
        }
        return $this->_memcache->set($this->_sanitize_id($id), $data, $this->_flags, $lifetime);
    }

    public function delete($id, $timeout = 0) {
        return $this->_memcache->delete($this->_sanitize_id($id), $timeout);
    }

    public function delete_all() {
        $result = $this->_memcache->flush();
        
        // We must sleep after flushing, or overwriting will not work!
        // @see http://php.net/manual/en/function.memcache-flush.php#81420
        sleep(1);
        
        return $result;
    }
    
    public function incr($id, $step = 1) {
		$id = $this->_sanitize_id($id);
        return $this->_memcache->increment($id, $step);
    }
    
    public function decr($id, $step = 1) {
		$id = $this->_sanitize_id($id);
        return $this->_memcache->decrement($id, $step);
    }
}