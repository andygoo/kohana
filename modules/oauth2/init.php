<?php

Route::set('oauth', 'oauth/<controller>(/<action>)')
->defaults(array(
    'controller' => 'github',
    'action' => 'index' 
));