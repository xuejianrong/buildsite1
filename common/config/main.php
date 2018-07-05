<?php
return [
    'vendorPath' => FRAMEWORK_PATH,
    'domain' => $aLocal['domain_host_name'] . '.' . $aLocal['domain_suffix'][YII_ENV],
    'aWebAppList' => array_keys($aLocal['aWebAppList']),
    'language' => 'zh-CN',
    'bootstrap' => ['log'],
	'defaultRoute' => 'site/index',
//	'catchAll' => [
//        'remind/close-website-remind',
//		'words' => '',
//		'start_time' => 0,
//		'end_time' => 0,
//    ],
    'components' => require(__DIR__ . '/components.php'),
];
