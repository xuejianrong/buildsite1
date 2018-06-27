<?php
namespace umeworld\lib;

use Yii;

class CaptchaAction extends \yii\captcha\CaptchaAction{
	
	public function init(){
		parent::init();
	}

	public function run(){
        if (Yii::$app->request->getQueryParam(self::REFRESH_GET_VAR) !== null) {
            // AJAX request for regenerating code
            $code = $this->getVerifyCode(true);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'hash1' => $this->generateValidationHash($code),
                'hash2' => $this->generateValidationHash(strtolower($code)),
                // we add a random 'v' parameter so that FireFox can refresh the image
                // when src attribute of image tag is changed
                'url' => Url::to([$this->id, 'v' => uniqid()]),
            ];
        } else {
            $this->setHttpHeaders();
            Yii::$app->response->format = Response::FORMAT_RAW;
            return $this->renderImage($this->getVerifyCode(true));
        }
    }
	
	public static function validateCaptcha($input, $captchaAction){
        $oCaptchaValidator = new \yii\captcha\CaptchaValidator();
        $oCaptchaValidator->captchaAction = $captchaAction;
		$oCaptchaAction = $oCaptchaValidator->createCaptchaAction();
        return $oCaptchaAction->validate($input, false);
    }
}