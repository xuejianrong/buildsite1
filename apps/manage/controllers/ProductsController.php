<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\Products;
use common\model\ProductsCategory;
use manage\model\form\ProductsListForm;

class ProductsController extends Controller{
	public function actionIndex(){
		$oListForm = new ProductsListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oListForm->load($aParams, '') || !$oListForm->validate())){
			return new Response(current($oListForm->getErrors())[0]);
		}
		
		$aList = $oListForm->getList();
		$oPage = $oListForm->getPageObject();
		
		return $this->render('index', [
			'oPage' => $oPage,
			'aList' => $aList,
			'aCategoryList' => ProductsCategory::findAll(),
		]);
	}
	
	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$name = (string)Yii::$app->request->post('name');
		$categoryId = (int)Yii::$app->request->post('categoryId');
		$shortcut = (string)Yii::$app->request->post('shortcut');
		$productModel = (string)Yii::$app->request->post('productModel');
		$producePlace = (string)Yii::$app->request->post('producePlace');
		$brand = (string)Yii::$app->request->post('brand');
		$price = (string)Yii::$app->request->post('price');
		$deliveryAddress = (string)Yii::$app->request->post('deliveryAddress');
		$hasSample = (int)Yii::$app->request->post('hasSample');
		$status = (int)Yii::$app->request->post('status');
		$description = (string)Yii::$app->request->post('description');
		$aOtherInfo = (array)Yii::$app->request->post('aOtherInfo');
		
		if(!$name){
			return new Response('请填写产品名称', -1);
		}
		$mProductsCategory = ProductsCategory::findOne($categoryId);
		if(!$mProductsCategory){
			return new Response('请选择分类', -1);
		}
		if(!$shortcut || !file_exists(Yii::getAlias('@p.resource') . '/' . $shortcut)){
			return new Response('请上传图片', -1);
		}
		if(!$description){
			return new Response('请填写产品描述', -1);
		}
		
		if($id){
			$mProducts = Products::findOne($id);
			if(!$mProducts){
				return new Response('记录不存在', 0);
			}
			$mProducts->set('name', $name);
			$mProducts->set('category_id', $categoryId);
			$mProducts->set('shortcut', $shortcut);
			$mProducts->set('product_model', $productModel);
			$mProducts->set('produce_place', $producePlace);
			$mProducts->set('brand', $brand);
			$mProducts->set('price', $price);
			$mProducts->set('delivery_address', $deliveryAddress);
			$mProducts->set('has_sample', $hasSample);
			$mProducts->set('description', $description);
			$mProducts->set('status', $status);
			$mProducts->set('other_info', $aOtherInfo);
			$mProducts->save();
		}else{
			Products::insert([
				'name' => $name,
				'category_id' => $categoryId,
				'shortcut' => $shortcut,
				'product_model' => $productModel,
				'produce_place' => $producePlace,
				'brand' => $brand,
				'price' => $price,
				'delivery_address' => $deliveryAddress,
				'has_sample' => $hasSample,
				'description' => $description,
				'status' => $status,
				'other_info' => $aOtherInfo,
				'create_time' => NOW_TIME,
			]);
		}
		
		return new Response('保存成功', 1);
	}

	public function actionDelete(){
		$id = (string)Yii::$app->request->post('id');
		
		$mProducts = Products::findOne($id);
		if(!$mProducts){
			return new Response('找不到记录，删除失败', 0);
		}
		
		$mProducts->delete();
		
		return new Response('删除成功', 1);
	}
}
