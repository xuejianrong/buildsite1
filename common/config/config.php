<?php
/*************************************************************************************************/
//域名主体部分配置，取域名中间部分，例如：www.abc.com，则为abc; www.abc.def.com，则为abc.def
$_domain_host_name = '';

//项目的运行环境
$_projectEnv = 'prod';

//项目的运行环境对应的域名后缀
$_aDomainSuffixConfig = [
	'dev' => 'dev',
	'test' => 'test',
	'prod' => 'com',
];

_InitProjectEnv_($_projectEnv, $_domain_host_name, $_aDomainSuffixConfig);

//应用列表标识和应用url配置
$_aWebAppList = [
	'home' => 'http://www.' . $_domain_host_name . '.' . $_aDomainSuffixConfig[$_projectEnv],
	'manage' => 'http://www.' . $_domain_host_name . '.' . $_aDomainSuffixConfig[$_projectEnv],
];

//静态资源url配置
$_resourceUrl = 'http://www.' . $_domain_host_name . '.' . $_aDomainSuffixConfig[$_projectEnv] . '/resource';

//是否开启数据库缓存
$_dbcache_is_active = 0;

/*************************************************************************************************/


function _InitProjectEnv_(&$_projectEnv, &$_domain_host_name, $_aDomainSuffixConfig){
	$aDomainExplode1 = explode('.', $_SERVER['SERVER_NAME']);
	$aDomainExplode2 = explode('.', $_SERVER['HTTP_HOST']);
	$_aDomainSuffix = [array_pop($aDomainExplode1), array_pop($aDomainExplode2)];
	foreach($_aDomainSuffixConfig as $k => $v){
		if(in_array($v, $_aDomainSuffix)){
			$_projectEnv = $k;
			break;
		}
	}
	if(!$_domain_host_name){
		$_domain_host_name = array_pop($aDomainExplode2);
	}
}

defined('PROJECT_PATH') || define('PROJECT_PATH', dirname(dirname(__DIR__)));
defined('FRAMEWORK_PATH') || define('FRAMEWORK_PATH', PROJECT_PATH . '/framework');

$aLocal = [
	'is_debug' => true,
	'env' => $_projectEnv,
	'domain_host_name' => $_domain_host_name,
	'domain_suffix' => $_aDomainSuffixConfig,
	'db' => [
		'dev' => require(PROJECT_PATH . '/common/config/db-dev.php'),
		'test' => require(PROJECT_PATH . '/common/config/db-test.php'),
		'prod' => require(PROJECT_PATH . '/common/config/db-prod.php'),
	],
	'aWebAppList' => $_aWebAppList,
	'resourceUrl' => $_resourceUrl,
	'cache' => require(PROJECT_PATH . '/common/config/cache.php'),
	'dbcacheIsActive' => $_dbcache_is_active,
	'temp' => [],
];


if(isset($_SERVER['SERVER_ADDR'])){
	if($_SERVER['SERVER_ADDR'] == '192.168.1.202'){
		$aLocal['env'] = 'test';
		$aLocal['cache']['redis']['part']['login'] = 4;
	}elseif($_SERVER['SERVER_ADDR'] == '115.159.155.71'){
		$aLocal['env'] = 'prod';
	}
}

if(!class_exists('Yii')){
	defined('YII_DEBUG') || define('YII_DEBUG', $aLocal['is_debug']);
	defined('YII_ENV') || define('YII_ENV', $aLocal['env']);
	require(FRAMEWORK_PATH . '/autoload.php');
	require(FRAMEWORK_PATH . '/yiisoft/yii2/Yii.php');
	require(PROJECT_PATH . '/common/config/bootstrap.php');
}