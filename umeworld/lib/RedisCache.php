<?php
namespace umeworld\lib;

use Yii;

class RedisCache extends \yii\base\Component{
	/**
	 * @var \Redis
	 */
    public $redis = null;
	public $serverName = '';
	public $defaultPart = [];
	public $dataPart = [];
	public $loginPart = [];
	public $tempPart = [];
	public $servers = [];

	public function init() {
		parent::init();
		$this->connect();
	}

	public function switchDb($serverName = ''){
		if($serverName){
			$this->serverName = $serverName;
		}
	}

	public function selectPart($part = 0){
		if($part){
			$this->redis->select($part);
		}else{
			$this->redis->select($this->dataPart['index']);
		}
	}

	public function connect(){
		if(!extension_loaded('redis')){
			return;
		}
		$host = $this->servers[$this->serverName]['host'];
		$port = $this->servers[$this->serverName]['port'];
		$password =$this->servers[$this->serverName]['password'];
		if(!$this->redis){
			$this->redis = new \Redis();
			$connect = $this->redis->connect($host, $port);
			if(!$connect){
				throw Yii::$app->buildError('连接redis缓存服务器 ' . $host . ' 失败!');
			}else{
				if($password){
					if(!$this->redis->auth($password)){
						Yii::info('redis服务器密码错误或者本身就无须密码');
					}
				}
			}
		}
		$this->selectPart();
    }

    public function add($key, $value){
        $this->redis->hMset($key, $value);
    }

    public function get($key, $value=array()){
        /*if(empty($value)){
            $this->redis->hGetAll($key);
        }else{
			$value = str_replace('`', '', $value);
			$this->redis->hMGet($key, $value);
		}*/
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

	public function deleteOne($key){
		return $this->redis->delete($key);
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

	public function expireOne($key, $seconds){
		return $this->redis->expire($key, $seconds);
	}
}