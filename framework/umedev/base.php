<?php
abstract class Base{
	public static function start(){
		error_reporting(-1);
		self::_loadPath(SYSTEM_BASE_PATH . 'library/');
		self::_loadPath(SYSTEM_BASE_PATH . 'dboi/');
		self::_loadPath(PROJECT_PATH . '/apps/model/');
	}

	private static function _loadPath($path){
		$path = str_replace('\\', '/', $path);
		$pathHandle = opendir($path);
		while($file = readdir($pathHandle)){
			if($file != '.' && $file != '..'){
				if(substr(strrchr($file, '.'), 1) == 'php'){
					$incldeFile = $path . $file;
					if(!AUTO_COMPILER){
						include_once $incldeFile;
					}else{
						$GLOBALS['COMPILER'] .= $incldeFile . '||';
					}
				}
			}
		}
	}

}

$_SERVER['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
Base::start();