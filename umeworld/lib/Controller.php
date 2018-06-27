<?php
namespace umeworld\lib;

use Yii;

class Controller extends \yii\web\Controller{
	public function actions(){
		return [
			'error' => [
				'class' => 'umeworld\lib\ErrorAction',
			],
		];
	}
	
	public function init(){
		parent::init();
		$this->_initLanguage();
	}
	
	private function _initLanguage(){
		$_SERVER['REQUEST_URI'] = rtrim($_SERVER['REQUEST_URI'], '/');
		$language = Yii::$app->request->get('lang');
		if($language && isset(Yii::$app->lang->aLanguageWordsDataList[$language]) && Yii::$app->lang->aLanguageWordsDataList[$language]){
			Yii::$app->language = $language;
		}
	}
	
}