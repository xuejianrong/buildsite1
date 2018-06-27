<?php
class XxtException extends Exception{
	private $_interface = '';	//接口名称
	private $_aData = array();
	private $_aErrorList = array(
		'627' => '和教育平台运行出错，请联系和教育管理方',
		'900' => '错误的响应数据',
		'901' => '未知错误',
		'902' => '和教育服务器出错',
	);

	public function __construct($interface, $aResponse){
		if($aResponse instanceof stdClass){
			$aResponse = (array)$aResponse;
		}

		$this->_interface = $interface;
		$this->_aData = $aResponse;

		$errorCode = 900;
		if(is_array($aResponse)){
			if(isset($aResponse['Result'])){
				$errorCode = $aResponse['Result'];
			}elseif(isset($aResponse['faultstring']) || isset($aResponse['faultcode'])){
				$errorCode = 902;
			}else{
				$errorCode = 901;
			}
		}

		$errorMessage = '';
		if(isset($this->_aErrorList[$errorCode])){
			$errorMessage = $this->_aErrorList[$errorCode];
		}elseif(isset($aResponse['Desc'])){
			$errorMessage = $aResponse['Desc'];
		}else{
			$errorMessage = $this->_aErrorList[901];
		}
		$this->code = $errorCode;
		$this->message = $errorMessage;
	}

	public function getData(){
		return $this->_aData;
	}

	public function log($flag = 'xxt_exception'){
		$oException = Yii::$app->buildError($flag . '->' . $this->_interface, false, $this->_aData);
		Yii::error((string)$oException);
	}
}