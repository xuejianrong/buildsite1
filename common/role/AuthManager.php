<?php
namespace common\role;

use umeworld\lib\Url;


/**
 * 权限验证管理者
 */
class AuthManager extends \yii\base\Component{

	public function init() {
		parent::init();
	}

	/**
	 * 检查一个用户是否有指定的权限
	 * @param int $userId 后台用户ID
	 * @param string $permissionName 权限标识名称
	 * @return boolean
	 * @throws \yii\base\InvalidParamException
	 */
	public function	checkAccess($userId, $permissionName){
		if(!$userId){
			return false;
		}
		
		$mUser = null;
		if($permissionName == \common\filter\UserAccessControl::USERS){
			$mUser = \common\model\User::findOne($userId);
		}
		if($permissionName == \common\filter\ManagerAccessControl::USERS){
			$mUser = \common\model\Manager::findOne($userId);
		}

		if(!$mUser){
			throw new \yii\base\InvalidParamException('无效的用户ID');
		}

		return $mUser->allow($permissionName);
	}

}