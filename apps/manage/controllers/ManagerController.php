<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\Manager;
use common\model\ManagerGroup;
use manage\model\form\ManagerListForm;

class ManagerController extends Controller{
	
	public function actionIndex(){
		$oManagerListForm = new ManagerListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oManagerListForm->load($aParams, '') || !$oManagerListForm->validate())){
			return new Response(current($oManagerListForm->getErrors())[0]);
		}
		
		$aList = $oManagerListForm->getList();
		$oPage = $oManagerListForm->getPageObject();
		
		$aManagerGroupList = ManagerGroup::findAll();
		return $this->render('index', [
			'oPage' => $oPage,
			'aList' => $aList,
			'aManagerGroupList' => $aManagerGroupList,
		]);
	}

	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$nickName = (string)trim(strip_tags(Yii::$app->request->post('nickName')));
		$account = (string)trim(strip_tags(Yii::$app->request->post('account')));
		$groupId = (int)Yii::$app->request->post('groupId');
		$password = (string)Yii::$app->request->post('password');
		$enpassword = (string)Yii::$app->request->post('enpassword');
		$isForbidden = (int)Yii::$app->request->post('isForbidden');
		
		if(!$nickName){
			return new Response('请填写姓名', -1);
		}
		$mManager = Manager::findOne(['nick_name' => $nickName]);
		if($mManager && $mManager->id != $id){
			return new Response('姓名已存在', -1);
		}
		if(!$account){
			return new Response('请填写账号', -1);
		}
		$mManager = Manager::findOne(['account' => $account]);
		if($mManager && $mManager->id != $id){
			return new Response('账号已存在', -1);
		}
		if(!$groupId || !ManagerGroup::findOne($groupId)){
			return new Response('请选择角色', -1);
		}
		if(!$id || ($password && $enpassword)){
			if(!$password){
				return new Response('请填写密码', -1);
			}
			if($password != $enpassword){
				return new Response('输入两次密码不一致', -1);
			}
		}
		
		if($id){
			$mManager = Manager::findOne($id);
			if(!$mManager){
				return new Response('记录不存在', 0);
			}
			$mManager->set('nick_name', $nickName);
			$mManager->set('account', $account);
			$mManager->set('group_id', $groupId);
			$mManager->set('is_forbidden', $isForbidden);
			if($password && $enpassword){
				$mManager->set('password', Manager::encryptPassword($password));
			}
			$mManager->save();
		}else{
			Manager::insert([
				'nick_name' => $nickName,
				'account' => $account,
				'group_id' => $groupId,
				'is_forbidden' => $isForbidden,
				'password' => Manager::encryptPassword($password),
				'create_time' => NOW_TIME,
			]);
		}
		
		return new Response('保存成功', 1);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
		$mManager = Manager::findOne($id);
		if(!$mManager){
			return new Response('找不到记录，删除失败', 0);
		}
		if($mManager->id === 1){
			return new Response('超级管理员禁止删除', -1);
		}
		$mManager->delete();
		
		return new Response('删除成功', 1);
	}
	
	public function actionUpdate(){
		$id = (int)Yii::$app->request->post('id');
		$uKey = (string)Yii::$app->request->post('uKey');
		$uValue = (string)Yii::$app->request->post('uValue');
		
		$mManager = Manager::findOne($id);
		if(!$mManager){
			return new Response('找不到记录，删除失败', 0);
		}
		if(!isset($mManager->$uKey)){
			return new Response('出错啦', 0);
		}
		if($uKey == 'password'){
			$uValue = Manager::encryptPassword($uValue);
		}
		$mManager->set($uKey, $uValue);
		$mManager->save();
		
		return new Response('操作成功', 1);
	}
	
}
