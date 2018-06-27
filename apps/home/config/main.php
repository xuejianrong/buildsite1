<?php
$params = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
    //require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'home',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'home\controllers',
    'runtimePath' => PROJECT_PATH . '/runtime/home',
    'components' => [
		'view' => [
			'commonTitle' => '广东锐进广告有限公司',
			'baseTitle' => 'XXX-X-XX-XC-basetitle',
		],
    ],
	'layout' => 'main',
	'urlManagerName' => 'urlManagerHome',
//	'catchAll' => [
//        'remind/close-website-remind',
//		'words' => '',
//		'start_time' => 0,
//		'end_time' => 0,
//    ],
    'params' => $params,
];
