<?php

return array(
	'redis' => array(
		'driver'             => 'redis',
		'default_expire'     => 3600,
		'servers'            => array(
			array(
				'host'       => 'localhost', 
				'port'       => 6379, 
			),
		),
	),
	'memcache' => array(
		'driver'             => 'memcache',
		'default_expire'     => 3600,
		'compression'        => FALSE,        // Use Zlib compression (can cause issues with integers)
		'servers'            => array (
			array (
				'host'       => 'localhost',  
				'port'       => 11211,
				'persistent' => FALSE, 
			),
		),
		'default_expire'     => 3600,
	),
    'sqlite'   => array(
        'driver'             => 'sqlite',
        'default_expire'     => 3600,
        'database'           => APPPATH.'cache/kohana-cache.sql3',
        'schema'             => 'CREATE TABLE caches(id VARCHAR(127) PRIMARY KEY, tags VARCHAR(255), expiration INTEGER, cache TEXT)',
    ),
    'file'    => array(
        'driver'             => 'file',
        'cache_dir'          => APPPATH.'cache',
        'default_expire'     => 3600,
        'ignore_on_delete'   => array(
            '.gitignore',
            '.git',
            '.svn'
        ),
    ),
);