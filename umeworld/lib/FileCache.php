<?php
namespace umeworld\lib;

use Yii;
use yii\helpers\FileHelper;

class FileCache extends \yii\caching\FileCache{
    public function init(){
        parent::init();
    }

    public function exists($key){
		if($this->_existNeverExpireKey($key)){
			return true;
		}
        $cacheFile = $this->getCacheFile($this->buildKey($key));

        return @filemtime($cacheFile) > time();
    }

    protected function getValue($key){
		$isNeverExpireKey = $this->_existNeverExpireKey($key);
        $cacheFile = $this->getCacheFile($key);
		if($isNeverExpireKey){
			$cacheFile = $this->getNeverExpireCacheFile($key);
		}

        if ($isNeverExpireKey || @filemtime($cacheFile) > time()) {
            $fp = @fopen($cacheFile, 'r');
            if ($fp !== false) {
                @flock($fp, LOCK_SH);
                $cacheValue = @stream_get_contents($fp);
                @flock($fp, LOCK_UN);
                @fclose($fp);
                return $cacheValue;
            }
        }

        return false;
    }

    protected function setValue($key, $value, $duration){
        $this->gc();
        $cacheFile = $this->getCacheFile($key);
		if($duration <= 0){
			$this->_addNeverExpireKey($key);
			$cacheFile = $this->getNeverExpireCacheFile($key);
		}
        if ($this->directoryLevel > 0) {
            @FileHelper::createDirectory(dirname($cacheFile), $this->dirMode, true);
        }
        if (@file_put_contents($cacheFile, $value, LOCK_EX) !== false) {
            if ($this->fileMode !== null) {
                @chmod($cacheFile, $this->fileMode);
            }
            /*if ($duration <= 0) {
                $duration = 31536000000; // 1000 year
            }*/

            return @touch($cacheFile, $duration + time());
        } else {
            $error = error_get_last();
            Yii::warning("Unable to write cache file '{$cacheFile}': {$error['message']}", __METHOD__);
            return false;
        }
    }

    protected function addValue($key, $value, $duration){
        $cacheFile = $this->getCacheFile($key);
        if ($duration > 0 && !$this->_existNeverExpireKey($key) && @filemtime($cacheFile) > time()) {
            return false;
        }

        return $this->setValue($key, $value, $duration);
    }
	
	protected function deleteValue($key){
        $cacheFile = $this->getCacheFile($key);
		if($this->_existNeverExpireKey($key)){
			$this->_deleteNeverExpireKey($key);
			$cacheFile = $this->getNeverExpireCacheFile($key);
		}

        return @unlink($cacheFile);
    }
	
	protected function getNeverExpireCacheFile($key){
        if ($this->directoryLevel > 0) {
            $base = Yii::getAlias('@p.resource') . '/data/cache';
            for ($i = 0; $i < $this->directoryLevel; ++$i) {
                if (($prefix = substr($key, $i + $i, 2)) !== false) {
                    $base .= DIRECTORY_SEPARATOR . $prefix;
                }
            }

            return $base . DIRECTORY_SEPARATOR . $key . $this->cacheFileSuffix;
        } else {
            return Yii::getAlias('@p.resource') . '/data/cache' . DIRECTORY_SEPARATOR . $key . $this->cacheFileSuffix;
        }
    }

	private function _getNeverExpireKeyFile(){
		return Yii::getAlias('@p.resource') . '/data/never_expire_key_list.bin';
	}
	
	private function _getNeverExpireKeyList(){
		$file = $this->_getNeverExpireKeyFile();
		$aList = [];
		if(!file_exists($file)){
			@file_put_contents($file, serialize($aList), LOCK_EX);
		}else{
			$aList = unserialize(file_get_contents($file));
		}
		return $aList;
	}
	
	private function _addNeverExpireKey($key){
		$aList = $this->_getNeverExpireKeyList();
		if(!in_array($key, $aList)){
			array_push($aList, $key);
			@file_put_contents($this->_getNeverExpireKeyFile(), serialize($aList), LOCK_EX);
		}
	}
	
	private function _deleteNeverExpireKey($key){
		$aList = [];
		$aTempList = $this->_getNeverExpireKeyList();
		foreach($aTempList as $v){
			if($v != $key){
				array_push($aList, $v);
			}
		}
		@file_put_contents($this->_getNeverExpireKeyFile(), serialize($aList), LOCK_EX);
	}
		
	private function _existNeverExpireKey($key){
		$aList = $this->_getNeverExpireKeyList();
		if(!in_array($key, $aList)){
			return false;
		}else{
			return true;
		}
	}
	
}
