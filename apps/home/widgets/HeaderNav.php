<?php
namespace home\widgets;

use Yii;
use yii\base\Widget;
use common\model\Setting;

class HeaderNav extends Widget{
	public function run(){
		$mUser = Yii::$app->user->getIdentity();
		$aUser = [];
		if($mUser){
			$mUser = $mUser->toArray();
		}
		
		return $this->render('header_navi', [
			'aUser' => $aUser,
		]);
	}

}