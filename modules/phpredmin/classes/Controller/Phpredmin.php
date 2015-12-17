<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Phpredmin extends Controller {

	public $template = 'phpredmin/layout';

	protected $_redis;
    protected $content;

	public function before() {
	    parent::before();

	    $this->_redis = new Redis();
	    
	    $config = Kohana::config('redis.default');
	    $this->_redis->connect($config['host'], $config['port']);
	    
	    $info = $this->_redis->info();
	    $dbs = $this->getDbs($info);
	    
	    $curr_db = $this->request->param('db');
	    if ($curr_db !== null) {
	        $this->_redis->select($curr_db);
	    }
	    
	    View::bind_global('dbs', $dbs);
	    View::bind_global('curr_db', $curr_db);
	    View::bind_global('info', $info);
	    View::bind_global('action', $this->request->action);
	    View::bind_global('content', $this->content);
	}
	
	public function action_index() {
	    $config = $this->_redis->config('GET', '*');
	    
	    $this->content = View::factory('phpredmin/index');
	    $this->content->config = $config;
	}
	
	public function action_keys() {
	    $k = Arr::get($_GET, 'key', '');
	    $k = trim($k);
        $_keys = $this->_redis->keys($k);
        if (empty($_keys)) {
            $_keys = array();
        }
        
        $total = count($_keys);
        $pager = new Pager($total, 10);
        $_keys = array_slice($_keys, $pager->offset, $pager->size);
        
        $keys = array();
        foreach ($_keys as $key) {
            $item = array();
            $item['key'] = $key;
            $item['type'] = $this->getType($key);
            $item['ttl'] = $this->getTTL($key);
            $item['ttl2'] = $this->_redis->ttl($key);
            $item['encode'] = $this->getEncoding($key);
            $item['size'] = $this->getSize($key);
            $keys[] = $item;
        }
        
	    $this->content = View::factory('phpredmin/keys');
	    $this->content->pager = $pager->render('phpredmin/pager');
	    $this->content->keys = $keys;
	}
	
	public function action_view() {
	    $key = Arr::get($_GET, 'key', '');
	    
	    $total = 1;
	    $type = $this->getType($key);
	    if($type == 'List') {
    	    $total = $this->_redis->lSize($key);
	    } elseif ($type == 'ZSet') {
	        $total = $this->_redis->zCard($key);
	    }
	    $pager = new Pager($total, 2);
	    $values = $this->getValue($key, $pager->offset, $pager->size);
	    
	    $content = View::factory('phpredmin/view');
	    $content->pager = $pager->render('phpredmin/pager');
	    $content->type = $type; 
	    $content->values = $values; 
	    echo $content;
	    exit;
	}

	public function action_del() {
	    $key = Arr::get($_GET, 'key', '');
	    $ret = $this->_redis->del($key);
	    if ($ret !== false) {
	        $this->redirect(Request::$referrer);
	    }
	}

	public function action_expire() {
	    $updated = null;
	    if (!empty($_POST)) {
    	    $ttl = Arr::get($_POST, 'value');
    	    $ttl = intval($ttl);
    	    $key = Arr::get($_POST, 'pk');
	        $oldttl  = $this->_redis->ttl($key);
	    
	        if ($ttl > 0) {
	            $updated = $this->_redis->expire($key, $ttl);
	        } elseif ($oldttl > 0) {
	            $updated = $this->_redis->persist($key);
	        } else {
	            $updated = true;
	        }
		    header('Content-Type: application/json; charset=utf-8');
	        $ret = $updated ? array('success'=>true) : array('success'=>false, 'msg'=>'error');
	        echo json_encode($ret);
	    }
	    exit;
	}
	
	public function after() {
	    parent::after();
	
	    if ($this->auto_render !== TRUE) {
	        $this->request->response = (string)$this->content;//->render();
	    }
	}
	
	public function getDbs($info) {
	    $result = array();
	    $keys   = array_keys($info);
	    foreach ($keys as $db) {
	        if (preg_match('/^db([0-9]+)$/', $db, $matches)) {
                if (preg_match('/^keys=([0-9]+),expires=([0-9]+)/', $info[$db], $matches2)) {
	                $result[] = array('db'=>$matches[1], 'keys'=>$matches2[1]);
                }
	        }
	    }
	    return $result;
	}

	public function getType($key) {
	    switch($this->_redis->type($key)) {
	        case Redis::REDIS_STRING:
	            return 'String';
	        case Redis::REDIS_SET:
	            return 'Set';
	        case Redis::REDIS_LIST:
	            return 'List';
	        case Redis::REDIS_ZSET:
	            return 'ZSet';
	        case Redis::REDIS_HASH:
	            return 'Hash';
	        default:
	            return '-';
	    }
	}
	
	public function getTTL($key) {
	    return $this->_time($this->_redis->ttl($key));
	}
	
	public function getEncoding($key) {
	    return $this->_redis->object("encoding", $key);
	}
	
	public function getSize($key) {
	    switch($this->_redis->type($key)) {
	        case Redis::REDIS_LIST:
	            $size = $this->_redis->lSize($key);
	            break;
	        case Redis::REDIS_SET:
	            $size = $this->_redis->sCard($key);
	            break;
	        case Redis::REDIS_HASH:
	            $size = $this->_redis->hLen($key);
	            break;
	        case Redis::REDIS_ZSET:
	            $size = $this->_redis->zCard($key);
	            break;
	        case Redis::REDIS_STRING:
	            $size = $this->_redis->strlen($key);
	            break;
	        default:
	            $size = '-';
	    }
	
	    return $size <= 0 ? '-' : $size;
	}
	
	protected function _time($time) {
	    if ($time <= 0) {
	        return '-';
	    } else {
	        $days = floor($time / 86400);
	        return ($days > 0 ? "{$days} Days " : '') . gmdate('H:i:s', $time);
	    }
	}
	
	public function getValue($key, $offset=0, $size=10) {
	    switch($this->_redis->type($key)) {
	        case Redis::REDIS_STRING:
	            return $this->_redis->get($key);
	        case Redis::REDIS_SET:
	            return $this->_redis->sMembers($key);
	        case Redis::REDIS_LIST:
                return $this->_redis->lRange($key, $offset, $offset + $size-1);
	        case Redis::REDIS_ZSET:
                //return $this->_redis->zRange($key, $offset, $offset + $size-1, true);
                return $this->_redis->zrevrange($key, $offset, $offset + $size-1, true);
	        case Redis::REDIS_HASH:
	            return $this->_redis->hGetAll($key);
	        default:
	            return '-';
	    }
	}
}