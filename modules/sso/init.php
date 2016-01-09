<?php defined('SYSPATH') or die('No direct script access.');


Route::set('ssoserver', 'sso/<action>/<broker>/<token>/<checksum>')->defaults(array('controller'=>'sso'));

SSO::init();