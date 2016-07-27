<?php defined('SYSPATH') or die('No direct script access.');

Route::set('media', 'media(/<action>)/<file>.<format>', array('action'=>'(index|minicss|minijs)', 'file'=>'[a-zA-Z0-9\.\-_/]+', 'format'=>'[a-zA-Z0-9]+'))
	->defaults(array(
		'controller' => 'media',
		'action'     => 'index',
	));
