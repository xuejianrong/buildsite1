<?php
/**
*验证类
*@author Jose
*@date 2013-06-06
 */
abstract class Validate{

	private static $_currentField = '';
	private static $_method = '';
	private static $_aCheckValues = null;

	/**
	 * 生成前端所需的JS验证函数　以字符串的形式返回
	 * @param $fields 前端所需验证的控件id 多个id用 , 分隔
	 * @param $functionName 生成的JS函数名 默认为 checkForm　可自定义
	 * @param $errorCallBack 错误信息回调函数 默认为 弹出错误信息　可自定义
	 * @return string
	 **/
	public static function bulidJavascriptCode($fields, $functionName = 'checkForm' , $errorCallBackName = 'UBox.show', $aRule){

		$beforeCheckForm = '_before_'.$functionName;
		$afterCheckForm = '_after_'.$functionName;

		$aFields = explode(',', trim($fields));
		$javascriptCode = 'function ' . $functionName . '(){if(typeof('.$beforeCheckForm.') == "function"){if(false == '.$beforeCheckForm.'()){return false;}}';

		foreach($aFields as $field){
			if(isset($aRule[$field])){
				$_obj_before = self::bulidBeforeAfter('before',$field);
				$_obj_after = self::bulidBeforeAfter('after',$field);
				$javascriptCode .= $_obj_before;
				$ucField = ucfirst($field);
				$objectName = 'o' . $ucField;
				$javascriptCode .= $objectName . ' = new Validater("' . $field . '");';
				if (!empty($errorCallBackName)) {
					$javascriptCode .= $objectName . '.errorCallBack = ' . $errorCallBackName . ';';
				}
				$ruleCode = '';
				foreach($aRule[$field] as $rule){
					$valiResult = 'validate' . $ucField . 'Result';
					$ruleStr = str_replace('\\', '\\\\', $rule[0]);
					$ruleStr = str_replace('\\\\"', '\\"', $ruleStr);
					$ruleCode .= 'var ' . $valiResult . ' = ' . $objectName . '.' . $ruleStr . ';  if(' . $valiResult . ' !== true){' . $objectName . '.errorCallBack("' . $rule[1] . '", ' . $valiResult . ');return false;}';
				}
				$javascriptCode .= $ruleCode;
				$javascriptCode .= $_obj_after;
			}
		}
		$javascriptCode .=  'if(typeof(' . $afterCheckForm . ') == "function"){if(false == ' . $afterCheckForm . '()){return false;}}return true;}';
		return $javascriptCode;
	}

	/**
	 * 构建before和after某个函数的回调
	 * @param type $type
	 * @param type $field
	 * @return type
	 */
	private static function bulidBeforeAfter($type, $field){
		$fName = '_' . $type . '_' . $field;
		return 'if(typeof(' . $fName . ') == "function"){if(false == ' . $fName . '()){return false;}}';

	}

	/**
	 * 表单检查
	 * @param type $fields 要检查的字段,多个用逗号隔开
	 * @param type $method 从POST还是GET数据里取样本检测
	 * @param type $aRule 验证规则
	 * @return string 有则返回错误消息,全部通过检测则返回空字符串
	 */
	public static function check($fields, $method = 'post', $aRule = array()){
		$checkValues = strtolower($method) == 'post' ? $_POST : $_GET;
		$aFields = explode(',', trim($fields));
		foreach($aFields as $field){
			if(!isset($aRule[$field])){
				continue;
			}
			foreach($aRule[$field] as $rule){
				if(!isset($checkValues[$field])){
					return '抱歉，您的提交请求缺少参数验证失败，可能是页面出错';
				}

				$position = strpos($rule[0], '(');
				$functionName = substr($rule[0], 0, $position);
				if(!is_callable(array(__CLASS__, $functionName))){
					continue;
				}

				self::setCheckValue($checkValues[$field]);
				eval('$result = self::' . $rule[0] . ';');
				if(!$result){
					return $rule[1];
				}
			}
		}
		return '';
	}

	/**
	 * 获得要检查的变量值
	 * @return type
	 */
	public static function setCheckValue($checkValue){
		self::$_aCheckValues = !is_array($checkValue) ? array($checkValue) : $checkValue;
	}

	public static function isQQNumber(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vIsQQNumber');
		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']);
		return $result;
	}

	/**
	 * 验证是否邮箱格式
	 * @return boolean
	 */
	public static function isEmail(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vIsEmail');
		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']);
		return $result;
	}

	/**
	 * 验证字符串长度 max 为0 表示不限制长度
	 * @param type $min
	 * @param type $max
	 * @return type
	 */
	public static function length($min = 0, $max = 0){
		$values = self::$_aCheckValues;

		$GLOBALS['tmp_var'] = array(
			'ok' => true,
			'min' => $min,
			'max' => $max,
		);
		array_walk_recursive($values, 'vLength');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']);
		return $result;
	}


	/**
	 * 是否是手机号
	 * @return boolean
	 */
	public static function isPhone(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vIsPhone');
		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']);
		return $result;
	}

	/**
	 * 值不能为空
	 * @param $error
	 * @return string
	 */
	public static function notNull(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vNotNull');
		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']);
		return $result;
	}

	/**
	 * 数字取值范围
	 * @param string $min	最小值
	 * @param string $max	最大值
	 * @param $error	错误提示
	 * @return string
	 */
	public static function range($min = 'm', $max = 'n'){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array(
			'ok' => true,
			'min' => $min,
			'max' => $max,
		);
		array_walk_recursive($values, 'vRange');
		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']);
		return $result;
	}

	/**
	 * 验证日期的格式是否正确 正确的日期格式为2013-06-24 或2013/06/24
	 * @param $error
	 * @return string
	 */
	public static function isDate(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vIsDate');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function isDateOrNull(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vIsDateOrNull');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function isNumber($length = ''){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true, 'length' => $length);
		array_walk_recursive($values, 'vIsNumber');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function isInteger($length){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true, 'length' => $length);
		array_walk_recursive($values, 'vIsInteger');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function eq($defaultValue){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true, 'defaultValue' => $defaultValue);
		array_walk_recursive($values, 'vEq');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function neq($defaultValue){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true, 'defaultValue' => $defaultValue);
		array_walk_recursive($values, 'vNeq');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function _in($defaultValue){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true, 'defaultValue' => func_get_args());
		array_walk_recursive($values, 'vIn');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function notIn($defaultValue){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true, 'defaultValue' => func_get_args());
		array_walk_recursive($values, 'vNotIn');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function noStr($defaultValue){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true, 'defaultValue' => $defaultValue);
		array_walk_recursive($values, 'vNoStr');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function isLetterNumberUnderline(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vIsLetterNumberUnderline');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function haveChinese(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vHaveChinese');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}

	public static function isAllChinese(){
		$values = self::$_aCheckValues;
		$GLOBALS['tmp_var'] = array('ok' => true);
		array_walk_recursive($values, 'vIsAllChinese');

		$result = $GLOBALS['tmp_var']['ok'];
		unset($GLOBALS['tmp_var']['ok']);
		return $result;
	}
}

function vIsEmail($value){
	$result = preg_match('/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/', $value);
	if(!$result){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vLength($value){
	extract($GLOBALS['tmp_var']);
	$len = mb_strlen($value, 'UTF8');
	if($max != 0){
		if($len < $min || $len > $max){
			$GLOBALS['tmp_var']['ok'] = false;
		}
	}else{
		if($len < $min){
			$GLOBALS['tmp_var']['ok'] = false;
		}
	}
}

function vIsPhone($value){
	if (!preg_match('/^((\+86)|(86))?(1[3|4|5|8]{1}\d{9})$/', $value)) {
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vNotNull($value){
	if($value == '' || $value == null) {
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vRange($value){
	extract($GLOBALS['tmp_var']);
	if($value == '' || $value == null){
		$GLOBALS['tmp_var']['ok'] = false;
		return false;
	}

	if(!is_numeric($value)){
		$GLOBALS['tmp_var']['ok'] = false;
		return false;
	}

	if($min == 'm') {
		if($value > $max){
			$GLOBALS['tmp_var']['ok'] = false;
			return false;
		}
	}elseif($max == 'n'){
		if($value < $min){
			$GLOBALS['tmp_var']['ok'] = false;
			return false;
		}
	}else{
		if($value < $min || $value > $max){
			$GLOBALS['tmp_var']['ok'] = false;
			return false;
		}
	}
}

 function vIsDate($value){
	$matchResult = preg_match('/^(\d{4}[-|\/]\d{2}[-|\/]\d{2})$/', $value);
	if($matchResult){
		if(strpos($value, '-')){
			$aDateInfo = explode('-', $value);
		}else{
			$aDateInfo = explode('/', $value);
		}
		if(!checkdate($aDateInfo[1], $aDateInfo[2], $aDateInfo[0])){
			$GLOBALS['tmp_var']['ok'] = false;
			return false;
		}
	}
}

function vIsDateOrNull($value){
	if(!$value === ''){
		$GLOBALS['tmp_var']['ok'] = self::isDate();
	}
}

function vIsQQNumber($value){
	if(!preg_match("/^[^0]\d{4,10}$/", $value)){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vIsNumber($value){
	extract($GLOBALS['tmp_var']);
	if($length){
		if((strlen($value) != $length) || !is_numeric($value)){
			$GLOBALS['tmp_var']['ok'] = false;
		}
	}else{
		if(!is_numeric($value)){
			$GLOBALS['tmp_var']['ok'] = false;
		}
	}
}

function vIsInteger($value){
	extract($GLOBALS['tmp_var']);
	if(!preg_match('/^[0-9]+$/', $value)){
		$GLOBALS['tmp_var']['ok'] = false;
	}elseif($length){
		if((strlen($value) != $length) || !is_numeric($value)){
			$GLOBALS['tmp_var']['ok'] = false;
		}
	}else{
		if(!is_numeric($value)){
			$GLOBALS['tmp_var']['ok'] = false;
		}
	}
}

function vEq($value){
	extract($GLOBALS['tmp_var']);
	if((string)$value !== (string)$defaultValue){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vNeq($value){
	extract($GLOBALS['tmp_var']);
	if((string)$value === (string)$defaultValue){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vIn($value){
	extract($GLOBALS['tmp_var']);
	if(!in_array($value, $defaultValue)){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vNotIn($value){
	extract($GLOBALS['tmp_var']);
	if(in_array($value, $defaultValue)){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vNoStr($value){
	extract($GLOBALS['tmp_var']);
	if(preg_match('/[' . $defaultValue . ']/', $value)){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vIsLetterNumberUnderline($value){
	if(!preg_match('/^[a-zA-Z]{1}[a-zA-Z0-9_]*$/', $value)){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vHaveChinese($value){
	if(!String::haveChinese($value)){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

function vIsAllChinese($value){
	if(!preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $value)){
		$GLOBALS['tmp_var']['ok'] = false;
	}
}

