<?php
return [
	'bootstrap' => [
		//'debug',
		//'gii',
	],
	'modules' => [
		//'debug' => 'yii\debug\Module',
		//'gii' => 'yii\gii\Module',
	],
    'components' => [
		'view' => [
			/*'on beginBody' => function(){
				$jsLogUrl = 'http://192.168.1.202:9301/addjserrorlog.php';
				echo '<script type="text/javascript">window.App && App.importUrl({add_js_log : "' . $jsLogUrl . '"});</script>';
			},*/
		],


		'log' => [
			'targets' => [
                /*[
					//登陆流水
                    'class' => 'umeworld\lib\FileLogTarget',
                    'levels' => ['info'],
					'categories' => ['login', 'rebuild-login-status'],
					'logVars' => [],
					'logFile' => '@runtime/../login_' . $aLocal['temp']['todayLog'],
                ],*/
                /*[
					//登陆流水
                    'class' => 'umeworld\lib\FileLogTarget',
                    'levels' => ['info'],
					'categories' => ['gd_xxt_login'],
					'logVars' => [],
					'logFile' => '@runtime/../xxt_gd_token_record_' . $aLocal['temp']['todayLog'],
                ],*/
			],
		],
	],
];
