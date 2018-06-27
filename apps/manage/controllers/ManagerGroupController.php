<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\ManagerGroup;

class ManagerGroupController extends Controller{
	
	public function actionIndex(){
		$aList = ManagerGroup::findAll();
		
		return $this->render('index', [
			'aList' => $aList,
		]);
	}

	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$name = (string)trim(strip_tags(Yii::$app->request->post('name')));
		
		if(!$name){
			return new Response('请填角色名称', -1);
		}
		$mManagerGroup = ManagerGroup::findOne(['name' => $name]);
		if($mManagerGroup && $mManagerGroup->id != $id){
			return new Response('角色名称已存在', -1);
		}
		
		if($id){
			$mManagerGroup = ManagerGroup::findOne($id);
			if(!$mManagerGroup){
				return new Response('记录不存在', 0);
			}
			$mManagerGroup->set('name', $name);
			$mManagerGroup->save();
		}else{
			ManagerGroup::insert([
				'name' => $name,
				'actions' => [],
			]);
		}
		
		return new Response('保存成功', 1);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
		$mManagerGroup = ManagerGroup::findOne($id);
		if(!$mManagerGroup){
			return new Response('找不到记录，删除失败', 0);
		}
		$mManagerGroup->delete();
		
		return new Response('删除成功', 1);
	}
	
	public function actionUpdate(){
		$id = (int)Yii::$app->request->post('id');
		$uKey = (string)Yii::$app->request->post('uKey');
		$uValue = Yii::$app->request->post('uValue');
		
		$mManagerGroup = ManagerGroup::findOne($id);
		if(!$mManagerGroup){
			return new Response('找不到记录，删除失败', 0);
		}
		if(!$uKey || !isset($mManagerGroup->$uKey)){
			return new Response('出错啦', 0);
		}
		$mManagerGroup->set($uKey, $uValue);
		$mManagerGroup->save();
		
		return new Response('操作成功', 1);
	}
	
	public function actionEditActions(){
		$aList = ManagerGroup::findAll();
		$aManagerGroupActionsList = Yii::$app->params['manager_group_actions_list'];
		
		return $this->render('edit_actions', [
			'aList' => $aList,
			'aManagerGroupActionsList' => $aManagerGroupActionsList,
		]);
	}
	
	public function actionSaveActions(){
		$id = (int)Yii::$app->request->post('id');
		$actions = (array)Yii::$app->request->post('actions');
		
		$mManagerGroup = ManagerGroup::findOne($id);
		if(!$mManagerGroup){
			return new Response('找不到记录，删除失败', 0);
		}
		$mManagerGroup->set('actions', $actions);
		$mManagerGroup->save();
		
		return new Response('操作成功', 1);
	}
	
}
