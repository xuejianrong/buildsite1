<?php
namespace umeworld\lib;

use Yii;

class Memcache extends \yii\caching\MemCache{

	public function init() {
		if(extension_loaded('memcache') || extension_loaded('memcached')){
			parent::init();
		}
	}

}