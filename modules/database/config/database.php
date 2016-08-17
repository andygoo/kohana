<?php

return array(
    'default' => array(
        'type' => 'PDO',
        'connection' => array(
            'dsn' => 'mysql:host=localhost;dbname=kohana',
            'username' => 'root',
            'password' => 'root',
            'persistent' => TRUE 
        ) 
    ),
    'mysqli' => array(
        'type' => 'MySQLi',
        'connection' => array(
            'hostname' => 'localhost',
            'database' => 'kohana',
            'username' => 'root',
            'password' => 'root',
        ) 
    ),
    'mysql' => array(
        'type' => 'MySQL',
        'connection' => array(
            'hostname' => 'localhost',
            'database' => 'kohana',
            'username' => 'root',
            'password' => 'root',
            'persistent' => FALSE 
        ) 
    ),
    'mssql' => array(
        'type' => 'MsSQL',
        'connection' => array(
            'dsn' => 'dblib:host=hostname;dbname=database',
            'username' => 'root',
            'password' => 'root',
            'persistent' => TRUE 
        ) 
    ), 
    'master_slave' => array(
        'type' => 'PDO',
        'master' => array(
            'connection' => array(
                'dsn' => 'mysql:host=localhost;dbname=kohana',
                'username' => 'root',
                'password' => 'root',
                'persistent' => TRUE 
            ) 
        ),
        'slave' => array(
            'connection' => array(
                'dsn' => 'mysql:host=localhost;dbname=kohana',
                'username' => 'root',
                'password' => 'root',
                'persistent' => TRUE 
            ) 
        ) 
    ),
);