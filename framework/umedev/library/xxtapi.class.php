<?php
if(IS_DISPLAY_ERROR){
	//测试版
	define('XXT_VERSIONCODE', '2.02');	//校迅通平台版本号
	define('XXT_PERFORMCODE', '180fd945-ac63-4a7d-9715-8e25164450e0');	//校迅通平台代码
	define('XXT_PLATFORMKEY', 'csymf2014');	//校迅通平台约定密匙
	define('XXT_API_URL', 'http://zsqtsm.vicp.net:63088/services/eduSOAP?wsdl');	//校迅通平台接口地址
	define('XXT_API_LOGIN_URL', 'http://zsqtsm.vicp.net:63087/oauth/login.do?appCode=csymf2014&display=mobile');	//校迅通平台登录组件地址
	define('PC_API_LOGIN_URL', 'http://zsqtsm.vicp.net:63087/oauth/login.do?appCode=csymf');	//校迅通平台登录组件地址
	define('XXT_IMG_DOMAIN', 'http://edu.gd.chinamobile.com/');	//校迅通头像域名
}else{
	//正式版
	define('XXT_VERSIONCODE', '2.02');	//校迅通平台版本号
	define('XXT_PERFORMCODE', 'dc722c22-9f81-47cd-bae5-6d67b337a500');	//校迅通平台代码
	define('XXT_PLATFORMKEY', 'pcymf');	//校迅通平台约定密匙
	define('XXT_API_URL', 'http://api.ydxxt.com/services/eduSOAP?wsdl');	//校迅通平台接口地址
	define('XXT_API_LOGIN_URL', 'http://open.edu.gd.chinamobile.com/oauth/login.do?appCode=appymf&display=mobile');	//校迅通平台登录组件地址
	define('PC_API_LOGIN_URL', 'http://open.edu.gd.chinamobile.com/oauth/login.do?appCode=pcymf');	//校迅通平台登录组件地址
	define('XXT_IMG_DOMAIN', 'http://edu.gd.chinamobile.com/');	//校迅通头像域名
}

class XxtApi{
	private $_host = '';
	private $_port = '';
	private $_path = '';
	private $_msgType = '';
	private $_aRequestParams = array();

	public function __construct(){
		$aUrl = parse_url(XXT_API_URL);
        $this->_host = $aUrl['host'];
        $this->_port = isset($aUrl['port']) ? $aUrl['port'] : 80;
        $this->_path = $aUrl['path'];
	}

	/**
	 * 发送校迅通平台接口请求
	 * @param $msgType 消息类型
	 * @param $aRequestParams 请求报文体参数数组
	 */
	public function sendRequest($msgType, $aRequestParams){
		$this->_msgType = $msgType;	//消息类型
		$this->_aRequestParams = $aRequestParams;	//请求报文体参数数组

		$timeStamp = date('Y-m-d H:i:s');	//时间戳
		$msgSeq = time() . rand(10000, 99999);	//消息序列号
		$skey = md5(XXT_PLATFORMKEY . $timeStamp . $msgSeq . $this->_msgType . XXT_PERFORMCODE);	//数据有效性签名
		$body = self::_buildRequestMessagebodyByMsgType();	//请求消息体

		$aRequestData = array(
			'Version' => XXT_VERSIONCODE,
			'MsgSeq' => $msgSeq,
			'TimeStamp' => $timeStamp,
			'MsgType' => $this->_msgType,
			'PerformCode' => XXT_PLATFORMKEY,
			'Skey' => $skey,
			'Body' => $body
		);

		$message = self::_buildRequestMessage($aRequestData);
		$returnString = self::_sentSocket($message);
		$aResult = self::_xmlToArray($returnString);

		if(isset($aResult['Body'])){
			$aResult['Body'] = htmlspecialchars_decode($aResult['Body']);
			$aMsgBody = self::_xmlToArray($aResult['Body']);
			if(is_array($aMsgBody)){
				$aResult = array_merge($aResult, $aMsgBody);
			}
		}

		return $aResult;
	}

	/**
	 * 组装请求报文xml
	 */
	private function _buildRequestMessage($aRequestData){
		$xmlString = '';
		$xmlString .= '<?xml version="1.0" encoding="UTF-8"?>';
		$xmlString .= '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.cmcc.com/edu/">';
		$xmlString .= '<SOAP-ENV:Body>';
		$xmlString .= '<ns1:Request>';

		foreach($aRequestData as $key => $value){
			$xmlString .= '<' . $key . '>' . $value . '</' . $key . '>';
		}

		$xmlString .= '</ns1:Request>';
		$xmlString .= '</SOAP-ENV:Body>';
		$xmlString .= '</SOAP-ENV:Envelope>';

		return $xmlString;
	}

	/**
	 * 组装请求报文体xml
	 */
	private function _buildRequestMessagebodyByMsgType() {
		//数组转换成xml格式
		$xml = $this->_arrayToXml($this->_aRequestParams);
		if (!$xml) {
			alert('参数出错');
		}
		$xmlString = trim($xml, '<?xml version="1.0" encoding="utf-8"?>');
		$xmlString = htmlspecialchars($xmlString);
		return $xmlString;
	}

	/**
	 * 数组转xml
	 * @param type $aData 要转换的数组
	 * @param type $rootNodeName 跟节点
	 * @param type $xml
	 * @return type xml
	 */
	private function _arrayToXml($aData, $rootNodeName = 'MSG_BODY', $xml = null) {
		if (!is_array($aData)) {
			return false;
		}
		if ($xml == null) {
			$xml = simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><' . $rootNodeName . ' />');
		}

		foreach ($aData as $key => $value) {
			if (is_numeric($key)) {
				$key = 'unknownNode_' . (string) $key;
			}
			$key = preg_replace('/[^a-z]/i', '', $key);
			if (is_array($value)) {
				$node = $xml->addChild($key);
				$this->_arrayToXml($value, $rootNodeName, $node);
			} else {
				$value = htmlentities($value);
				$xml->addChild($key, $value);
			}
		}
		return $xml->asXML();
	}

	/**
	 * 发送Socket请求
	 */
	private function _sentSocket($content){
		$sendContent = "POST " . $this->_path . " HTTP/1.0\r\n";
		$sendContent .= "Host: " . $this->_host . ":" . $this->_port . "\r\n";
		$sendContent .= "User-Agent: UMFun v1.0\r\n";
		$sendContent .= "Accept: text/xml\r\n";
		$sendContent .= "Accept-encoding: gzip\r\n";
		$sendContent .= "Accept-language: en-us,zh-cn\r\n";
		$sendContent .= "Connection: Keep-Alive\r\n";
		$sendContent .= "Cache-Control: no-cache\r\n";
		$sendContent .= "Content-Type: text/xml; charset=utf-8\r\n";
		$sendContent .= "SOAPAction: \"http://www.cmcc.com/edu/EDU\"\r\n";
		$sendContent .= "Content-Length: " . strlen($content) . "\r\n\r\n";
		$sendContent .= $content;

		$returnString = '';
		$fp = fsockopen($this->_host, $this->_port, $errno, $errstr, 1);
		if(!$fp){
			alert($errstr . PHP_EOL . $errno);
		}else{
			fputs($fp, $sendContent);
			while(!feof($fp)){
				$returnString .= fgets($fp, 4096);
			}
			fclose($fp);
		}

		return $returnString;
	}



	/**
	 * xml字符串转数组
	 * @param $xmlString xml字符串
	 */
	private function _xmlToArray($xmlString){
		$aData = array();
		$reg = '/<\s*(\\w+)[^>]*?\s*>([\\x00-\\xFF]*?)<\s*\\/\s*\\1\s*>/';
		if(preg_match_all($reg, $xmlString, $matches)){
			$count = count($matches[0]);
			for($i = 0; $i < $count; $i++){
				$key= $matches[1][$i];
				$val = self::_xmlToArray($matches[2][$i]);
				if(array_key_exists($key, $aData)){
					if(is_array($aData[$key])){
						if(!array_key_exists(0, $aData[$key])){
							$aData[$key] = array($aData[$key]);
						}
					}else{
						$aData[$key] = array($aData[$key]);
					}
					$aData[$key][] = $val;
				}else{
					$aData[$key] = $val;
				}
			}
			return $aData;
		}else{
			return $xmlString;
		}
	}

}