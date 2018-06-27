<?php
/**
 * 图片处理类
 * @author 罗启军 <lqjdjj@yahoo.cn>
 * @date  2013-5-31
 */
 
class Image{
    public $soureIm = '';	//源图片
    public $destImPath = '';	//目标图片保存的路径和名称
	public $waterPos = 9;	//水印位置  * 0为随机位置；1为顶端居左，2为顶端居中，3为顶端居右；4为中部居左，5为中部居中，6为中部居右；7为底端居左，8为底端居中，9为底端居右；
	public $waterText = '';		//水印文字
	public $textFont = '';		//水印文字大小
	public $textColor = '#FF0000';	//水印字体颜色
	public $fontTtf = '';	//水印文字的字体文件
	public $waterImage = '';	//水印图片路径和名字
	public $x1 = 10;	//被截图片的开始截取位置的X坐标
	public $y1 = 10;	//被截图片的开始截取位置的Y坐标
	public $x2 = 50;	//被截图片的结束截取位置的X坐标
	public $y2 = 50;	//被截图片的结束截取位置的Y坐标
	public $thumbW = 100;	//缩略图宽度
	public $thumbH = 100;	//缩略图高度
    protected $_tmpIm = null;	//临时图片
    protected $_imW = 200;	//临时图片宽
    protected $_imH = 200;	//临时图片高
    protected $_ext = 2;	//源图片格式 1：GIF 格式 2：JPEG/JPG 格式 3：PNG 格式
	protected $_imageExt = array('gif', 'jpg', 'jpeg', 'png');	//支持处理的图片格式
	protected $_result = array();	//返回的错误信息
	protected $_execSequence = array();		//截图，缩略图和打水印的执行方法的顺序，先设置参数的先执行
	

	/**
	 * 设置源图片
	 * @param string $imPath   源图片路径和名称
	 */
	public function setSoureImage($imPath){
		$this->soureIm = $imPath;
		$this->_tmpIm = null;	//清空临时的源图片
		$this->_execSequence = array();		//清空动作参数,默认全部不执行
		$this->_result = array();	//清空返回的错误信息		
    }

    /**
     * 设置缩略图宽度和高度
     * @param int $toW 生成缩略图的宽
     * @param int $toH 生成缩略图的高
     */
    public function setThumb($toW, $toH){
		$this->thumbW = $toW;
		$this->thumbH = $toH;
		$this->_execSequence[] = 'thumb';
    }

    /**
     * 设置截图，截取矩形的图片的参数
     * @param type $x1  被截图片的开始截取位置的X坐标
     * @param type $y1  被截图片的开始截取位置的Y坐标
     * @param type $x2  被截图片的结束截取位置的X坐标
	 * @param type $y2  被截图片的结束截取位置的Y坐标
     */
    public function setScreenshot($x1, $y1, $x2, $y2){
		$this->x1 = $x1;
		$this->y1 = $y1;
		$this->x2 = $x2;
		$this->y2 = $y2;
		$this->_execSequence[] = 'screenshot';	//设置截图的执行和其执行步骤
    }

   /**
    * 设置图片加水印的参数
    * $groundImage 背景图片，即需要加水印的图片，暂只支持GIF,JPG,PNG格式；
    * $waterPos 水印位置，有10种状态：
    * 0为随机位置；
    * 1为顶端居左，2为顶端居中，3为顶端居右；
    * 4为中部居左，5为中部居中，6为中部居右；
    * 7为底端居左，8为底端居中，9为底端居右；
    * $waterImage 图片水印，即作为水印的图片，暂只支持GIF,JPG,PNG格式；
    * $waterText 文字水印，即把文字作为为水印，支持ASCII码，不支持中文；
    * $textFont 文字大小，值为1、2、3、4或5，默认为5；
    * $textColor 文字颜色，值为十六进制颜色值，默认为#FF0000(红色)；
    * $waterImage 和 $waterText 最好不要同时使用，选其中之一即可，优先使用 $waterImage。
    * 当$waterImage有效时，参数$waterString、$stringFont、$stringColor均不生效。
    * 加水印后的图片的文件名和 $groundImage 一样
    */
    public  function setWatermark($waterImage = '', $waterPos = 9, $waterText = '', $textFont = 0, $textColor = '#FF0000', $fontTtf = ''){
		$this->waterImage = $waterImage;
		$this->waterPos = $waterPos;
		$this->waterText = $waterText;
		$this->textFont = $textFont;
		$this->textColor = $textColor;
		$this->fontTtf = $fontTtf;
		$this->_execSequence[] = 'watermark';
    }

    /**
     * 输出图片
     */
    public function display(){
		$this->_imageProcess();		//图片处理
		
		//错误检查
		if(isset($this->_result['status']) && isset($this->_result['error'])){
			return $this->_result;
		}
		
		if($this->_ext == 1){
			header('Content-type: image/gif');
		    imagegif($this->_tmpIm);
		}elseif($this->_ext == 2){
			header('Content-type: image/jpeg');
			imagejpeg($this->_tmpIm);
		}elseif($this->_ext == 3){
			header('Content-type: image/png');
			imagepng($this->_tmpIm);
		}else{
			header('Content-type: image/jpeg');
			imagejpeg($this->_tmpIm);
		}
	}

	/**
	 * 处理图片（截图，打水印和缩略图中的0个或多个）并保存
	 * @param type $destImagePath	目标文件保存的路径和名称
	 * @return boolean 保存成功则返回TRUE，失败则返回FALSE或带错误信息的数组
	 */
	public function save($destImagePath){
		$this->_imageProcess();		//图片处理
		
		//错误检查
		if(isset($this->_result['status']) && isset($this->_result['error'])){
			return $this->_result;
		}
		
		$this->destImPath = $destImagePath;
		$destImageInfo = pathinfo($this->destImPath);

		//检查目标图片存放目录
		if(isset($destImageInfo['dirname'])){
			if(!is_dir($destImageInfo['dirname'])){
				$this->_result['status'] = 0;
				$this->_result['error'] = 'SAVE_PATH_NOT_FOUND';
				return $this->_result;
			}
		}
		
		//错误检查
		if(isset($this->_result['status']) && isset($this->_result['error'])){
			return $this->_result;
		}
		
		//检查目标图片格式
		if(isset($destImageInfo['extension'])){
			if(!in_array(strtolower($destImageInfo['extension']),  $this->_imageExt)){
				$this->_result['status'] = 0;
				$this->_result['error'] = 'IMAGE_SAVE_EXTENSION_UNSUPPORT';
				return $this->_result;
			}
		}else{
			$this->_result['status'] = 0;
			$this->_result['error'] = 'IMAGE_SAVE_EXTENSION_UNKNOW';
			return $this->_result;
        }
		
		//错误检查
		if(isset($this->_result['status']) && isset($this->_result['error'])){
			return $this->_result;
		}
		
	
		//保存图片文件
		if($this->_ext == 1){
			return imagegif($this->_tmpIm, $this->destImPath);
		}elseif($this->_ext == 2){
			return imagejpeg($this->_tmpIm, $this->destImPath);
		}elseif($this->_ext == 3){
			return imagepng($this->_tmpIm, $this->destImPath);
		}
	}
	
	/**
	 * 处理图片，执行缩略图，截图和打水印等方法
	 */
	protected function _imageProcess(){
		$this->_initSoureImage();	//检查并初始化源图片
		
		//检查源图片初始化是否成功
		if(isset($this->_result['status']) && isset($this->_result['error'])){
			return $this->_result;
		}
		
		//按设置的参数的顺序执行打水印，缩略图和截图方法，有设置的才会执行
		foreach($this->_execSequence as $key => $val){
			$this->_updateImageInfo();	//更新源图片的宽高等属性
			if($val == 'watermark'){				
				$this->_watermark();	//打水印
			}elseif($val == 'thumb'){
				$this->_thumb();	//生成缩略图
			}elseif($val == 'screenshot'){
				$this->_screenshot();	//截图
			}
			if(isset($this->_result['status']) && isset($this->_result['error'])){
				return $this->_result;
			}
			unset($this->_execSequence[$key]);	
		}
	}

	/**
	 * 根据图片来生成缩略图或截图
	 * @param type $img	源图片
	 * @param int $dstX	目标图片 X 坐标点
	 * @param int $dstY	目标图片 Y 坐标点
	 * @param int $srcX	源图片的 X 坐标点
	 * @param int $srcY	源图片的 Y 坐标点
	 * @param int $creatW		目标图片宽度
	 * @param int $creatH		目标图片高度
	 * @param int $srcImgW		源图片宽度
	 * @param int $srcImgH		源图片高度
	 * @return type $creatImage   创建的图片
	 */
	protected function _creatImage($img, $dstX, $dstY, $srcX, $srcY, $creatW, $creatH, $srcImgW, $srcImgH){
		if(function_exists('imagecreatetruecolor')){
			$creatImage = imagecreatetruecolor($creatW, $creatH);
		 }else{
			$creatImage = imagecreate($creatW, $creatH);
		 }
		 
		 //gif和png图的透明处理
		 $imageInfo = getimagesize($this->soureIm);
		 $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
		 
		 if($imageType == 'gif'){
			 $trnprt_indx = imagecolortransparent($img);  //获得源图片的透明色位置
			 if ($trnprt_indx >= 0) {
					//its transparent
				   $trnprt_color = imagecolorsforindex($img , $trnprt_indx); //根据透明色位置取得透明色值
				   $trnprt_indx = imagecolorallocate($creatImage, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);   //填充透明色到缩略图中
				   imagefill($creatImage, 0, 0, $trnprt_indx);    //再填充图片内容
				   imagecolortransparent($creatImage, $trnprt_indx);  //定义trnprt_indx位置的颜色为透明色
			}
		}elseif($imageType == 'png'){
			imagealphablending($creatImage, false);//取消默认的混色模式（为解决阴影为绿色的问题）
			imagesavealpha($creatImage,true);//设定保存完整的 alpha 通道信息（为解决阴影为绿色的问题） 
		}
		imagecopyresampled($creatImage, $img, $dstX, $dstY, $srcX, $srcY, $creatW, $creatH, $srcImgW, $srcImgH);
		return $creatImage;
	}

	/**
	 * 更新源图片的宽高等属性
	 */
	protected function _updateImageInfo(){
		if($this->_tmpIm){
			$this->_imW = imagesx($this->_tmpIm);
			$this->_imH = imagesy($this->_tmpIm);
		}
	}
	
	/**
	 * 检查并初始化源图片
	 */
	protected function _initSoureImage(){
		if($this->_tmpIm == null){		//未初始化的时候才执行
			if(!file_exists($this->soureIm)){
				$this->_result['status'] = 0;
				$this->_result['error'] = 'SOURCE_IMAGE_NOT_FOUND';
				return $this->_result;
			}

			//检查图片的文件信息
			$imFileInfo = pathinfo($this->soureIm);
			if(isset($imFileInfo['extension'])){
				if(!in_array(strtolower($imFileInfo['extension']),$this->_imageExt)){
					$this->_result['status'] = 0;
					$this->_result['error'] = 'SOURCE_IMAGE_EXTENSION_UNSUPPORT';
					return $this->_result;
				}
			}else{
				$this->_result['status'] = 0;
				$this->_result['error'] = 'SOURCE_IMAGE_EXTENSION_UNKNOWN';
				return $this->_result;
			}

			$imInfo = getimagesize($this->soureIm);    //获取图片信息
			if(!$imInfo){
				$this->_result['status'] = 0;
				$this->_result['error'] = 'SOURCE_IMAGE_IS_NOT_IMAGE_FILE';
				return $this->_result;
			}
			$this->_imW = $imInfo[0];
			$this->_imH = $imInfo[1];
			$this->_ext = $imInfo[2];

			//根据载入图片生成一个临时图片
			switch ($this->_ext){
				case 1:
					if(!function_exists('imagecreatefromgif')){
						$this->_result['status'] = 0;
						$this->_result['error'] = 'FUNCTION_IMAGECREATEFROMGIF_UNSUPPORT';
						return $this->_result;
					}
					$this->_tmpIm = imagecreatefromgif($this->soureIm);
					break;

				case 2:
					if(!function_exists('imagecreatefromjpeg')){
						$this->_result['status'] = 0;
						$this->_result['error'] = 'FUNCTION_IMAGECREATEFROMJPEG_UNSUPPORT';
						return $this->_result;
					}
					$this->_tmpIm = imagecreatefromjpeg($this->soureIm);
					break;

				case 3:
					if(!function_exists('imagecreatefrompng')){
						$this->_result['status'] = 0;
						$this->_result['error'] = 'FUNCTION_IMAGECREATEFROMPNG_UNSUPPORT';
						return $this->_result;
					}
					$this->_tmpIm = imagecreatefrompng($this->soureIm);
					break;
			}
		}
	}
	
	/**
	 * 生成缩略图
	 */
	protected function _thumb(){
		$toWH = $this->thumbW / $this->thumbH;    //缩略图的宽高比
        $srcWH = $this->_imW / $this->_imH;    //源图片的宽高比

        //设定缩略图的比例宽高
        if($toWH <= $srcWH){
            $ftoW = $this->thumbW;
            $ftoH = $ftoW * ($this->_imH / $this->_imW);
        }else{
              $ftoH = $this->thumbH;
              $ftoW = $ftoH * ($this->_imW / $this->_imH);
        }

        //生成高质量缩略图
        if($this->_imW > $this->thumbW || $this->_imH > $this->thumbH){
			$this->_tmpIm = $this->_creatImage($this->_tmpIm,  0, 0, 0, 0, $ftoW, $ftoH, $this->_imW, $this->_imH);
        }else{
			$this->_tmpIm = $this->_creatImage($this->_tmpIm, 0, 0, 0, 0, $this->_imW, $this->_imH, $this->_imW, $this->_imH);
        }
	}
	
	/**
	 * 截图
	 */
	protected function _screenshot(){
		$screenW = abs($this->x2 - $this->x1);
		$screenH = abs($this->y2 - $this->y1);
		$longestScreenW = $this->_imW - $this->x1;    //根据x1和源图宽度确定最大可截图的宽
		$longestScreenH = $this->_imH - $this->y1;    //根据y1和源图高度确定最大可截图的高

		if($screenW > $longestScreenW){
			$screenW = $longestScreenW;
		}
		if($screenH > $longestScreenH){
			$screenH = $longestScreenH;
		}
        $this->_tmpIm = $this->_creatImage($this->_tmpIm, 0, 0, $this->x1, $this->y1, $screenW, $screenH, $screenW, $screenH);
	}
	
	/**
	 * 打水印
	 */
	protected function _watermark(){
		$isWaterImage = 0;

        //读取水印文件
        if(!empty($this->waterImage)){
            $isWaterImage = TRUE;
            $waterInfo = getimagesize($this->waterImage);
            if(!$waterInfo){
                $this->_result['status'] = 0;
				$this->_result['error'] = 'WATER_FILE_IS_NOT_IMAGE';
				return $this->_result;
            }
            $waterW = $waterInfo[0];   //取得水印图片的宽
            $waterH = $waterInfo[1];   //取得水印图片的高

            switch($waterInfo[2]){  //取得水印图片的格式
                case 1:
                    $waterIm = imagecreatefromgif($this->waterImage);
                    break;
                case 2:
                    $waterIm = imagecreatefromjpeg($this->waterImage);
                    break;
                case 3:
                    $waterIm = imagecreatefrompng($this->waterImage);
                    break;
            }
        }

        //水印位置
        if($isWaterImage){
            $w = $waterW;
            $h = $waterH;
        }
        else{   //文字水印
            $temp = imagettfbbox(ceil($this->textFont * 5), 0, $this->fontTtf, $this->waterText);    //取得使用 TrueType 字体的文本的范围
            $w = $temp[2] - $temp[6];
            $h = $temp[3] - $temp[7];
            unset($temp);
        }

        if(($this->_imW < $w) || ($this->_imH < $h)){
			$this->_result['status'] = 0;
			$this->_result['error'] = 'SOURE_IMAGE_IS_TOO_SMALL';
			return $this->_result;
        }

        switch($this->waterPos){
            case 0:     //随机
                $posX = rand(0, ($this->_imW - $w));
                $posY = rand(0, ($this->_imH - $h));
                break;
            case 1:     //1为顶端居左
                $posX = 0;
                $posY = 0;
                break;
            case 2:     //2为顶端居中
                $posX = ($this->_imW - $w) / 2;
                $posY = 0;
                break;
            case 3:     //3为顶端居右
                $posX = $this->_imW - $w;
                $posY = 0;
                break;
            case 4:     //4为中部居左
                $posX = 0;
                $posY = ($this->_imH - $h) / 2;
                break;
            case 5:     //5为中部居中
                $posX = ($this->_imW - $w) / 2;
                $posY = ($this->_imH - $h) / 2;
                break;
            case 6:     //6为中部居右
                $posX = $this->_imW - $w;
                $posY = ($this->_imH - $h) / 2;
                break;
            case 7:     //7为底端居左
                $posX = 0;
                $posY = $this->_imH - $h;
                break;
            case 8:     //8为底端居中
                $posX = ($this->_imW - $w) / 2;
                $posY = $this->_imH - $h;
                break;
            case 9:     //9为底端居右
                $posX = $this->_imW - $w;
                $posY = $this->_imH - $h;
                break;
            default:        //随机
                $posX = rand(0, ($this->_imW - $w));
                $posY = rand(0, ($this->_imH - $h));
                break;
        }
        if($isWaterImage){      //图片水印
			  imagecopy($this->_tmpIm, $waterIm, $posX, $posY, 0, 0, $waterW, $waterH);       //拷贝水印到目标图片
        }
        else{      //文字水印
            if(!empty($this->textColor) && (strlen($this->textColor) == 7)){
                $R = hexdec(substr($this->textColor, 1, 2));
                $G = hexdec(substr($this->textColor, 3, 2));
                $B = hexdec(substr($this->textColor, 5));
            }
            else{
				$this->_result['status'] = 0;
				$this->_result['error'] = 'TEXT_COLOR_IS_WRONG';
				return $this->_result;
            }
            imagestring($this->_tmpIm, $this->textFont, $posX, $posY, $this->waterText, imagecolorallocate($this->_tmpIm, $R, $G, $B));
        }

        //释放内存
        if(isset($waterIm)){
            imagedestroy($waterIm);
        }
	}
}
/**
 * DEMO:
	$oImage = new Image();	//实例化对象
	$oImage->setSoureImage('lala.jpg');		//设置源图片
	
	//先设置的先执行
	$oImage->setScreenshot(0,0,300,300);		//设置截图参数 
	$oImage->setWatermark('water.png');		//设置打水印参数
	$oImage->setThumb(300,300);		//设置生成缩略图参数
	$oImage->save('djjo.jpg');		//保存
	$oImage->display();		//输出
	
	$oImage->setSoureImage('haha.jpg');		//再设置源图片2
	$oImage->setScreenshot(0,0,300,300);		//设置源图片2截图参数 
	$oImage->setWatermark('water.png');		//设置源图片2打水印参数
	$oImage->save('ok.jpg');		//保存
 */