<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class Setting extends \common\lib\DbOrmModel{
	const SITE_SETTING_CACHE_KEY = 'site_setting';
	const FRIENDS_LINK_CACHE_KEY = 'friends_link';
	const COOPERATIVE_PARTNERS_CACHE_KEY = 'cooperative_partners';
	const CONTACTUS_CACHE_KEY = 'contactus';
	const ABOUTUS_CACHE_KEY = 'aboutus';
	const SITE_TEMPLATE_CACHE_KEY = 'site_template';
	const ZHAOPIN_CACHE_KEY = 'zhaopin';
	const ZHAOPIN_TALENT_CONCEPT_CACHE_KEY = 'zhaopin_zhaopin_talent';
	
	public static function getSetting($key, $defaultValue = false){
		$rs = Yii::$app->fileCache->get($key);
		return $rs ? $rs : $defaultValue;
	}
	
	public static function setSetting($key, $value, $expireTime = 0){
		$bakHistoryKey = 'bak_history';
		$aBakHistoryKey = static::getSetting($bakHistoryKey, []);
		$bakKey = $key . '_bak_' . date('YmdHis');
		$bakExpireTime = 864000;
		//备份保留10天
		$aSetting = static::getSetting($key);
		if($aSetting){
			array_push($aBakHistoryKey, $bakKey);
			Yii::$app->fileCache->set($bakKey, $aSetting, $bakExpireTime);
			Yii::$app->fileCache->set($bakHistoryKey, $aBakHistoryKey, 8640000);
		}
		Yii::$app->fileCache->set($key, $value, $expireTime);
	}
	
}