<?php
/* define('SMTP_ACCOUNT', '..........@qq.com');
define('SMTP_PASSWORD', '..........');
define('SMTP_HOST', 'smtp.qq.com');
define('SMTP_PORT', '25');
define('SMTP_TIME_OUT', '20'); */

//Sample: 
//print_r(Mail::send('zhuangshishang@foxmail.com', 'subject', '<h1>content</h1>', 'braveperson@qq.com,braveperson@foxmail.com', 'easonyeung@vip.qq.com'));


/*
	//Expected Sample:

	$data = array(
		'to' 		=>	'eason@qq.com',
		'subject' 	=>	'This is a great work!',
		'content' 	=>	'<h1>This is a great work! I will finish it do my best.</h1>',
		'cc' 		=>	'eason@qq.com,	braveperson@qq.com, 304042517@qq.com',
		'bcc' 		=>	'eason@qq.com,	braveperson@qq.com, 304042517@qq.com',
	);
	
	$result = email($data);
	
	if(isset($result['error'])){
		myLog($result['error']);
	}
*/



abstract class Mail{

	public static function send($to, $subject, $body, $cc = '', $bcc = ''){

		$return = true;
		$errorInfo = '';
		$eol = "\r\n";
		$headers = 'Content-Type: text/html; charset="utf-8"' . $eol . 'Content-Transfer-Encoding: base64';
		$aHeaders = explode($eol,$headers);
		if($body){
			$bodyContent = preg_replace("/^\./", "..", explode($eol, $body));
		}

		$aSmtp = array(
		   array('EHLO ' . 'UMFun' 				. $eol, '220,250', 	"HELO error: "),
		   array('AUTH LOGIN' 					. $eol, '334', 		"AUTH error:"),
		   array(base64_encode(SMTP_ACCOUNT) 	. $eol, '334', 		"AUTHENTIFICATION error : "),
		   array(base64_encode(SMTP_PASSWORD)	. $eol, '235', 		"AUTHENTIFICATION error : "),
		   array('MAIL FROM: <' . SMTP_ACCOUNT . '>'	. $eol,	'250',		"MAIL FROM error: "),
		);
		
		$SendArray=explode(',', $to);
		
		if($cc){
			$ccArray = explode(',', $cc);
			for($i = 0; $i < count($ccArray); $i++){
				$SendArray[]=$ccArray[$i];
			}
		}
		
		if($bcc){
			$bccArray = explode(',', $bcc);
			for($i = 0; $i < count($bccArray); $i++){
				$SendArray[]=$bccArray[$i];
			}
		}

		for($i=0; $i < count($SendArray); $i++){
			if(trim($SendArray[$i])){
				$aSmtp[] = array('RCPT TO: <' . $SendArray[$i] . '>' . $eol, '250', 'RCPT TO error: ');
			}
		}

		$aSmtp[] = array('DATA' . $eol, '354', 'DATA error: ');
		$aSmtp[] = array('From: ' . SMTP_ACCOUNT . $eol, '', '');
		$aSmtp[] = array('To: '. $to . $eol , '', '');
		if($cc){
			$aSmtp[] = array('Cc: ' . $cc . $eol, '', '');
		}
		if($bcc){
			$aSmtp[] = array('Bcc: ' . $bcc . $eol, '', '');
		}
		
		$aSmtp[] = array('Subject: ' . $subject . $eol, '', '');
		
		foreach($aHeaders as $header){
			$aSmtp[] = array($header . $eol, '', '');
		}
		
		$aSmtp[] = array($eol, '', '');
		
		if($bodyContent){
			foreach($bodyContent as $b){
				$aSmtp[] = array(base64_encode($b . $eol) . $eol, '', '');
			}
		}
		
		$aSmtp[] = array('.' . $eol, '250', 'DATA(end)error: ');
		$aSmtp[] = array('QUIT'. $eol, '221', 'QUIT error: ');

		$fsockHandle = fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, SMTP_TIME_OUT);
		if($fsockHandle){
			while($result = fgets($fsockHandle, 1024)){
				if(substr($result, 3, 1) == ' '){
					break; 
				}
			}
			
			foreach($aSmtp as $command){
				fputs($fsockHandle, $command[0]);
				if($command[1]){
				
					while($result = fgets($fsockHandle, 1024)){
						if(substr($result, 3, 1) == ' '){
							break;
						}
					};
					
					if(!strstr($command[1],substr($result, 0, 3))){
						//The codes below will recorde the detail process, when the response code is not an expected code, please don't delete them.
						//$errorInfo .= $command[2] . $result . $eol;
					}
				}
			}
			
			fclose($fsockHandle);
		}else{
			$errorInfo .= 'Send Mail Error: Cannot conect to Smtp host: ' . SMTP_HOST . $eol . $errno . $eol . $errstr . $eol;
		}
		
		if($errorInfo){
			$errorInfo .= 'Mail Data>>>>TO: ' . $to . $eol;
			$errorInfo .= 'Mail Data>>>>CC: ' . $cc . $eol;
			$errorInfo .= 'Mail Data>>>>BCC: ' . $bcc . $eol;
			$errorInfo .= 'Mail Data>>>>SUBJECT: ' . $subject . $eol;
			$errorInfo .= 'Mail Data>>>>BODY: ' . $body . $eol;
			$return = array();
			$return['error'] = $errorInfo;
		}

		return $return;
	}

}