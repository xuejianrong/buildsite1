<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;
use yii\data\Pagination;
use common\model\ContentItem;

class CustomerCaseController extends Controller{
	
	public function actionIndex(){
		$page = (int)Yii::$app->request->get('page');
		$pageSize = (int)Yii::$app->request->get('pageSize');
		
		if($page <= 0){
			$page = 1;
		}
		if($pageSize <= 0){
			$pageSize = 9;
		}
		
		$aCondition = [
			'type' => ContentItem::TYPE_CUSTOMER_CASE,
			'status' => ContentItem::STATUS_PUBLISHED,
		];
		$totalCount = ContentItem::getCount($aCondition);
		$aCustomerCaseList = ContentItem::getList($aCondition, [
			'page' => $page,
			'page_size' => $pageSize,
			'order_by' => ['order' => SORT_ASC, 'id' => SORT_DESC],
		]);
		
		$oPage = new Pagination(['totalCount' => $totalCount, 'pageSize' => $pageSize, 'aPaginationUrl' => ['home', 'customer-case/index']]);
		
		return $this->render('index', [
			'oPage' => $oPage,
			'aCustomerCaseList' => $aCustomerCaseList,
		]);
	}
	
	public function actionDetail(){
		$id = (int)Yii::$app->request->get('id');
		
		if(!$id){
			return new Response('出错了', 0);
		}
		
		$mContentItem = ContentItem::findOne($id);
		if(!$mContentItem){
			return new Response('客户案例不存在', 0);
		}
		
		return $this->render('detail', [
			'aCustomerCase' => $mContentItem->toArray(),
		]);
	}

}
