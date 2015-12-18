<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(

	'driver'       => 'File',
	'hash_method'  => 'sha256',
	'hash_key'     => '!@#$%#',
	'lifetime'     => 1209600,
	'session_key'  => 'auth_user',

	'users' => array(
		 'admin' => 'c42968cc52c2e8de6a52728d207e73b6134b8093515d79cee707498d7e2d9cf1',
	),

);
