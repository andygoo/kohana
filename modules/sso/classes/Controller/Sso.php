<?php if (!defined('SYSPATH')) exit('No direct script access allowed');

class Controller_Sso extends Controller {

    public $auto_render = false;
    
    public function __construct(Request $request) {
        parent::__construct($request);
        
    	$broker    = $this->request->param('broker');
    	$token     = $this->request->param('token');
    	$checksum  = $this->request->param('checksum');

    	if (!SSO::check($broker, $token, $checksum)) {
            exit;
    	}
    }

    public function action_login() {
        var_dump('sdsd');
        Cookie::set('sso', '1');
    }
    
    public function action_logout() {
        Cookie::delete('sso');
    }

}
