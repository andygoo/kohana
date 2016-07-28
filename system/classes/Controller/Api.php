<?php

class Controller_Api extends Controller {
    
    public $auto_render = false;
    public $response = array('errno'=>0, 'data'=>array());
    public $format = 'json';

    public function __construct(Request $request) {
        parent::__construct($request);

        $this->format = $this->request->param('format');
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
} 
