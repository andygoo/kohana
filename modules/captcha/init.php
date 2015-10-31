<?php defined('SYSPATH') or die('No direct script access.');

Route::set('captcha', 'captcha(/<group>)')
	->defaults(array(
		'controller' => 'captcha',
		'action' => 'index',
		));
