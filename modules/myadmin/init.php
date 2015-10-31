<?php defined('SYSPATH') or die('No direct script access.');

Route::set('myadmin','myadmin(/<action>(/<database>(/<table>)))')
	->defaults(array(
		'controller' => 'Myadmin',
		'action'     => 'index'
	));