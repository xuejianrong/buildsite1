<?php
return [
	'master' => [
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => '123456',
		'node' => [
			['dsn' => 'mysql:host=127.0.0.1;dbname=buildsite1'],
		],
	],
	'slaver' => [
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => '123456',
		'node' => [
			['dsn' => 'mysql:host=127.0.0.1;dbname=buildsite1'],
		],
	],
];
