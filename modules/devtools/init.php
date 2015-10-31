<?php defined('SYSPATH') or die('No direct script access.');

Route::set('devtools','devtools(/<action>)')
	->defaults(array(
		'controller' => 'Devtools',
		'action'     => 'info'
	));