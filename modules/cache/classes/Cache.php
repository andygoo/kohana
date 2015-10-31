<?php

abstract class Cache {
    
    public static $default = 'redis';
    public static $instances = array();
    
    protected $_config;

    public static function instance($group = NULL) {
        if ($group === NULL) {
            $group = Cache::$default;
        }
        
        if (isset(Cache::$instances[$group])) {
            return Cache::$instances[$group];
        }
        
        $config = Kohana::config('cache.' . $group);
        $cache_class = 'Cache_' . ucfirst($config['driver']);
        Cache::$instances[$group] = new $cache_class($config);
        
        return Cache::$instances[$group];
    }

    protected function __construct(array $config) {
        $this->_config = $config;
    }

    abstract public function get($id, $default = NULL);
    abstract public function set($id, $data, $lifetime = 3600);
    abstract public function delete($id);
    abstract public function delete_all();

    protected function _sanitize_id($id) {
        return str_replace(array('/', '\\', ' '), '_', $id);
    }
}