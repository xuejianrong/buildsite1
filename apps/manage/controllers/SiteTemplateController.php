<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\Setting;

class SiteTemplateController extends Controller{
	
	public function actionIndex(){
		return $this->render('index', [
			'aSiteTemplate' => Setting::getSetting(Setting::SITE_TEMPLATE_CACHE_KEY, []),
		]);
	}

	public function actionSave(){
		$id = (string)Yii::$app->request->post('id');
		$name = (string)Yii::$app->request->post('name');
		$link = (string)Yii::$app->request->post('link');
		$shortcut = (string)Yii::$app->request->post('shortcut');
		$price = (string)Yii::$app->request->post('price');
		$order = (int)Yii::$app->request->post('order');
		
		if(!$name){
			return new Response('请填写模板名称', -1);
		}
		if(!$link){
			return new Response('请填写模板链接', -1);
		}
		if(!$shortcut || !file_exists(Yii::getAlias('@p.resource') . '/' . $shortcut)){
			return new Response('请上传模板截图', -1);
		}
		
		$aSaveData = [
			'id' => $id ? $id : uniqid(),
			'name' => $name,
			'link' => $link,
			'shortcut' => $shortcut,
			'price' => $price,
			'order' => $order,
		];
		$aSiteTemplate = Setting::getSetting(Setting::SITE_TEMPLATE_CACHE_KEY, []);
		$isFind = false;
		foreach($aSiteTemplate as $k => $v){
			if($v['id'] == $id){
				$aSiteTemplate[$k] = $aSaveData;
				$isFind = true;
				break;
			}
		}
		if(!$isFind){
			array_push($aSiteTemplate, $aSaveData);
		}
		ArrayHelper::multisort($aSiteTemplate, 'order', SORT_ASC);
		Setting::setSetting(Setting::SITE_TEMPLATE_CACHE_KEY, $aSiteTemplate);
		
		return new Response('保存成功', 1);
	}

	public function actionDelete(){
		$id = (string)Yii::$app->request->post('id');
		
		$aSiteTemplate = Setting::getSetting(Setting::SITE_TEMPLATE_CACHE_KEY, []);
		$isFind = false;
		$aTempData = [];
		foreach($aSiteTemplate as $k => $v){
			if($v['id'] == $id){
				$isFind = true;
			}else{
				array_push($aTempData, $v);
			}
		}
		if(!$isFind){
			return new Response('找不到记录，删除失败', -1);
		}else{
			$aSiteTemplate = $aTempData;
			Setting::setSetting(Setting::SITE_TEMPLATE_CACHE_KEY, $aSiteTemplate);
			return new Response('删除成功', 1);
		}
	}
}
