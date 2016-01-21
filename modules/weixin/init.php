<?php defined('SYSPATH') or die('No direct script access.');

Route::set('weixin_oauth', 'weixin(/<controller>(/<action>))', array('directory' => 'weixin', 'controller' => '(oauth|reply)'))
->defaults(array(
'controller' => 'oauth',
'action'     => 'index',
));