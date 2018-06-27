<?php
error_reporting(-1);
$appPath = PROJECT_PATH;
Yii::setAlias('common', dirname(__DIR__));
Yii::setAlias('apps', dirname(dirname(__DIR__)) . '/apps');

//APP别名设置 start
if(!YII_ENV_PROD){
	Yii::setAlias('dev', $appPath . '/apps/dev');
}
$domainSuffix = $aLocal['domain_suffix'][YII_ENV];
$domainHostName = $aLocal['domain_host_name'];

Yii::setAlias('home',				$appPath . '/apps/home');
Yii::setAlias('url.home',			'http://www.' . $domainHostName . '.' . $domainSuffix);

Yii::setAlias('manage',				$appPath . '/apps/manage');
Yii::setAlias('url.manage',			'http://www.' . $domainHostName . '.' . $domainSuffix);

//APP别名设置 end

Yii::setAlias('umeworld',			$appPath . '/umeworld');
Yii::setAlias('r.url', 'http://www.' . $domainHostName . '.' . $domainSuffix . '/resource');
$aLocal['resource_url'] = Yii::getAlias('@r.url');
Yii::setAlias('p.resource',		$appPath . '/resource');
Yii::setAlias('p.system_view',		$appPath . '/common/views/system');
Yii::setAlias('@p.user_profile', 'data/user/profile/');
Yii::setAlias('@p.temp_upload', 'data/temp/');
Yii::setAlias('@p.uploads', 'data/uploads/');

defined('NOW_TIME') || define('NOW_TIME', time());
unset($appPath, $domainSuffix);


/**
 * 调试输出函数
 * @param type mixed $xData 要调试输出的数据
 * @param type int $mode 11=输出并停止运行,111=停止并输出运行轨迹,12=以PHP代码方式输出,13=dump方式输出,其中第十位数为0的时候表示不停止运行,前面的参数样例十位都是1所以会停止运行,个位用于控制输出模式 @see \umeworld\lib\Debug
 */
function debug($xData, $mode = null){
	if($mode === null){
		$mode = \umeworld\lib\Debug::MODE_NORMAL;
	}
	\umeworld\lib\Debug::dump($xData, $mode, true);
}

function words($keyName, $aReplacement = []){
	return Yii::$app->lang->words($keyName, $aReplacement);
}

if(isset($_GET['__SQS'])){
	unset($_GET['__SQS']);
}
