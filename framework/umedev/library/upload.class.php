<?php
/**
 * 上传文件类
 * @author yang
 * 2013-05-31
 */
class UploadFile{
    public $maxSize = -1;				//上传文件的最大值
    public $allowExts = array();		//允许上传的文件后缀,留空不作后缀检查
    public $allowTypes = array();		//允许上传的文件类型,留空不做检查
    public $savePath = '';				//上传文件保存路径
    public $newFileName='';             //新文件名        // 上传文件命名规则;例如可以是 time uniqid com_create_guid 等;必须是一个无需任何参数的函数名 可以使用自定义函数
    public $autoCheck = true;			//是否自动检查附件
    public $uploadReplace = false;		//存在同名是否覆盖
    private $_error = '';				//错误信息
    private $_uploadFileInfo;			//上传成功的文件信息


    public function __construct($maxSize='', $allowExts='', $allowTypes='', $savePath='', $newFileName='', $uploadReplace = false){
        if(!empty($maxSize) && is_numeric($maxSize)){
            $this->maxSize = $maxSize;
        }

    	if(!empty($allowExts)){
			$this->allowExts = explode(',', strtolower($allowExts));
        }
        
 		if(!empty($allowTypes)){
			$this->allowTypes = explode(',', strtolower($allowTypes));
        }
        
	   if(!empty($savePath)){
            $this->savePath = $savePath;
        }	
        
       if(!empty($newFileName)){
            $this->newFileName = $newFileName;
       }
	   
	   $this->uploadReplace = $uploadReplace;
    }


    /**
     * 上传文件
     * @access public
     * @param string $savePath  上传文件保存路径
     * @return string
     * @throws ThinkExecption
     */
    public function upload()
    {
        $savePath = $this->savePath;
        if(!is_dir($savePath)){
            if(is_dir(base64_decode($savePath))){	//检查目录是否编码后的
                $savePath =	base64_decode($savePath);
            }else{
                if(!mkdir($savePath)){
                    $this->_error  =  '上传目录'.$savePath.'不存在';
                    return false;
                }
            }
        }else{
            if(!is_writeable($savePath)){
                $this->_error  =  '上传目录'.$savePath.'不可写';
                return false;
            }
        }
        $fileInfo = array();
        $isUpload = false;
        $files = $this->_dealFiles($_FILES);
        foreach($files as $key => $file) {
        	if($file['error']){
        		$this->_httpError($file['error']);
        		return false;
        	}
            if(!empty($file['name'])){//过滤无效的上传
                $file['key']        = $key;
                $file['extension']  = $this->_getExt($file['name']);
                $file['savepath']   = $savePath;
                $file['savename']   = $this->_getSaveName($file);
                if($this->autoCheck){	// 自动检查附件
                    if(!$this->_check($file)){
                        return false;
                    }
                }
                if(!$this->_save($file)){	//保存上传文件
                	return false;
                }
                unset($file['tmp_name'],$file['error']);
                $fileInfo[] = $file;	//上传成功后保存文件信息，供其他地方调用
                $isUpload   = true;
            }
        }
        if($isUpload){
            $this->_uploadFileInfo = $fileInfo;
            return true;
        }else{
            $this->_error  =  '没有选择上传文件';
            return false;
        }
    }
    
    
    /**
     * 上传单个上传字段中的文件 支持多附件
     * @access public
     * @param array $file  上传文件信息
     * @param string $savePath  上传文件保存路径
     * @return string
     * 2013-06-03
     */
    public function uploadOne($file){
    	$savePath = $this->savePath;
    	if(!is_dir($savePath)){	// 检查上传目录
    		if(!mkdir($savePath)){
    			$this->_error  =  '上传目录'.$savePath.'不存在';
    			return false;
    		}
    	}else{
    		if(!is_writeable($savePath)){
    			$this->_error  =  '上传目录'.$savePath.'不可写';
    			return false;
    		}
    	}
    	if(!empty($file['name'])) {
    		$fileArray = array();
    		if(is_array($file['name'])) {
    			$keys = array_keys($file);
    			$count	 =	 count($file['name']);
    			for ($i=0; $i<$count; $i++) {
    				foreach ($keys as $key)
    					$fileArray[$i][$key] = $file[$key][$i];
    			}
    		}else{
    			$fileArray[] =  $file;
    		}
    		$info =  array();
    		foreach ($fileArray as $key=>$file){
    		   	$file['extension']  = $this->_getExt($file['name']);
                $file['savepath']   = $savePath;
                $file['savename']   = $this->_getSaveName($file);
    			if($this->autoCheck) {
    				if(!$this->_check($file)){
    					return false;
    				}
    			}
    		 	if(!$this->_save($file)){	//保存上传文件
                	return false;
                }
    			unset($file['tmp_name'],$file['error']);
    			$info[] = $file;
    		}
    		return $info;
    	}else{
    		$this->_error  =  '没有选择上传文件';
    		return false;
    	}
    }
    
    
    /**
     * 取得上传文件的信息
     * @access public
     * @return array
     */
    public function getUploadFileInfo(){
    	return $this->_uploadFileInfo;
    }
    
    
    /**
     * 取得最后一次错误信息
     * @access public
     * @return string
     */
    public function getErrorMsg()
    {
    	return $this->_error;
    }
    
    
    /**
     * 获取错误代码信息
     * @access public
     * @param string $errorNumber  错误号码
     * @return void
     * @throws ThinkExecption
     */
    protected function _httpError($errorNumber)
    {
    	switch($errorNumber) {
    		case 1:
    			$this->_error = '请上传小于' . ini_get('upload_max_filesize') . '的文件';
    			break;
    		case 2:
    			$this->_error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
    			break;
    		case 3:
    			$this->_error = '文件只有部分被上传';
    			break;
    		case 4:
    			$this->_error = '没有文件被上传';
    			break;
    		case 6:
    			$this->_error = '找不到临时文件夹';
    			break;
    		case 7:
    			$this->_error = '文件写入失败';
    			break;
    		default:
    			$this->_error = '未知上传错误！';
    	}
    	return;
    }   
    
    private function _save($file)
    {
    	$fileName = $file['savepath'].$file['savename'];
    	if(!$this->uploadReplace && is_file($fileName)){
    		$this->_error	=	'文件已经存在！'.$fileName;// 不覆盖同名文件
    		return false;
    	}
		
		if(!$file['size']){
			$this->_error = '上传图片出错';
    		return false;
		}
		
    	// 如果是图像文件 检测文件格式
    	if(in_array(strtolower($file['extension']),array('gif','jpg','jpeg','bmp','png','swf')) && false === getimagesize($file['tmp_name'])){// 如果是图像文件 检测文件格式
    		$this->_error = '非法图像文件';
    		return false;
    	}
    	if(!move_uploaded_file($file['tmp_name'], iconv('utf-8','gbk',$fileName))){
    		$this->_error = '上传文件保存错误！';
    		return false;
    	}
    	return true;
    }
    
    
    /**
     * 转换上传文件数组变量为正确的方式
     * @access private
     * @param array $files  上传的文件变量
     * @return array
     */
    private function _dealFiles($files)
    {
       $fileArray = array();
       $n = 0;
       foreach ($files as $file){
           if(is_array($file['name'])) {
               $keys = array_keys($file);
               $count	 =	 count($file['name']);
               for ($i=0; $i<$count; $i++) {
                   foreach ($keys as $key)
                       $fileArray[$n][$key] = $file[$key][$i];
                   $n++;
               }
           }else{
               $fileArray[$n] = $file;
               $n++;
           }
       }
       return $fileArray;
    }

   

    /**
     * 取得保存文件名
     * @access private
     * @param  string $filename 数据
     * @return string
     */
    private function _getSaveName($fileName)
    {
     	$rule = $this->newFileName;
        if(empty($rule)) {//没有定义命名规则，则保持文件名不变
            $saveName = $fileName['name'];
        }else {
            if(function_exists($rule)) {
                //使用函数生成一个唯一文件标识号
                $saveName = $rule().".".$fileName['extension'];
            }else {
                //使用给定的文件名作为标识号
                $saveName = $rule.".".$fileName['extension'];
            }
        }
       	return $saveName;
    }

    
    /**
     * 检查上传的文件
     * @access private
     * @param array $file 文件信息
     * @return boolean
     */
    private function _check($file)
    {
        if($file['error']!== 0) {
            $this->_error = $file['error'];	//文件上传失败;捕获错误代码
            return false;
        }

        if(!$this->_checkType($file['type'])){	//检查文件Mime类型
            $this->_error = '上传文件MIME类型不允许！';
            return false;
        }
        
        if(!$this->_checkSize($file['size'])){	//文件上传成功，进行自定义规则检查：检查文件大小
        	$this->_error = '上传文件大小超出限制！';
        	return false;
        }
        
        if(!$this->_checkExt($file['extension'])){	//检查文件类型
            $this->_error ='文件类型不允许';
            return false;
        }
        

        if(!$this->_checkUpload($file['tmp_name'])){	//检查是否合法上传
            $this->_error = '非法上传文件！';
            return false;
        }
        return true;
    }

    /**
     * 检查上传的文件类型是否合法
     * @access private
     * @param string $type 数据
     * @return boolean
     */
    private function _checkType($type)
    {
        if(!empty($this->allowTypes)){
            return in_array(strtolower($type),$this->allowTypes);
        }
        return true;
    }


    /**
     * 检查上传的文件后缀是否合法
     * @access private
     * @param string $ext 后缀名
     * @return boolean
     */
    private function _checkExt($ext)
    {
        if(!empty($this->allowExts)){
          	return in_array(strtolower($ext),$this->allowExts,true);  
        }
        return true;
    }

    /**
     * 检查文件大小是否合法
     * @access private
     * @param integer $size 数据
     * @return boolean
     */
    private function _checkSize($size)
    {
        return !($size > $this->maxSize) || (-1 == $this->maxSize);
    }

    /**
     * 检查文件是否非法提交
     * @access private
     * @param string $filename 文件名
     * @return boolean
     */
    private function _checkUpload($fileName)
    {
        return is_uploaded_file($fileName);
    }

    /**
     * 取得上传文件的后缀
     * @access private
     * @param string $filename 文件名
     * @return boolean
     */
    private function _getExt($fileName){
        $pathinfo = pathinfo($fileName);
        return isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
    }
}