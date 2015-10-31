<?php
return array(
    'default_' => array(
        'type' => 'PDO',
        'connection' => array(
            'dsn' => 'mysql:host=localhost;dbname=admin',
            'username' => 'root',
            'password' => 'root',
            'persistent' => TRUE 
        ) 
    ),
    'default' => array(
        'type' => 'PDO',
        'master' => array(
            'connection' => array(
                'dsn' => 'mysql:host=localhost;dbname=admin',
                'username' => 'root',
                'password' => 'root',
                'persistent' => TRUE 
            ) 
        ),
        'slave' => array(
            'connection' => array(
                'dsn' => 'mysql:host=localhost;dbname=admin',
                'username' => 'root',
                'password' => 'root',
                'persistent' => TRUE 
            ) 
        ) 
    ) 
);