<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\SiteMessage;
use manage\model\form\SiteMessageListForm;

class SiteMessageController extends Controller{
	
	public function actionIndex(){
		$oSiteMessageListForm = new SiteMessageListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oSiteMessageListForm->load($aParams, '') || !$oSiteMessageListForm->validate())){
			return new Response(current($oSiteMessageListForm->getErrors())[0]);
		}
		
		$aList = $oSiteMessageListForm->getList();
		$oPage = $oSiteMessageListForm->getPageObject();
		
		return $this->render('index', [
			'oPage' => $oPage,
			'aList' => $aList,
		]);
	}

	public function actionDelete(){
		$id = (string)Yii::$app->request->post('id');
		
		$mSiteMessage = SiteMessage::findOne($id);
		if(!$mSiteMessage){
			return new Response('找不到记录，删除失败', 0);
		}
		
		$mSiteMessage->delete();
		
		return new Response('删除成功', 1);
	}

}
