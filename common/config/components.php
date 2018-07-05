<?php
$_urlManagerList = [
	//'urlManagerHome' => require(Yii::getAlias('@home') . '/config/url.php'),
	//'urlManagerManage' => require(Yii::getAlias('@manage') . '/config/url.php'),
];
foreach($aLocal['aWebAppList'] as $webApp => $appUrl){
	$_urlManagerList['urlManager' . ucwords($webApp)] = require(Yii::getAlias('@' . $webApp) . '/config/url.php');
}

return array_merge($_urlManagerList, [
	'request' => [
		'cookieValidationKey' => 'EArv76QW-Dc8ngUP-qndrD0BDlodbqw-',
	],

	'assetManager' => [
		'bundles' => [
			'yii\web\JqueryAsset' => [
				'sourcePath' => null,
				'js' => []
			],
		]
	],
	
	'lang' => [
		'class' => 'umeworld\lib\Lang',	//语言组件
	],

	'response' => [
		'class' => 'yii\web\Response',
		'format' => 'html',
	],

	'log' => require(__DIR__ . '/log.php'),

	'errorHandler' => [
		'class' => 'common\lib\ErrorHandler',
		'errorAction' => 'site/error',	//所有站点APP统一使用site控制器的error方法处理网络可能有点慢
	],

	'ui' => [
		'class' => 'umeworld\lib\BaseUi',
		'aTips' => [
			'error' => [
				'common' => '抱歉,系统繁忙,请重试',
			],
		],
	],

	'view' => [
		'class' => 'umeworld\lib\View',
		'on beginPage' => function(){
			Yii::$app->view->title = \yii\helpers\Html::encode(Yii::$app->view->title);

			Yii::$app->view->registerLinkTag([
				'rel' => 'shortcut icon',
				'href' => Yii::getAlias('@r.url') . '/favicon.ico',
			]);

			Yii::$app->view->registerMetaTag([
				'name' => 'csrf-token',
				'content' => Yii::$app->request->csrfToken,
			]);
			//http转https
			/*if(YII_ENV_PROD){
				header("Content-Security-Policy: upgrade-insecure-requests");
			}*/
			//加载语言配置js
			if(isset(Yii::$app->language) && Yii::$app->language){
				$link = str_replace('lang.data', 'lang.data.' . Yii::$app->language, Yii::getAlias('@r.js.lang.data'));
				$fileName = str_replace(Yii::getAlias('@r.url'), Yii::getAlias('@p.resource'), $link);
				if(file_exists($fileName)){
					$link .= '?v=' . date('YmdHis', filemtime($fileName));
				}
				echo '<script type="text/javascript" src="' . $link . '"></script>';
			}
		},

		'on endPage' => function(){
			// echo '<!--domainname';	//防止尾部运营商注入广告脚本,IE会显示半截标签，暂时屏蔽
		},
		'on endBody' => function(){
			// echo '<!--domainname';	//防止尾部运营商注入广告脚本,IE会显示半截标签，暂时屏蔽
			//http转https
			//echo \common\widgets\Https::widget();
		},
	],

   'db' => [
		'class' => 'umeworld\lib\Connection',
		'charset' => 'utf8',
		'aTables' => [
			/**
			 * 当你要求user表不使用缓存
			 * 'user' => 'cache:0'
			 *
			 * 当你的某个表不在主库mydb,而是在财务库mydb_recharge
			 * 'recharge' => 'table:mydb_recharge.recharge'		//以recharge为别名指向具体的数据库,必须有table:
			 *
			 * 既定义数据库的具体位置又定义是否缓存
			 * 'recharge' => 'table:db2.recharge;cache:0'	//这里增加了cache控制,1/0表示是否缓存数据,其实语法就像CSS一样
			 *
			 * 以后若有更多控制需求,可以增加"CSS属性"并在 umeworld\lib\Query::from 类里做解析代码
			 */
		],

		'masterConfig' => [
			'username' => $aLocal['db'][YII_ENV]['master']['username'],
			'password' => $aLocal['db'][YII_ENV]['master']['password'],
			'attributes' => [
				// use a smaller connection timeout
				PDO::ATTR_TIMEOUT => 10,
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
			],
		],

		'masters' => $aLocal['db'][YII_ENV]['master']['node'],

		'slaveConfig' => [
			'username' => $aLocal['db'][YII_ENV]['slaver']['username'],
			'password' => $aLocal['db'][YII_ENV]['master']['password'],
			'attributes' => [
				// use a smaller connection timeout
				PDO::ATTR_TIMEOUT => 10,
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
			],
		],

		'slaves' => $aLocal['db'][YII_ENV]['slaver']['node'],
	],

	'redis' => [
		'class' => 'umeworld\lib\RedisCache',
		'serverName' => $aLocal['cache']['redis']['server_name'],
		'dataPart'	=>	[
			'index'		=>	$aLocal['cache']['redis']['part']['data'],
			'is_active'	=>	$aLocal['dbcacheIsActive'],
		],
		'loginPart' =>	[
			'index'		=>	$aLocal['cache']['redis']['part']['login'],
			'is_active'	=>	$aLocal['dbcacheIsActive'],
		],
		'tempPart'	=>	[
			'index'		=>	$aLocal['cache']['redis']['part']['temp'],
			'is_active'	=>	$aLocal['dbcacheIsActive'],
		],
		'servers' => [
			'redis_1' => [
				'is_active' =>  $aLocal['dbcacheIsActive'],
				'host'		=>	$aLocal['cache']['redis']['host'],
				'port'		=>	$aLocal['cache']['redis']['port'],
				'password'	=>	$aLocal['cache']['redis']['password'],
			],
		],
	],

	'redisCache' => [
		'class' => 'umeworld\lib\RedisCache',
		'serverName' => $aLocal['cache']['redis']['server_name'],
		'dataPart'	=>	[
			'index'		=>	$aLocal['cache']['redis']['part']['default'],
			'is_active'	=>	$aLocal['dbcacheIsActive'],
		],
		'servers' => [
			'redis_1' => [
				'is_active' =>  $aLocal['dbcacheIsActive'],
				'host'		=>	$aLocal['cache']['redis']['host'],
				'port'		=>	$aLocal['cache']['redis']['port'],
				'password'	=>	$aLocal['cache']['redis']['password'],
			],
		],
	],
	
	'fileCache' => [
		'class' => '\yii\caching\FileCache',
		'cachePath' => PROJECT_PATH . '/cache',
		'directoryLevel' => 2,
		'gcProbability' => 1000000
	],

	'client' => [
		'class' => 'umeworld\helper\Client'
	],
	
	'authManager' => [
		'class' => 'common\role\AuthManager',
	],
	
	'user' => [
		'class' => 'common\role\UserRole',
		'identityClass' => 'common\model\User',
		'reloginOvertime' => 604800,//3600,
		'rememberLoginTime' => 604800,//3000000,
		'enableAutoLogin' => true,
		'loginUrl' => function(){
			exit('抱歉,登陆超时,请重新授权登陆');
		},
	],
	
	'manager' => [
		'class' => 'common\role\ManagerRole',
		'identityClass' => 'common\model\Manager',
		'reloginOvertime' => 604800,//3600,
		'rememberLoginTime' => 604800,//3000000,
		'enableAutoLogin' => true,
		'loginUrl' => function(){
			Yii::$app->response->redirect(\umeworld\lib\Url::to('manage', 'site/show-login'))->send();
			//exit('抱歉,登陆超时,请重新授权登陆');
		},
	],
	
	'siteSetting' => [
		'class' => 'common\model\SiteSetting',
	],

	'sms'=>[
		'class' => 'umeworld\lib\Sms',
		'username' => 'xxx2016',
		'password' => '1ab4263b0dfbff034bf6',
	],
	
	/**
		用法
		Yii::$app->mailer->compose()
			//->setFrom('from@domain.com')
			->setTo($account)
			->setSubject('验证码')
			->setTextBody($content)
			//$mail->setHtmlBody("<a>content</a>");  
			->send();
	*/
	'mailer' => [
		'class' => 'yii\swiftmailer\Mailer',
		'useFileTransport' =>false,	//这句一定有，false发送邮件，true只是生成邮件在runtime文件夹下，不发邮件
		'transport' => [
			'class' => 'Swift_SmtpTransport',
			'host' => 'smtp.qq.com',
			'username' => 'xxxxx@qq.com',
			'password' => 'owvyorxkzgsibebd',	//密码不是邮箱密码，而是登录授权码
			'encryption' => 'tls', //tls 或 ssl
			'port' => '587',	//原先是25，不行就用465 或 587
		],
		'messageConfig' => [
		   'charset' => 'UTF-8',
		   'from' => ['xxxx@qq.com' => 'Jay']
		],
		'htmlLayout' => '@common/views/mail/html-layout',
		'textLayout' => '@common/views/mail/text-layout',
	],
]);
