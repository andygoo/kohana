<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_Captcha extends Controller {

	public $auto_render = FALSE;

	public function action_index() {
	    $group = $this->request->param('group', 'alpha');
		Captcha::instance($group)->render();
	}

	public function after() {
		Captcha::instance()->update_response_session();
	}
}
