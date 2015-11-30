<?php

class Controller_Api extends Controller {
    
    public $auto_render = false;
    public $response = array('errno'=>0, 'data'=>array());
    public $format = 'json';

    public function __construct(Request $request) {
        parent::__construct($request);

        $this->format = $this->request->param('format');
        $controller = $this->request->controller;
        $action = $this->request->action;
        
        $path = $this->request->param('path');
        $pathinfo = pathinfo($path);
        if (!empty($pathinfo['extension'])) {
            $this->format = $pathinfo['extension'];
        }
        
        if (!method_exists('Controller_' . ucfirst($controller), 'action_' . $action)) {
            $this->request->action = 'error';
        }
    }

    public function before() {
        if ($this->format == 'xml') {
            header('Content-Type: application/xml; charset=utf-8');
        } else {
            header('Content-Type: application/json; charset=utf-8');
        }
    }

    public function after() {
        if ($this->format == 'xml') {
            echo Arr::toxml($this->response, 'response');
        } else {
            echo json_encode($this->response, JSON_UNESCAPED_UNICODE);
        }
    }

    public function action_error() {
        $this->response = array('errno'=>-1, 'errmsg'=>'This method does not exist!');
    }
} 
