<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\Setting;

class ContactusController extends Controller{
	private $_aDefaultSetting = [
		'companyName' => '',
		'phone' => '',
		'hotsPhone' => '',
		'servicePhone' => '',
		'mobile' => '',
		'email' => '',
		'qq' => '',
		'address' => '',
		'intro' => '',
		'mapApi' => '',
		'lng' => '',
		'lat' => '',
	];
	
	public function actionIndex(){
		return $this->render('index', [
			'aSetting' => Setting::getSetting(Setting::CONTACTUS_CACHE_KEY, $this->_aDefaultSetting),
		]);
	}

	public function actionSave(){
		$companyName = (string)Yii::$app->request->post('companyName');
		$phone = (string)Yii::$app->request->post('phone');
		$hotsPhone = (string)Yii::$app->request->post('hotsPhone');
		$servicePhone = (string)Yii::$app->request->post('servicePhone');
		$mobile = (string)Yii::$app->request->post('mobile');
		$email = (string)Yii::$app->request->post('email');
		$qq = (string)Yii::$app->request->post('qq');
		$address = (string)Yii::$app->request->post('address');
		$mapApi = (string)Yii::$app->request->post('mapApi');
		$lng = (string)Yii::$app->request->post('lng');
		$lat = (string)Yii::$app->request->post('lat');
		$intro = (string)Yii::$app->request->post('intro');
		
		if(!$companyName){
			return new Response('请填写公司名称', -1);
		}
		if(!$hotsPhone){
			return new Response('请填写服务热线', -1);
		}
		if(!$address){
			return new Response('请填写地址', -1);
		}
		
		$aSetting = Setting::getSetting(Setting::CONTACTUS_CACHE_KEY, $this->_aDefaultSetting);
		$aSetting['companyName'] = $companyName;
		$aSetting['phone'] = $phone;
		$aSetting['hotsPhone'] = $hotsPhone;
		$aSetting['servicePhone'] = $servicePhone;
		$aSetting['mobile'] = $mobile;
		$aSetting['email'] = $email;
		$aSetting['qq'] = $qq;
		$aSetting['address'] = $address;
		$aSetting['mapApi'] = $mapApi;
		$aSetting['lng'] = $lng;
		$aSetting['lat'] = $lat;
		$aSetting['intro'] = $intro;
		Setting::setSetting(Setting::CONTACTUS_CACHE_KEY, $aSetting);
		
		return new Response('保存成功', 1);
	}


}
