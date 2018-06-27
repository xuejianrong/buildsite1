<?php
abstract class Verify{

    public static function display($cookieName = '', $length = '5', $width = '160', $height = '40'){
    	$code = self::_randomCode($length);
		Cookie::setEncrypt($cookieName, $code);
		
		
		self::_getAuthImage($code, $width, $height);
		
		/*
		$fontFilePath = SYSTEM_BASE_PATH . 'library/support/captcha.ttf';
        $fontSize = $height * 0.7;
        $image = imagecreate($width, $height);
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);
        $noiseColorDot = imagecolorallocate($image, 225, 225, 225);
        $noiseColorLine = imagecolorallocate($image, 204, 51, 51);
        for($i = 0; $i < ($width * $height) / 3; $i++){
			imagefilledellipse($image, mt_rand(0, $width), mt_rand(0, $height), 1, 1, $noiseColorDot);
		}
        for($i = 0; $i < ($width * $height) / 1000; $i++){
			imageline($image, mt_rand(0, $width), mt_rand(0, $height), mt_rand(0, $width), mt_rand(0, $height), $noiseColorLine);
		}
        $textBox = imagettfbbox($fontSize, 0, $fontFilePath , $code);
        $x = ($width - $textBox[4]) / 2;
        $y = ($height - $textBox[5]) / 2 + 2;
        imagettftext($image, $fontSize, 7, $x, $y, $textColor, $fontFilePath , $code);
        imagejpeg($image);
        imagedestroy($image);
		header('Content-Type: image/jpeg');
		*/
    }

	public static function match($cookieName, $value){
		if(strtolower(Cookie::getDecrypt($cookieName)) == strtolower($value)){
			return true;
		}else{
			return false;
		}
    }

	private static function _randomCode($length){
        $rndNums = '23456789abcdfhjknprstvxyz';
        $code = '';
        $i = 0;
        while($i < $length){
            $code .= substr($rndNums, mt_rand(0, strlen($rndNums) - 1), 1);
            $i++;
        }
        return $code;
    }
	
	
	private static function _getAuthImage($text, $width, $height){
		$image = imagecreatetruecolor($width, $height);
		$textColor = ImageColorAllocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
		$backgroundColor = ImageColorAllocate($image, 255, 255, 255);
		imagefill($image, 0, 0, $backgroundColor);
		$font = SYSTEM_BASE_PATH . 'library/support/t1.ttf';

		for ($i=0; $i<strlen($text); $i++){
			$tmp = substr($text, $i, 1);
			$array = array(-1, 1);
			$p = array_rand($array);
			$an = $array[$p] * mt_rand(1, 10);
			$size = 26;
			imagettftext($image, $size, $an, 0+$i*$size, 35, $textColor, $font, $tmp);
		}
		$verify = imagecreatetruecolor ($width, $height);
		imagefill($verify, 16, 13, $backgroundColor);
		for ( $i=0; $i<$width; $i++) {
			for ( $j=0; $j<$height; $j++) {
				$rgb = imagecolorat($image, $i , $j);
				if( (int)($i+20+sin($j/$height*2*M_PI)*10) <= imagesx($verify)&& (int)($i+20+sin($j/$height*2*M_PI)*10) >=0 ) {
					imagesetpixel ($verify, (int)($i+10+sin($j/$height*2*M_PI-M_PI*0.1)*4) , $j , $rgb);
				}
			}
		}
		
		
		//加入干扰象素	$count = 160;for($i=0; $i<$count; $i++){$randcolor = ImageColorallocate($verify,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));imagesetpixel($verify, mt_rand()%$width , mt_rand()%$height , $randcolor);}
		$rand = mt_rand(5,30);$rand1 = mt_rand(15,25);$rand2 = mt_rand(5,10);for ($yy=$rand; $yy<=+$rand+1; $yy++){for ($px=-80;$px<=80;$px=$px+0.1){$x=$px/$rand1;	if ($x!=0){$y=sin($x);}$py=$y*$rand2;imagesetpixel($verify, $px+80, $py+$yy, $textColor);}}
		
		Header("Content-type: image/JPEG");
		ImagePNG($verify);
		ImageDestroy($image);
		ImageDestroy($verify);
	}
}



