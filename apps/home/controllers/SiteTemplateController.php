<?php
namespace home\controllers;

use Yii;
use umeworld\lib\Controller;
//use home\lib\Controller;
use umeworld\lib\Response;

class SiteTemplateController extends Controller{
	
	public function actionIndex(){
		return $this->render('index');
	}

}
