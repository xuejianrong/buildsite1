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

class CustomerCaseController extends Controller{
	
	public function actionIndex(){
		$oContentItemListForm = new ContentItemListForm();
		$aParams = Yii::$app->request->get();
		if($aParams && (!$oContentItemListForm->load($aParams, '') || !$oContentItemListForm->validate())){
			return new Response(current($oContentItemListForm->getErrors())[0]);
		}
		$oContentItemListForm->type = ContentItem::TYPE_CUSTOMER_CASE;
		$aList = $oContentItemListForm->getList();
		$oPage = $oContentItemListForm->getPageObject();
		
		return $this->render('index', [
			'oPage' => $oPage,
			'aList' => $aList,
		]);
	}

}
