<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\Setting;

class AboutusController extends Controller{
	private $_aDefaultSetting = [
		'companyProfile' => '',
		'companyHistory' => '',
		'aCompanyCertificate' => [],
		'companyCulture' => '',
	];
	
	public function actionIndex(){
		return $this->render('index', [
			'aSetting' => Setting::getSetting(Setting::ABOUTUS_CACHE_KEY, $this->_aDefaultSetting),
		]);
	}

	public function actionSave(){
		$companyProfile = (string)Yii::$app->request->post('companyProfile');
		$companyHistory = (string)Yii::$app->request->post('companyHistory');
		$aCompanyCertificate = (array)Yii::$app->request->post('aCompanyCertificate');
		$companyCulture = (string)Yii::$app->request->post('companyCulture');
		
		
		$aSetting = Setting::getSetting(Setting::ABOUTUS_CACHE_KEY, $this->_aDefaultSetting);
		$aSetting['companyProfile'] = $companyProfile;
		$aSetting['companyHistory'] = $companyHistory;
		$aSetting['aCompanyCertificate'] = $aCompanyCertificate;
		$aSetting['companyCulture'] = $companyCulture;
		Setting::setSetting(Setting::ABOUTUS_CACHE_KEY, $aSetting);
		
		return new Response('保存成功', 1);
	}


}
