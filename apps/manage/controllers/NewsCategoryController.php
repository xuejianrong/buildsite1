<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\NewsCategory;

class NewsCategoryController extends Controller{
	
	public function actionIndex(){
		$aList = NewsCategory::findAll();
		
		return $this->render('index', [
			'aList' => $aList,
		]);
	}

	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$pid = (int)Yii::$app->request->post('pid');
		$name = (string)trim(strip_tags(Yii::$app->request->post('name')));
		
		if(!$name){
			return new Response('请填分类名称', -1);
		}
		$mNewsCategory = NewsCategory::findOne(['name' => $name, 'pid' => $pid]);
		if($mNewsCategory && $mNewsCategory->id != $id){
			return new Response('分类名称已存在', -1);
		}
		
		if($id){
			$mNewsCategory = NewsCategory::findOne($id);
			if(!$mNewsCategory){
				return new Response('记录不存在', 0);
			}
			$mNewsCategory->set('pid', $pid);
			$mNewsCategory->set('name', $name);
			$mNewsCategory->save();
		}else{
			NewsCategory::insert([
				'pid' => $pid,
				'name' => $name,
			]);
		}
		
		return new Response('保存成功', 1);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
		$mNewsCategory = NewsCategory::findOne($id);
		if(!$mNewsCategory){
			return new Response('找不到记录，删除失败', 0);
		}
		$mNewsCategory->delete();
		
		return new Response('删除成功', 1);
	}
		
}
