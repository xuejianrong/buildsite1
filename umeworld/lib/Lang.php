<?php
namespace umeworld\lib;

use Yii;

class Lang extends \yii\base\Object{
	public $aLanguageCodeList = ['zh', 'zh-CN', 'zh-HK', 'zh-MO', 'zh-SG', 'zh-TW', 'en', 'en-AU', 'en-BZ', 'en-CA', 'en-CB', 'en-GB', 'en-IE', 'en-JM', 'en-NZ', 'en-PH', 'en-TT', 'en-US', 'en-ZA', 'en-ZW', 'af', 'af-ZA', 'ar', 'ar-AE', 'ar-BH', 'ar-DZ', 'ar-EG', 'ar-IQ', 'ar-JO', 'ar-KW', 'ar-LB', 'ar-LY', 'ar-MA', 'ar-OM', 'ar-QA', 'ar-SA', 'ar-SY', 'ar-TN', 'ar-YE', 'az', 'az-AZ', 'az-AZ', 'be', 'be-BY', 'bg', 'bg-BG', 'bs-BA', 'ca', 'ca-ES', 'cs', 'cs-CZ', 'cy', 'cy-GB', 'da', 'da-DK', 'de', 'de-AT', 'de-CH', 'de-DE', 'de-LI', 'de-LU', 'dv', 'dv-MV', 'el', 'el-GR', 'eo', 'es', 'es-AR', 'es-BO', 'es-CL', 'es-CO', 'es-CR', 'es-DO', 'es-EC', 'es-ES', 'es-ES', 'es-GT', 'es-HN', 'es-MX', 'es-NI', 'es-PA', 'es-PE', 'es-PR', 'es-PY', 'es-SV', 'es-UY', 'es-VE', 'et', 'et-EE', 'eu', 'eu-ES', 'fa', 'fa-IR', 'fi', 'fi-FI', 'fo', 'fo-FO', 'fr', 'fr-BE', 'fr-CA', 'fr-CH', 'fr-FR', 'fr-LU', 'fr-MC', 'gl', 'gl-ES', 'gu', 'gu-IN', 'he', 'he-IL', 'hi', 'hi-IN', 'hr', 'hr-BA', 'hr-HR', 'hu', 'hu-HU', 'hy', 'hy-AM', 'id', 'id-ID', 'is', 'is-IS', 'it', 'it-CH', 'it-IT', 'ja', 'ja-JP', 'ka', 'ka-GE', 'kk', 'kk-KZ', 'kn', 'kn-IN', 'ko', 'ko-KR', 'kok', 'kok-IN', 'ky', 'ky-KG', 'lt', 'lt-LT', 'lv', 'lv-LV', 'mi', 'mi-NZ', 'mk', 'mk-MK', 'mn', 'mn-MN', 'mr', 'mr-IN', 'ms', 'ms-BN', 'ms-MY', 'mt', 'mt-MT', 'nb', 'nb-NO', 'nl', 'nl-BE', 'nl-NL', 'nn-NO', 'ns', 'ns-ZA', 'pa', 'pa-IN', 'pl', 'pl-PL', 'pt', 'pt-BR', 'pt-PT', 'qu', 'qu-BO', 'qu-EC', 'qu-PE', 'ro', 'ro-RO', 'ru', 'ru-RU', 'sa', 'sa-IN', 'se', 'se-FI', 'se-FI', 'se-FI', 'se-NO', 'se-NO', 'se-NO', 'se-SE', 'se-SE', 'se-SE', 'sk', 'sk-SK', 'sl', 'sl-SI', 'sq', 'sq-AL', 'sr-BA', 'sr-BA', 'sr-SP', 'sr-SP', 'sv', 'sv-FI', 'sv-SE', 'sw', 'sw-KE', 'syr', 'syr-SY', 'ta', 'ta-IN', 'te', 'te-IN', 'th', 'th-TH', 'tl', 'tl-PH', 'tn', 'tn-ZA', 'tr', 'tr-TR', 'ts', 'tt', 'tt-RU', 'uk', 'uk-UA', 'ur', 'ur-PK', 'uz', 'uz-UZ', 'uz-UZ', 'vi', 'vi-VN', 'xh', 'xh-ZA', 'zu', 'zu-ZA'];
	public $aLanguageWordsDataList = [];
	private $lastEditTime = 0;
	
	public function init(){
		parent::init();
		foreach($this->aLanguageCodeList as $languageCode){
			$this->aLanguageWordsDataList[$languageCode] = $this->_loadLanguage($languageCode);
		}
		$saveFileName = Yii::getAlias('@p.resource') . '/data/temp/lang.data' . $this->lastEditTime . '.js';
		if(!file_exists($saveFileName)){
				file_put_contents($saveFileName, '');
			foreach($this->aLanguageWordsDataList as $lang => $aData){
				if($aData){
					$content = 'var __current__site_language__ = "' . Yii::$app->language . '"; var aLanguageWordsDataList = ' . json_encode($aData) . ';';
					file_put_contents(Yii::getAlias('@p.resource') . '/data/temp/lang.data.' . $lang . '.js', $content);
				}
			}
		}
	}
	
	
	private function _loadLanguage($languageCode){
		$fileName = Yii::getAlias('@common') . '/config/language/' . $languageCode . '.php';
		if(!file_exists($fileName)){
			return [];
		}
		$ctime = filemtime($fileName);
		if($ctime && $ctime > $this->lastEditTime){
			$this->lastEditTime = $ctime;
		}
		return require(Yii::getAlias('@common') . '/config/language/' . $languageCode . '.php');
	}
	
	/**
	 *	解释语言
	 *	Yii::$app->lang->words('test', ['title' => '今天好开心', 'content' => '今天好开心,不为什么']);
	 */
	public function words($keyName, $aReplacement = []){
		if(!isset(Yii::$app->language) || !isset($this->aLanguageWordsDataList[Yii::$app->language])){
			return '';
		}
		$aDataList = $this->aLanguageWordsDataList[Yii::$app->language];
		if(!$aDataList || !isset($aDataList[$keyName])){
			return '';
		}
		$returnString = $aDataList[$keyName];
		if($aReplacement){
			foreach($aReplacement as $key => $value){
				$returnString = str_replace('{' . $key . '}', $value, $returnString);
			}
		}
		return $returnString;
	}
}