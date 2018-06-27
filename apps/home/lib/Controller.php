<?php
namespace home\lib;

use Yii;
use common\filter\UserAccessControl as Access;

class Controller extends \umeworld\lib\Controller{
	/**
	 * 返回一个登陆验证过滤器配置,要求是USERS级别的用户才能使用
	 * @see \common\filter\UserAccessControl
	 * @return type array
	 */
	public function behaviors(){
		return [
			'access' => [
				//登陆访问控制过滤
				'class' => Access::className(),
				'ruleConfig' => [
					'class' => 'yii\filters\AccessRule',
					'allow' => true,
				],
				'rules' => [
					[
						'roles' => [Access::USERS],  //'@'
					],
				]
			],
		];
	}
}