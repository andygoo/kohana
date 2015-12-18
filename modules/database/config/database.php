<?php
defined('SYSPATH') or die('No direct access allowed.');

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
    'default_' => array(
        'type' => 'MySQL',
        'connection' => array(
            'hostname' => 'localhost',
            'database' => 'kohana',
            'username' => 'root',
            'password' => 'root',
            'persistent' => FALSE 
        ) 
    ),
    'alternate' => array(
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
    'mssql' => array(
        'type' => 'MsSQL',
        'connection' => array(
            'dsn' => 'dblib:host=hostname;dbname=database',
            'username' => 'root',
            'password' => 'root',
            'persistent' => TRUE 
        ) 
    ) 
);