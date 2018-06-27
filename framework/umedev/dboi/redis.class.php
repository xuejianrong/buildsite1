<?php
class RedisCache{
    public $redis = null;

    public function connect($redisServerName, $redisPart = 0){
        if(!isset($GLOBALS['redis_connection'][$redisServerName])){
            $redisHost = $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$redisServerName]['host'];
            $redisPort = $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$redisServerName]['port'];
            $redisPassword = $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$redisServerName]['password'];
            $GLOBALS['redis_connection'][$redisServerName] = new Redis();
            $connect = $GLOBALS['redis_connection'][$redisServerName]->connect($redisHost, $redisPort);
            if(!$connect){//如果连接不成功
                $GLOBALS['DB_CONFIG']['REDIS_SERVER'][$redisServerName]['is_active'] = 0;//关闭redis操作
				throw Yii::$app->buildError('无法连接redis服务器: ' . $redisServerName);
            }else{
                if(!empty($redisPassword)){
                    $GLOBALS['redis_connection'][$redisServerName]->auth($redisPassword);
                }
            }
        }
        $this->redis = $GLOBALS['redis_connection'][$redisServerName];
		if($redisPart){
			$this->select($redisPart);
		}else{
			$this->select();
		}
    }

    public function select($redisPort = 0){
		if($redisPort){
			$this->redis->select($redisPort);
		}else{
			$this->redis->select($GLOBALS['DB_CONFIG']['REDIS_PART']);
		}
    }

    public function add($key, $value){
        $this->redis->hMset($key, $value);
    }

    public function get($key, $value = array()){
		$this->redis->hGetAll($key);
    }

    public function update($key, $value){
		$keyExist = $this->redis->keys($key);
        if(!empty($keyExist)){
			//如果存在
			$value = str_replace('`', '', $value);
			$this->redis->hMset($key, $value);
        }
    }

    public function delete($keys){
		if(!empty($keys)){
            $this->redis->delete($keys);
        }
    }

    public function addToList($key, $value){
		self::select();
        if(!empty($value)){
            return $this->redis->rPush($key, $value);
        }
    }

    public function getList($key, $start=0, $end=-1){
		self::select();
        return $this->redis->lRange($key, $start, $end);
    }

    public function delFromList($key, $value){
		self::select();
        return $this->redis->lRem($key, $value, 0);
    }

	public function getOne($key){
		return $this->redis->hGetAll($key);
    }

	public function expire($key, $seconds){
		$this->redis->expire($key, $seconds);
	}

	public function deleteOne($key){
		return $this->redis->delete($key);
    }

	public function expireOne($key, $seconds){
		return $this->redis->expire($key, $seconds);
	}
}