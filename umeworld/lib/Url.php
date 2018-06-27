<?php
namespace umeworld\lib;

use Yii;
//class Url extends \yii\helpers\BaseUrl{
class Url extends \yii\base\Object{
	/*public static function to($xUrl = '', $scheme = false){
		if(is_array($xUrl) && $xUrl[0][0] != '/'){
			//自动添加根URL标识，以防底层toRoute的时候自动添加模块名称
			$xUrl[0] = '/' . $xUrl[0];
		}

		$result = parent::to($xUrl, $scheme);
		if(!$scheme){
			$result = str_replace(\Yii::$app->getUrlManager()->baseUrl, '', $result);
		}

		return $result;
	}*/
	
	public static function to($appName, $key, $aParams = []){
		$fileName = Yii::getAlias('@apps/' . $appName . '/config/url.php');
		$aUrlConfigList = [];
		if(is_file($fileName)){
			$aUrlConfigList = require($fileName);
		}
		foreach($aUrlConfigList['rules'] as $i => $j){
			if($j == $key){
				$url = $i;
				$aMatch = static::_getMatchTagList($url);
				if(static::_compare($aMatch, $aParams)){
					foreach($aMatch as $t => $r){
						$url = str_replace($r, $aParams[$t], $url);
					}
					return $aUrlConfigList['baseUrl'] . '/' . $url;
				}
			}
		}
		
		return $aUrlConfigList['baseUrl'];
	}
	
	private static function _compare($aParams, $aMatch){
		foreach($aParams as $p => $q){
			if(!isset($aMatch[$p])){
				return false;
			}
		}
		foreach($aMatch as $m => $n){
			if(!isset($aParams[$m])){
				return false;
			}
		}
		return true;
	}
	
	private static function _getMatchTagList($str){
		$aMatch = [];
		$tag = '';
		$param = '';
		$tagFlag = false;
		$paramFlag = false;
		for($j = 0; $j < strlen($str); $j++){
			if($str[$j] == ':'){
				$paramFlag = false;
			}
			if($str[$j] == '>'){
				$tagFlag = false;
				$paramFlag = false;
				$aMatch[$param] = $tag . $str[$j];
				$param = '';
				$tag = '';
			}
			if($tagFlag){
				$tag .= $str[$j]; 
				if($paramFlag){
					$param .= $str[$j]; 
				}
			}
			if($str[$j] == '<'){
				$tagFlag = true;
				$paramFlag = true;
				$tag .= $str[$j];
			}
		}
		return $aMatch;
	}
}