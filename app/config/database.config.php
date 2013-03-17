<?php

/**
 * Array with the configuration for database. Array specifications:
 *
 * array (
 * 	'[PROFILE]'	=> array (
 * 		'[ENVIRONMENT]'	=> array (
 *			'host'	=> 'xxxxxxxxxx',
 *			'user'	=> 'xxxxxxxxxx',
 *			'pass'	=> 'xxxxxxxxxx',
 *			'dbname'	=> 'xxxxxx',
 *			'charset'	=> 'xxxxxx'
 *		),
 *		...
 *	),
 *	...
 * )
 * @var array $config.
 */

// 'futbol' Profile
$config['football']	= array (

	// 'DEV' Environment
	'DEV'		=> array (
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'dbname'	=> 'football',
		'charset'	=> 'utf8'
	),

	// 'PROD' Environment
	'PRO'	=> array (
		'host'	=> 'localhost',
		'user'	=> 'root',
		'pass'	=> 'root',
		'dbname'	=> 'football',
		'charset'	=> 'utf8'
	)
);
