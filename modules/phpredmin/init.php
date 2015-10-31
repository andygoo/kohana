<?php defined('SYSPATH') or die('No direct script access.');

Route::set('phpredmin','phpredmin(/<action>(/<db>))')
	->defaults(array(
		'controller' => 'phpredmin',
		'action'     => 'index'
	));