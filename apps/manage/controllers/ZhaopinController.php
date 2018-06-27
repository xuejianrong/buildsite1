<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\Setting;

class ZhaopinController extends Controller{
	
	public function actionIndex(){
		return $this->render('index', [
			'aZhaopinList' => Setting::getSetting(Setting::ZHAOPIN_CACHE_KEY, []),
		]);
	}

	public function actionSave(){
		$id = (string)Yii::$app->request->post('id');
		$position = (string)Yii::$app->request->post('position');
		$count = (string)Yii::$app->request->post('count');
		$workplace = (string)Yii::$app->request->post('workplace');
		$qualifications = (string)Yii::$app->request->post('qualifications');
		$expirence = (string)Yii::$app->request->post('expirence');
		$publishTime = (string)Yii::$app->request->post('publishTime');
		$description = (string)Yii::$app->request->post('description');
		
		if(!$position){
			return new Response('请填写招聘职位', -1);
		}
		if(!$description){
			return new Response('请填写职位描述', -1);
		}
		
		$aSaveData = [
			'id' => $id ? $id : uniqid(),
			'position' => $position,
			'count' => $count,
			'workplace' => $workplace,
			'qualifications' => $qualifications,
			'expirence' => $expirence,
			'publishTime' => $publishTime,
			'description' => $description,
		];
		$aZhaopinList = Setting::getSetting(Setting::ZHAOPIN_CACHE_KEY, []);
		$isFind = false;
		foreach($aZhaopinList as $k => $v){
			if($v['id'] == $id){
				$aZhaopinList[$k] = $aSaveData;
				$isFind = true;
				break;
			}
		}
		if(!$isFind){
			array_push($aZhaopinList, $aSaveData);
		}
		
		Setting::setSetting(Setting::ZHAOPIN_CACHE_KEY, $aZhaopinList);
		
		return new Response('保存成功', 1);
	}

	public function actionDelete(){
		$id = (string)Yii::$app->request->post('id');
		
		$aZhaopinList = Setting::getSetting(Setting::ZHAOPIN_CACHE_KEY, []);
		$isFind = false;
		$aTempData = [];
		foreach($aZhaopinList as $k => $v){
			if($v['id'] == $id){
				$isFind = true;
			}else{
				array_push($aTempData, $v);
			}
		}
		if(!$isFind){
			return new Response('找不到记录，删除失败', -1);
		}else{
			$aZhaopinList = $aTempData;
			Setting::setSetting(Setting::ZHAOPIN_CACHE_KEY, $aZhaopinList);
			return new Response('删除成功', 1);
		}
	}

	public function actionTalentConcept(){
		return $this->render('talent_concept', [
			'talentConcept' => Setting::getSetting(Setting::ZHAOPIN_TALENT_CONCEPT_CACHE_KEY, ''),
		]);
	}

	public function actionSaveTalentConcept(){
		$talentConcept = (string)Yii::$app->request->post('talentConcept');
		
		Setting::setSetting(Setting::ZHAOPIN_TALENT_CONCEPT_CACHE_KEY, $talentConcept);
		
		return new Response('保存成功', 1);
	}
}
