<?php defined('SYSPATH') or die('No direct script access.');

Route::set('sms', 'sms/<action>', array('action' => '(send)'))
	->defaults(array(
		'controller' => 'sms',
		'action' => 'send',
		));
