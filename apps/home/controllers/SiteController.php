<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;
use common\model\ProductsCategory;
use common\model\Products;
use common\model\News;

class SiteController extends Controller{
	
	public function actionIndex(){
		$aProductsCategoryList = ProductsCategory::findAll();
		$aProductsList = Products::getList([
			'status' => Products::STATUS_PUBLISHED,
		], [
			'page' => 1,
			'page_size' => 10,
			'order_by' => ['id' => SORT_DESC],
		]);
		$aNewsList = News::getList([
			'status' => News::STATUS_PUBLISHED,
		], [
			'page' => 1,
			'page_size' => 3,
			'order_by' => ['id' => SORT_DESC],
		]);
		
		return $this->render('index', [
			'aProductsCategoryList' => $aProductsCategoryList,
			'aProductsList' => $aProductsList,
			'aNewsList' => $aNewsList,
		]);
	}

	public function actionAboutus(){
		return $this->render('aboutus');
	}

	public function actionContactus(){
		return $this->render('contactus');
	}

	public function actionZhaopin(){
		return $this->render('zhaopin');
	}

	public function actionTalentConcept(){
		return $this->render('talent_concept');
	}

}
