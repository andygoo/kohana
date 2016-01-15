<?php

abstract class Controller {
    public $request;
    public $template = 'template';
    public $auto_render = true;

    public function __construct(Request $request) {
        $this->request = $request;
        //Request::$theme = 'mobile';
        
        if (Request::$is_ajax) {
            $this->auto_render = FALSE;
        }
    }

    public function before() {
        if ($this->auto_render === TRUE) {
            $this->template = View::factory($this->template);
        } else {
            $this->template = new stdClass();
        }
    }

    public function after() {
        if ($this->auto_render === TRUE) {
            $this->request->response = $this->template;
        }
    }

    public function redirect($uri = '', $code = 302) {
        if ($this->auto_render === TRUE) {
            $this->request->redirect($uri, $code);
        } else {
            if (strpos($uri, '://') === FALSE) {
                $uri = URL::site($uri);
            }
            echo json_encode(array('code'=>302, 'url'=>$uri));
            exit;
        }
    }
}
