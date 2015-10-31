<?php defined('SYSPATH') or die('No direct script access.');

Route::set('imagefly', 'imagefly/<params>/<imagepath>', array('imagepath' => '.*'))
    ->defaults(array(
        'controller' => 'ImageFly',
    ));
    
