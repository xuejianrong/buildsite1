<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\ProductsCategory;

class ProductsCategoryController extends Controller{
	
	public function actionIndex(){
		$aList = ProductsCategory::findAll();
		
		return $this->render('index', [
			'aList' => $aList,
		]);
	}

	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$pid = (int)Yii::$app->request->post('pid');
		$name = (string)trim(strip_tags(Yii::$app->request->post('name')));
		$shortcut = (string)Yii::$app->request->post('shortcut');
		
		if(!$name){
			return new Response('请填分类名称', -1);
		}
		$mProductsCategory = ProductsCategory::findOne(['name' => $name, 'pid' => $pid]);
		if($mProductsCategory && $mProductsCategory->id != $id){
			return new Response('分类名称已存在', -1);
		}
		
		if($id){
			$mProductsCategory = ProductsCategory::findOne($id);
			if(!$mProductsCategory){
				return new Response('记录不存在', 0);
			}
			$mProductsCategory->set('pid', $pid);
			$mProductsCategory->set('name', $name);
			$mProductsCategory->set('shortcut', $shortcut);
			$mProductsCategory->save();
		}else{
			ProductsCategory::insert([
				'pid' => $pid,
				'name' => $name,
				'shortcut' => $shortcut,
			]);
		}
		
		return new Response('保存成功', 1);
	}
	
	public function actionDelete(){
		$id = (int)Yii::$app->request->post('id');
		
		$mProductsCategory = ProductsCategory::findOne($id);
		if(!$mProductsCategory){
			return new Response('找不到记录，删除失败', 0);
		}
		$mProductsCategory->delete();
		
		return new Response('删除成功', 1);
	}
		
}
