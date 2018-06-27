<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\CaptchaAction;
use common\model\Manager;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\filter\UserAccessControl as Access;

class SiteController extends Controller{
	
	public function actions(){
		return [
			'error' => [
				'class' => 'umeworld\lib\ErrorAction',
			],
			'captcha' => [
				'class' => 'umeworld\lib\CaptchaAction',
				'minLength' => 5,
				'maxLength' => 5,
			],
		];
	}
	
	public function behaviors(){
		return ArrayHelper::merge([
			'access' => [
				'class' => Access::className(),
				'rules' => [
					[
						'actions' => ['error', 'captcha', 'show-login', 'login'],
					],
				],
			],
		], parent::behaviors());
	}
	
	public function actionIndex(){
		return $this->render('index');
	}

	public function actionShowLogin(){
		$this->layout = 'login.php';
		return $this->render('login');
	}
	
	public function actionLogin(){
		$account = trim(strip_tags((string)Yii::$app->request->post('account')));
		$password = trim((string)Yii::$app->request->post('password'));
		$captcha = trim((string)Yii::$app->request->post('captcha'));
		$rememberme = (int)Yii::$app->request->post('rememberme');
		
		
		if(!$account){
			return new Response('请填写账号', -1);
		}
		if(!$password || strlen($password) < 6 || strlen($password) > 20){
			return new Response('密码长度为6~20个字符', -1);
		}
		if(!$captcha){
			return new Response('请输入验证码', -1);
		}
		if(!CaptchaAction::validateCaptcha($captcha, 'site/captcha')){
			return new Response('验证码不正确', -1);
		}
		$mManager = Manager::findOne(['account' => $account]);
		if(!$mManager){
			return new Response('账号或密码不正确', -1);
		}
		if($mManager->password != Manager::encryptPassword($password)){
			return new Response('密码不正确', -1);
		}
		if($mManager->is_forbidden){
			return new Response('账号已禁止', -1);
		}
		if(!Yii::$app->manager->login($mManager, $rememberme)){
			return new Response('登录失败', 0);
		}
		
		return new Response('登录成功', 1, Url::to(Yii::$app->id, 'site/index'));
	}
	
	public function actionLogout(){
		Yii::$app->manager->logout();
		return Yii::$app->response->redirect(Url::to(Yii::$app->id, 'site/show-login'));
	}
}
