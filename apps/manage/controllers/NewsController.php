<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\News;
use common\model\NewsCategory;
use manage\model\form\NewsListForm;

class NewsController extends Controller{
	public function actionIndex(){
		$oNewsListForm = new NewsListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oNewsListForm->load($aParams, '') || !$oNewsListForm->validate())){
			return new Response(current($oNewsListForm->getErrors())[0]);
		}
		
		$aList = $oNewsListForm->getList();
		$oPage = $oNewsListForm->getPageObject();
		
		return $this->render('index', [
			'oPage' => $oPage,
			'aList' => $aList,
			'aNewsCategoryList' => NewsCategory::findAll(),
		]);
	}
	
	public function actionSave(){
		$id = (int)Yii::$app->request->post('id');
		$title = (string)Yii::$app->request->post('title');
		$categoryId = (int)Yii::$app->request->post('categoryId');
		$status = (int)Yii::$app->request->post('status');
		$shortcut = (string)Yii::$app->request->post('shortcut');
		$content = (string)Yii::$app->request->post('content');
		
		if(!$title){
			return new Response('请填写标题', -1);
		}
		$mNewsCategory = NewsCategory::findOne($categoryId);
		if(!$mNewsCategory){
			return new Response('请选择分类', -1);
		}
		if(!$shortcut || !file_exists(Yii::getAlias('@p.resource') . '/' . $shortcut)){
			return new Response('请上传图片', -1);
		}
		if(!$content){
			return new Response('请填写内容', -1);
		}
		
		if($id){
			$mNews = News::findOne($id);
			if(!$mNews){
				return new Response('记录不存在', 0);
			}
			$mNews->set('title', $title);
			$mNews->set('category_id', $categoryId);
			$mNews->set('shortcut', $shortcut);
			$mNews->set('content', $content);
			$mNews->set('status', $status);
			if(!$mNews->publish_time && $status){
				$mNews->set('publish_time', NOW_TIME);
			}
			$mNews->save();
		}else{
			News::insert([
				'title' => $title,
				'category_id' => $categoryId,
				'shortcut' => $shortcut,
				'content' => $content,
				'status' => $status,
				'click_count' => 0,
				'publish_time' => $status ? NOW_TIME : 0,
				'create_time' => NOW_TIME,
			]);
		}
		
		return new Response('保存成功', 1);
	}

	public function actionDelete(){
		$id = (string)Yii::$app->request->post('id');
		
		$mNews = News::findOne($id);
		if(!$mNews){
			return new Response('找不到记录，删除失败', 0);
		}
		
		$mNews->delete();
		
		return new Response('删除成功', 1);
	}
}
