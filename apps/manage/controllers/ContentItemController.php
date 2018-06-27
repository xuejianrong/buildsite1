<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\ContentItem;
use manage\model\form\ContentItemListForm;

class ContentItemController extends Controller{
	
	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$type = (int)Yii::$app->request->post('type');
		$status = (int)Yii::$app->request->post('status');
		$order = (int)Yii::$app->request->post('order');
		$isJinping = (int)Yii::$app->request->post('isJinping');
		$title = (string)Yii::$app->request->post('title');
		$source = (string)Yii::$app->request->post('source');
		$content = (string)Yii::$app->request->post('content');
		$aOtherInfo = (array)Yii::$app->request->post('aOtherInfo');
		
		$aTypeList = ContentItem::getTypeList();
		if(!$title){
			return new Response('请填写标题', -1);
		}
		if(!isset($aTypeList[$type])){
			return new Response('出错啦', 0);
		}
		
		if($id){
			$mContentItem = ContentItem::findOne($id);
			if(!$mContentItem){
				return new Response('记录不存在', 0);
			}
			$mContentItem->set('title', $title);
			$mContentItem->set('type', $type);
			$mContentItem->set('source', $source);
			$mContentItem->set('content', $content);
			$mContentItem->set('order', $order);
			$mContentItem->set('is_jinping', $isJinping);
			$mContentItem->set('status', $status);
			$mContentItem->set('other_info', $aOtherInfo);
			$mContentItem->save();
		}else{
			ContentItem::insert([
				'type' => $type,
				'managerId' => Yii::$app->manager->id,
				'title' => $title,
				'source' => $source,
				'content' => $content,
				'order' => $order,
				'is_jinping' => $isJinping,
				'status' => $status,
				'other_info' => $aOtherInfo,
				'create_time' => NOW_TIME,
			]);
		}
		
		return new Response('保存成功', 1);
	}

	public function actionDelete(){
		$id = (string)Yii::$app->request->post('id');
		
		$mContentItem = ContentItem::findOne($id);
		if(!$mContentItem){
			return new Response('找不到记录，删除失败', 0);
		}
		$mContentItem->set('status', ContentItem::STATUS_DELETE);
		$mContentItem->save();
		
		return new Response('删除成功', 1);
	}
}
