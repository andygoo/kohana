<?php

abstract class Controller_Ajax extends Controller {
    
    public $auto_render = false;
    public $response = '';

    public function __construct(Request $request) {
        parent::__construct($request);
        
        if (!Request::$is_ajax) {
            //header('HTTP/1.1 403 Forbidden');
            exit;
        }
    }

    public function before() {
        header('Content-Type: application/json; charset=utf-8');
    }

    public function after() {
        if (!empty($this->response)) {
            echo json_encode($this->response, JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
} 
