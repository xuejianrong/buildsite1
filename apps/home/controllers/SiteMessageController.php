<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;
use common\model\SiteMessage;

class SiteMessageController extends Controller{
	public function actionIndex(){
		return $this->render('index');
	}
	
	public function actionAdd(){
		$contactName = (string)Yii::$app->request->post('contactName');
		$tel = (string)Yii::$app->request->post('tel');
		$email = (string)Yii::$app->request->post('email');
		$companyName = (string)Yii::$app->request->post('companyName');
		$address = (string)Yii::$app->request->post('address');
		$content = (string)Yii::$app->request->post('content');
		
		if(!$contactName){
			return new Response('请填写姓名', -1);
		}
		if(!$tel && !$email){
			return new Response('请填写电话或邮箱', -1);
		}
		if(!$content){
			return new Response('请填写需求描述', -1);
		}
		
		$id = SiteMessage::insert([
			'user_id' => Yii::$app->user->id ? Yii::$app->user->id : 0,
			'contact_name' => $contactName,
			'tel' => $tel,
			'email' => $email,
			'company_name' => $companyName,
			'address' => $address,
			'content' => $content,
			'create_time' => NOW_TIME,
		]);
		
		if(!$id){
			return new Response('提交失败', 0);
		}
		return new Response('提交成功', 1);
	}

}
