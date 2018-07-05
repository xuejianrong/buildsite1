<?php
//数据库表缓存目前只有redis版本的，memcache还不支持，需要优化umeworld\lib\Query和umeworld\lib\DbCommand
return [
	'redis' => [
		'host'		=>	'127.0.0.1',
		'port'		=>	'6379',
		'password'	=>	'',
		'server_name' => 'redis_1',
		'part' => [
			'default' => 1,
			'data' => 2,
			'login' => 3,
			'temp' => 4,
		],
	],
	'memcache' => [
		[
			'host' => '127.0.0.1',
			'port' => 11211,
		],
	],
];