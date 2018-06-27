<?php
namespace umeworld\lib;

use Yii;

/**
 * 应用程序
 */
class Application extends \yii\web\Application{
	/**
	 * @var array APP列表,元素就是别名
	 */
	public $aWebAppList = [];

	/**
	 * 域名
	 * @var string
	 */
	public $domain = '';

	private $_urlManagerName = '';	//当前APP要指定使用URL管理器的名称,因为一个APP会有N个URL管理器

	/**
	 * 初始化应用程序
	 * @param array $aConfig 应用程序配置
	 */
	public function __construct($aConfig = [])
    {
		parent::__construct($aConfig);
		$urlManagerName = $this->urlManagerName;
		$this->urlManager = $this->$urlManagerName;
		$this->_initConfigTojsFile();
    }
		
	/**
	 * 获取URL管理器的名称
	 * @return string URL管理器的名称
	 */
	public function getUrlManagerName(){
		return $this->_urlManagerName;
	}

	/**
	 * 设置URL管理器的名称
	 * @param string $name 要设置的URL管理器名称字符
	 */
	public function setUrlManagerName($name){
		$this->_urlManagerName = $name;
	}

	/**
	 * 获取URL管理器
	 * @return \yii\web\UrlManager
	 */
    public function getUrlManager(){
        return $this->get($this->urlManagerName);
    }

	/**
	 * 设置URL管理器
	 * @param \yii\web\UrlManager $oUrlManager 一个现成的管理器对象实例,只是作引用拷贝
	 */
	public function setUrlManager($oUrlManager){
		$this->urlManager = $oUrlManager;
	}

	/**
	 * 构造一个APP错误
	 * @param string $message 错误信息
	 * @param bool $isSendToEndUser 是否将错误信息发送给用户看
	 * @param mixed $xAddLogData 附加协助追查的日志数据
	 * @param int $errorCode 错误代码
	 * @return \umeworld\lib\ServerErrorHttpException
	 */
	public function buildError($message = '', $isSendToEndUser = false, $xAddLogData = null, $errorCode = 500){
		if(!$message){
			$message = Yii::$app->ui->getTips('error.common');
		}
		if(!YII_ENV_PROD){
			$isSendToEndUser = true;
		}
		return new ServerErrorHttpException($message, $isSendToEndUser, $xAddLogData, $errorCode);
	}

	/**
	 * 加载静态资源资源到别名中
	 */
	public function loadResource(){
		$fInitResourceAlias = function(array $aResourceList, $appId){
			foreach($aResourceList as $key => $aResource){
				if(is_string($aResource)){
					$fileName = '';
					if(strpos($aResource, '@r.url') === false){
						$fileName = Yii::getAlias('@' . $appId) . '/web' . $aResource;
					}else{
						$fileName = str_replace('@r.url', Yii::getAlias('@p.resource'), $aResource);
					}
					if(file_exists($fileName)){
						Yii::setAlias('r.' . $key, $aResource . '?v=' . date('YmdHis', filemtime($fileName)));
					}else{
						Yii::setAlias('r.' . $key, $aResource);
					}
					continue;
				}

				$refUrl = Yii::getAlias($aResource['ref']);
				if(isset($aResource['last_time'])){
					$refUrl .= '?v=' . $aResource['last_time'];
				}
				Yii::setAlias('r.' . $key, $refUrl);
			}
		};

		$fInitResourceAlias(require(Yii::getAlias('@common/config/resource.php')), $this->id);
		$fInitResourceAlias(require(Yii::getAlias('@' . $this->id . '/config/resource.php')), $this->id);
	}
	
	private function _initConfigTojsFile(){
		$aAppList = Yii::$app->aWebAppList;
		$lastcTime = 0;
		
		foreach($aAppList as $app){
			$fileName = Yii::getAlias('@apps/' . $app . '/config/url.php');
			$ctime = filemtime($fileName);
			if($ctime && $ctime > $lastcTime){
				$lastcTime = $ctime;
			}
		}
			
		$saveFileName = Yii::getAlias('@p.resource') . '/data/temp/config.data' . $lastcTime . '.js';
		if(!file_exists($saveFileName)){
			$files = glob(Yii::getAlias('@p.resource') . '/data/temp/*.js');
			foreach($files as $file){
				if(is_file($file)){
					unlink($file);
				}
			}
			$content = '';
			$aConfigDataList = [];
			foreach($aAppList as $app){
				$fileName = Yii::getAlias('@apps/' . $app . '/config/url.php');
				if(is_file($fileName)){
					$aConfigDataList[$app] = require($fileName);
				}
			}
			
			$content .= 'var aUrlMapConfigList = ' . json_encode($aConfigDataList) . ';';
			file_put_contents($saveFileName, '');
			file_put_contents(Yii::getAlias('@p.resource') . '/data/temp/config.data.js', $content);
		}
	}
	
}