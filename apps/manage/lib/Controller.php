<?php
namespace manage\lib;

use Yii;
use common\filter\ManagerAccessControl as Access;

class Controller extends \umeworld\lib\Controller{
	/**
	 * 返回一个登陆验证过滤器配置,要求是USERS级别的用户才能使用
	 * @see \common\filter\ManagerAccessControl
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