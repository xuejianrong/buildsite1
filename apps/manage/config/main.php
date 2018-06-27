<?php
$params = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
    //require(__DIR__ . '/params-local.php')
);
return [
    'id' => 'manage',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'manage\controllers',
    'runtimePath' => PROJECT_PATH . '/runtime/manage',
    'components' => [
		'view' => [
			'commonTitle' => '建站后台管理系统',
			'baseTitle' => 'XXX-X-XX-XC-basetitle',
		],
    ],
	'layout' => 'main',
	'urlManagerName' => 'urlManagerManage',
//	'catchAll' => [
//        'remind/close-website-remind',
//		'words' => '',
//		'start_time' => 0,
//		'end_time' => 0,
//    ],
    'params' => $params,
];
