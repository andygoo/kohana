<?php defined('SYSPATH') or die('No direct script access.');

Route::set('media', 'media/<file>.<format>', array('file'=>'[a-zA-Z0-9\.\-_/]+', 'format'=>'[a-zA-Z0-9]+'))
	->defaults(array(
		'controller' => 'media',
		'action'     => 'index',
	));
	
