<?php
abstract class Cookie{
	public static function set($name, $value, $expire = DEFAULT_COOKIE_EXPIRE, $path = DEFAULT_COOKIE_PATH, $domain = DEFAULT_COOKIE_DOMAIN){
		if(DEFAULT_COOKIE_NAME_ENCODE){
			$name = md5($name);
		}
		$_COOKIE[$name] = $value;
		return setcookie($name, $value, $expire, $path, $domain);
    }

	public static function get($name){
		if(self::isSetted($name)){
			if(DEFAULT_COOKIE_NAME_ENCODE){
				$name = md5($name);
			}
			return $_COOKIE[$name];
		}else{
			return null;
		}
	}

	public static function isSetted($name){
		return DEFAULT_COOKIE_NAME_ENCODE ? isset($_COOKIE[md5($name)]) : isset($_COOKIE[$name]);
	}
	
	public static function setEncrypt($name, $value, $expire = DEFAULT_COOKIE_EXPIRE, $path = DEFAULT_COOKIE_PATH, $domain = DEFAULT_COOKIE_DOMAIN){
		return self::set($name, Xxtea::encrypt($value), $expire, $path, $domain);
    }

	public static function getDecrypt($name){
		return self::isSetted($name) ? Xxtea::decrypt(self::get($name)) : null;
	}

	public static function setXcrypt($name, $value, $expire = DEFAULT_COOKIE_EXPIRE, $path = DEFAULT_COOKIE_PATH, $domain = DEFAULT_COOKIE_DOMAIN){
		return self::set($name, Xxtea::xcrypt($value), $expire, $path, $domain);
	}

	public static function getXcrypt($name){
		return self::isSetted($name) ? self::get($name) : null;
	}
	
	public static function delete($name = '', $path = DEFAULT_COOKIE_PATH, $domain = DEFAULT_COOKIE_DOMAIN){
		if($name == ''){
			foreach($_COOKIE as $key => $val){
				setcookie($key, '', 0, $path, $domain);
			}
			unset($_COOKIE);
			return true;
		}else{
			unset($_COOKIE[$name]);
			return self::set($name, '', 0, $path, $domain);
		}
	}
	


}
