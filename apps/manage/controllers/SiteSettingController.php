<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\Setting;

class SiteSettingController extends Controller{
	private $_aDefaultSiteSetting = [
		'siteTitle' => '',
		'siteLogo' => '',
		'siteSeoTitle' => '',
		'siteSeoKeywords' => '',
		'siteSeoDescription' => '',
		'siteCopyright' => '',
	];
	
	public function actionIndex(){
		return $this->render('index', [
			'aSiteSetting' => Setting::getSetting(Setting::SITE_SETTING_CACHE_KEY, $this->_aDefaultSiteSetting),
		]);
	}

	public function actionSave(){
		$siteTitle = (string)Yii::$app->request->post('siteTitle');
		$siteLogo = (string)Yii::$app->request->post('siteLogo');
		$siteSeoTitle = (string)Yii::$app->request->post('siteSeoTitle');
		$siteSeoKeywords = (string)Yii::$app->request->post('siteSeoKeywords');
		$siteSeoDescription = (string)Yii::$app->request->post('siteSeoDescription');
		$siteCopyright = (string)Yii::$app->request->post('siteCopyright');
		
		if(!$siteTitle){
			return new Response('请填写标题', -1);
		}
		if(!$siteLogo || !file_exists(Yii::getAlias('@p.resource') . '/' . $siteLogo)){
			return new Response('请上传Logo', -1);
		}
		
		$aSiteSetting = Setting::getSetting(Setting::SITE_SETTING_CACHE_KEY, $this->_aDefaultSiteSetting);
		$aSiteSetting['siteTitle'] = $siteTitle;
		$aSiteSetting['siteLogo'] = $siteLogo;
		$aSiteSetting['siteSeoTitle'] = $siteSeoTitle;
		$aSiteSetting['siteSeoKeywords'] = $siteSeoKeywords;
		$aSiteSetting['siteSeoDescription'] = $siteSeoDescription;
		$aSiteSetting['siteCopyright'] = $siteCopyright;
		Setting::setSetting(Setting::SITE_SETTING_CACHE_KEY, $aSiteSetting);
		
		return new Response('保存成功', 1);
	}

	public function actionFriendsLink(){
		return $this->render('friends_link', [
			'aFriendsLink' => Setting::getSetting(Setting::FRIENDS_LINK_CACHE_KEY, []),
		]);
	}

	public function actionSaveFriendsLink(){
		$id = (string)Yii::$app->request->post('id');
		$name = (string)Yii::$app->request->post('name');
		$link = (string)Yii::$app->request->post('link');
		$order = (int)Yii::$app->request->post('order');
		
		if(!$name){
			return new Response('请填写标题', -1);
		}
		if(!$link){
			return new Response('请填写链接', -1);
		}
		
		$aSaveData = [
			'id' => $id ? $id : uniqid(),
			'name' => $name,
			'link' => $link,
			'order' => $order,
		];
		$aFriendsLink = Setting::getSetting(Setting::FRIENDS_LINK_CACHE_KEY, []);
		$isFind = false;
		foreach($aFriendsLink as $k => $v){
			if($v['id'] == $id){
				$aFriendsLink[$k] = $aSaveData;
				$isFind = true;
				break;
			}
		}
		if(!$isFind){
			array_push($aFriendsLink, $aSaveData);
		}
		ArrayHelper::multisort($aFriendsLink, 'order', SORT_ASC);
		Setting::setSetting(Setting::FRIENDS_LINK_CACHE_KEY, $aFriendsLink);
		
		return new Response('保存成功', 1);
	}

	public function actionDeleteFriendsLink(){
		$id = (string)Yii::$app->request->post('id');
		
		$aFriendsLink = Setting::getSetting(Setting::FRIENDS_LINK_CACHE_KEY, []);
		$isFind = false;
		$aTempData = [];
		foreach($aFriendsLink as $k => $v){
			if($v['id'] == $id){
				$isFind = true;
			}else{
				array_push($aTempData, $v);
			}
		}
		if(!$isFind){
			return new Response('找不到记录，删除失败', -1);
		}else{
			$aFriendsLink = $aTempData;
			Setting::setSetting(Setting::FRIENDS_LINK_CACHE_KEY, $aFriendsLink);
			return new Response('删除成功', 1);
		}
	}

	public function actionCooperativePartners(){
		return $this->render('cooperative_partners', [
			'aCooperativePartners' => Setting::getSetting(Setting::COOPERATIVE_PARTNERS_CACHE_KEY, []),
		]);
	}

	public function actionSaveCooperativePartners(){
		$id = (string)Yii::$app->request->post('id');
		$name = (string)Yii::$app->request->post('name');
		$link = (string)Yii::$app->request->post('link');
		$linkLogo = (string)Yii::$app->request->post('linkLogo');
		$order = (int)Yii::$app->request->post('order');
		
		if(!$name){
			return new Response('请填写合作伙伴', -1);
		}
		if(!$link){
			return new Response('请填写链接', -1);
		}
		if(!$linkLogo || !file_exists(Yii::getAlias('@p.resource') . '/' . $linkLogo)){
			return new Response('请上传链接图片', -1);
		}
		
		$aSaveData = [
			'id' => $id ? $id : uniqid(),
			'name' => $name,
			'link' => $link,
			'linkLogo' => $linkLogo,
			'order' => $order,
		];
		$aCooperativePartners = Setting::getSetting(Setting::COOPERATIVE_PARTNERS_CACHE_KEY, []);
		$isFind = false;
		foreach($aCooperativePartners as $k => $v){
			if($v['id'] == $id){
				$aCooperativePartners[$k] = $aSaveData;
				$isFind = true;
				break;
			}
		}
		if(!$isFind){
			array_push($aCooperativePartners, $aSaveData);
		}
		ArrayHelper::multisort($aCooperativePartners, 'order', SORT_ASC);
		Setting::setSetting(Setting::COOPERATIVE_PARTNERS_CACHE_KEY, $aCooperativePartners);
		
		return new Response('保存成功', 1);
	}

	public function actionDeleteCooperativePartners(){
		$id = (string)Yii::$app->request->post('id');
		
		$aCooperativePartners = Setting::getSetting(Setting::COOPERATIVE_PARTNERS_CACHE_KEY, []);
		$isFind = false;
		$aTempData = [];
		foreach($aCooperativePartners as $k => $v){
			if($v['id'] == $id){
				$isFind = true;
			}else{
				array_push($aTempData, $v);
			}
		}
		if(!$isFind){
			return new Response('找不到记录，删除失败', -1);
		}else{
			$aCooperativePartners = $aTempData;
			Setting::setSetting(Setting::COOPERATIVE_PARTNERS_CACHE_KEY, $aCooperativePartners);
			return new Response('删除成功', 1);
		}
	}

}
