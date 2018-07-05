<?php
return [
	'redis' => [
		'host'		=>	'127.0.0.1',
		'port'		=>	'6379',
		'password'	=>	'',
		'server_name' => 'redis_1',
		'part' => [
			'data' => 5,
			'login' => 6,
			'temp' => 7,
		],
	],

	'redisCache' => [
		'host'		=>	'127.0.0.1',
		'port'		=>	'6379',
		'password'	=>	'',
		'server_name' => 'redis_1',
		'part' => 7,
	],
];