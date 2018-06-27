<?php
namespace manage\controllers;

use Yii;
//use umeworld\lib\Controller;
use manage\lib\Controller;
use umeworld\lib\Response;
use umeworld\lib\Url;
use yii\helpers\ArrayHelper;
use common\model\form\ImageUploadForm;
use yii\web\UploadedFile;

class UploadController extends Controller{
	
	public function actionUploadImage(){
		$oForm = new ImageUploadForm();
		$oForm->fCustomValidator = function($oForm){
			list($width, $height) = getimagesize($oForm->oImage->tempName);
			$oForm->toWidth = $width;
			$oForm->toHeight = $height;
			/*if($width != $height){
				$oForm->addError('oImage', '图片宽高比例应为1:1');
				return false;
			}*/
			return true;
		};
		
		$savePath = Yii::getAlias('@p.uploads') . '/' . mt_rand(10, 99);
		$oForm->oImage = UploadedFile::getInstanceByName('filecontent');
		$editorId = Yii::$app->request->get('editorid');
		if(!$oForm->upload($savePath)){
			$message = current($oForm->getErrors())[0];
			if($editorId || Yii::$app->request->get('_is_ajax') == '1?type=ajax'){
				return "<script>parent.UM.getEditor('". $editorId ."').getWidgetCallback('image')('', '" . $message . "')</script>";
			}else{
				return new Response($message, 0);
			}
		}else{
			if($editorId){
				return "<script>parent.UM.getEditor('". $editorId ."').getWidgetCallback('image')('" . $oForm->savedFile . "','" . 'SUCCESS' . "')</script>";
			}
			if(Yii::$app->request->get('_is_ajax') == '1?type=ajax'){
				return $oForm->savedFile;
			}
			return new Response('上传成功', 1, $oForm->savedFile);
		}
	}

}
