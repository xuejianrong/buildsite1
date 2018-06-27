<?php
namespace home\widgets;

use Yii;
use yii\base\Widget;
use common\model\ProductsCategory;
use common\model\NewsCategory;

class FooterNav extends Widget{
	public function run(){
		$mUser = Yii::$app->user->getIdentity();
		$aUser = [];
		if($mUser){
			$mUser = $mUser->toArray();
		}
		
		$aProductsCategoryList = ProductsCategory::findAll();
		$aNewsCategoryList = NewsCategory::findAll();
		return $this->render('footer_navi', [
			'aUser' => $aUser,
			'aProductsCategoryList' => $aProductsCategoryList,
			'aNewsCategoryList' => $aNewsCategoryList,
		]);
	}

}