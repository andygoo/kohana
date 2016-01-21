<?php defined('SYSPATH') or die('No direct script access.');

Route::set('weixin_oauth', '<directory>(/<controller>(/<action>))', array('directory' => 'weixin', 'controller' => '(oauth|server)'))
->defaults(array(
'controller' => 'Oauth',
'action'     => 'index',
));