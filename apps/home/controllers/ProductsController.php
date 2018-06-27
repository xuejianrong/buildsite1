<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;
use yii\data\Pagination;
use common\model\Products;
use common\model\ProductsCategory;

class ProductsController extends Controller{
	
	public function actionIndex(){
		$page = (int)Yii::$app->request->get('page');
		$pageSize = (int)Yii::$app->request->get('pageSize');
		$categoryId = (int)Yii::$app->request->get('categoryId');
		
		if($page <= 0){
			$page = 1;
		}
		if($pageSize <= 0){
			$pageSize = 12;
		}
		
		$aCondition = [
			'status' => Products::STATUS_PUBLISHED,
		];
		if($categoryId){
			$aCondition['category_id'] = $categoryId;
		}
		$totalCount = Products::getCount($aCondition);
		$aProductsList = Products::getList($aCondition, [
			'page' => $page,
			'page_size' => $pageSize,
			'order_by' => ['id' => SORT_DESC],
		]);
		
		$oPage = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize, 'aPaginationUrl' => [Yii::$app->id, 'products/index', ['page' => $page, 'perpage' => $pageSize, 'categoryId' => $categoryId]]]);
		
		$aProductsCategoryList = ProductsCategory::findAll();
		return $this->render('index', [
			'oPage' => $oPage,
			'aProductsCategoryList' => $aProductsCategoryList,
			'aProductsList' => $aProductsList,
		]);
	}
	
	public function actionDetail(){
		$id = (int)Yii::$app->request->get('id');
		
		if(!$id){
			return new Response('出错了', 0);
		}
		
		$mProducts = Products::findOne($id);
		if(!$mProducts){
			return new Response('产品不存在', 0);
		}
		if(!$mProducts->status){
			return new Response('产品未发布', 0);
		}
		
		$mProductsCategory = ProductsCategory::findOne($mProducts->category_id);
		
		$aRelateProductsList = Products::getList([
			'status' => Products::STATUS_PUBLISHED,
			'category_id' => $mProducts->category_id,
			'not_id' => $mProducts->id,
		], [
			'page' => 1,
			'page_size' => 4,
			'order_by' => ['id' => SORT_DESC],
		]);
		
		return $this->render('detail', [
			'aProducts' => $mProducts->toArray(),
			'productsCategory' => $mProductsCategory ? $mProductsCategory->name : '',
			'aRelateProductsList' => $aRelateProductsList,
		]);
	}

}
