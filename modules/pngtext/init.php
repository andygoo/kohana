<?php defined('SYSPATH') or die('No direct script access.');

Route::set('pngtext', 'pngtext')
	->defaults(array(
		'controller' => 'pngtext',
		'action'     => 'index',
	));
