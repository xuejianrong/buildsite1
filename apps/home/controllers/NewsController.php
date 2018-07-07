<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Pagination;
use common\model\News;
use common\model\NewsCategory;

class NewsController extends Controller{
	
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
			'status' => News::STATUS_PUBLISHED,
		];
		if($categoryId){
			$aCondition['category_id'] = $categoryId;
		}
		$totalCount = News::getCount($aCondition);
		$aNewsList = News::getList($aCondition, [
			'page' => $page,
			'page_size' => $pageSize,
			'order_by' => ['id' => SORT_DESC],
		]);
		
		$oPage = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize, 'aPaginationUrl' => [Yii::$app->id, 'news/index', ['page' => $page, 'perpage' => $pageSize, 'categoryId' => $categoryId]]]);
		
		$aNewsCategoryList = NewsCategory::findAll();
		return $this->render('index', [
			'categoryId' => $categoryId,
			'oPage' => $oPage,
			'aNewsCategoryList' => $aNewsCategoryList,
			'aNewsList' => $aNewsList,
		]);
	}
	
	public function actionDetail(){
		$id = (int)Yii::$app->request->get('id');
		
		if(!$id){
			return new Response('出错了', 0);
		}
		
		$mNews = News::findOne($id);
		if(!$mNews){
			return new Response('新闻不存在', 0);
		}
		if(!$mNews->status){
			return new Response('新闻未发布', 0);
		}
		
		$mNews->set('click_count', ['add', 1]);
		$mNews->save();
		
		$aNewsCategoryList = NewsCategory::findAll();
		
		$mNewsPre = News::findOne([
			'and',
			['status' => News::STATUS_PUBLISHED],
			['<', 'id', $mNews->id],
		]);
		$mNewsNext = News::findOne([
			'and',
			['status' => News::STATUS_PUBLISHED],
			['>', 'id', $mNews->id],
		]);
		
		return $this->render('detail', [
			'categoryId' => $mNews->category_id,
			'aNews' => $mNews->toArray(),
			'aNewsCategoryList' => $aNewsCategoryList,
			'aNewsPre' => $mNewsPre ? $mNewsPre->toArray() : [],
			'aNewsNext' => $mNewsNext ? $mNewsNext->toArray() : [],
		]);
	}

}
