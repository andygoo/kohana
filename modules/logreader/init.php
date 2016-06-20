<?php defined('SYSPATH') OR die('No direct script access.');

Route::set('log', 'log(/<action>(/<year>(/<month>(/<day>))))', array(
	'action' => '(show)',
	'year'   => '[0-9]{4}',
	'month'  => '[0-9]{2}',
	'day'    => '[0-9]{2}',
))->defaults(array(
	'controller' => 'log',
	'action'     => 'show',
	'year'       => date('Y'),
	'month'      => date('m'),
	'day'        => date('d'),
));
