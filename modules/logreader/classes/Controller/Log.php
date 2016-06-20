<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Log extends Controller {

    public $template = 'log/layout';
    
    protected $params;
    protected $content;
    
    public function before() {
        parent::before();
        
        $this->params = Arr::extract($this->request->param(), array('year','month','day'));
        
        View::bind_global('params', $this->params);
        View::bind_global('content', $this->content);
    }
    
    public function action_show() {
        /*/
        Kohana::$log->add(Log::ERROR, 'coverable error; nonrecoverable ble error');
        Kohana::$log->add(Log::DEBUG, 'coveraoverable eror; nonrecoverable eror');
        Kohana::$log->add(Log::INFO, 'coverabverable erable errror');
        Kohana::$log->add(Log::NOTICE, 'coverable error; nonrecole ble error');
        Kohana::$log->add(Log::WARNING, 'coverrrerror; nonreerror; nonrecoveracor');
        //*/
        //$rr .= 'fddf';
        
        $logreader = new LogReader();
        $log = $logreader->set_config($this->params);
        
        $levels = $log->get_levels();
        
        $level = Arr::get($_GET, 'level');
        $msgs = $log->get_messages($level);
        $msgs = array_reverse($msgs);
        
        $this->content = View::factory('log/show');
        $this->content->level = $level;
        $this->content->levels = $levels;
        $this->content->msgs = $msgs;
    }
    
	public function after() {
	    parent::after();
	
	    if ($this->auto_render !== TRUE) {
	        $this->request->response = (string)$this->content;
	    }
	}
} 
