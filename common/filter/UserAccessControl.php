<?php
namespace common\filter;

use Yii;

/**
 * 用户访问控制过滤器
 */
class UserAccessControl extends \yii\filters\AccessControl{
	public $user = 'user';	//控制哪个APP用户组件的访问
	public $aNoCsrfActions = [];

	//用户角色标记
	const USERS = 'user';	//用户

	public function beforeAction($action){
		if(in_array($action->id, $this->aNoCsrfActions)){
			$action->controller->enableCsrfValidation = false;
		}
		
		return parent::beforeAction($action);
	}

	/**
	 * 权限验证不通过的回调
	 * @param type $oWebUser WEB用户对象,未登陆的时候任何人都可能是,登陆的时候就是用户
	 * @throws ForbiddenHttpException
	 * @return type mixed
	 */
    protected function denyAccess($oWebUser){
		$isGuest = $oWebUser->getIsGuest();
        if($isGuest){
            return $oWebUser->loginRequired();
		}else{
            throw new \yii\web\ForbiddenHttpException(\Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
}