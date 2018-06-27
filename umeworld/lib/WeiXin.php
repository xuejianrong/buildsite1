<?php

namespace umeworld\lib;

use Yii;
use umeworld\lib\Cookie;
use umeworld\lib\Xxtea;
use common\model\Redis;

/*
 * 微信功能，只适用于微信客户端的操作
 * author twl
 */

class WeiXin extends \yii\base\Object{

	/**
	 * AppID(应用ID)
	 */
	public $appId = '';

	/**
	 * AppSecret(应用密钥)
	 */
	public $appSecret = '';
	
	/*
	 * 组装url访问后可以 获取用户微信信息
	 */
	public function getWebPassUrl($url, $userId, $role, $openid){
		$redirectUri = Yii::$app->urlManagerLogin->createUrl(['weixin/transfer-station', 'user_id' => $userId, 'role' => $role, 'url' => $url, 'openid' => $openid]);
		$weixinUrl = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $this->appId . '&redirect_uri=' . urlencode($redirectUri) . '&response_type=code&scope=snsapi_userinfo&state=uexiao#wechat_redirect';
		if($url){
			return $weixinUrl;
		}
		return '';
	}
	
	/*
	 * 请访问http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html 教程
	 * 
	 * $isBasic 是否只要subscribe之前的参数 $onlyOpenid = 是否只拿微信openid
	 * 
	 * https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx814b4f87a66a4e41&redirect_uri=重定向网址&response_type=code&scope=snsapi_userinfo&state=umantang#wechat_redirect
	 * 
	 * return [
			openid			用户的唯一标识
			nickname		用户昵称
			sex				用户的性别，值为1时是男性，值为2时是女性，值为0时是未知
			province		用户个人资料填写的省份
			city			普通用户个人资料填写的城市
			country			国家，如中国为CN
			headimgurl		用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。
			privilege		用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）
			unionid			只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。详见：获取用户个人信息（UnionID机制）
	
			当$isBasic => false 会追加以下
	
			subscribe		用户是否订阅该公众号标识，值为0时，代表此用户没有关注该公众号，拉取不到其余信息。
			subscribe_time	用户关注时间，为时间戳。如果用户曾多次关注，则取最后关注时间
			remark			公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注
			groupid			用户所在的分组ID
	 * ]
	 * Yii::$app->weiXin->userInfo
	 */
	public function getUserInfo($isBasic = true, $onlyOpenid = 'false'){
		if(!YII_ENV_PROD){
			return ['openid' => 'oCckMw9il94UirsqOsSEfuXVIG4E'];//!!!!!!!!!!!!!!!!!!!!!!线下测试用的，上线后修复
		}
		$code = Yii::$app->request->get('code');
		//设置cookie名称
		$accessTokenName = Xxtea::xcrypt('wx_access_token');
		$refreshTokenName = Xxtea::xcrypt('wx_refresh_token');
		$openIdName = Xxtea::xcrypt('wx_openid');
		//$state = Yii::$app->request->get('state');
		//此处代码是用来预防code失效还被传过来报错-----start
		$codeOk = true;
		if(!Cookie::getDecrypt($accessTokenName) || !Cookie::getDecrypt($refreshTokenName) || !Cookie::getDecrypt($openIdName)){
			$codeOk = false;
		}
		//此处代码是用来预防code失效还被传过来报错-----end
		if(!empty($code)){//!$codeOk && 
			//Yii::info('通过code参数进来,$code=' . $code);
			$tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code';
			$aResult = $this->_httpGet($tokenUrl);
			if(isset($aResult['errcode'])){
				Yii::error('"errcode": ' . $aResult['errcode'] . ' ,"errmsg": ' . $aResult['errmsg']);
				return false;
			}
			//保证access_token有效性，无效就重新请求, 
			$aToken = $this->_outmodedAccessToken($aResult['access_token'], $aResult['openid'], $aResult['refresh_token']);
			if(!$aToken){
				return false;
			}
			$aResult['access_token'] = $aToken['access_token'];
			$aResult['refresh_token'] = $aToken['refresh_token'];
			
			//存相关数据到cookie
			Cookie::setEncrypt($accessTokenName, $aResult['access_token']);
			Cookie::setEncrypt($refreshTokenName, $aResult['refresh_token']);
			Cookie::setEncrypt($openIdName, $aResult['openid']);
		}else{
			//Yii::info('不是通过code参数进来');
			//throw Yii::$app->buildError('授权失败');
			//Yii::error('授权失败');
			$accessToken = Cookie::getDecrypt($accessTokenName);
			$refreshToken = Cookie::getDecrypt($refreshTokenName);
			$openId = Cookie::getDecrypt($openIdName);
			if(!$accessToken || !$refreshToken || !$openId){
				Yii::info('不是从参数$code进来 获取变量的 $accessToken,$refreshToken,$openId 都为空 ');
				return false;
			}
			//Yii::info('$accessToken = ' . $accessToken . ',$refreshToken = ' . $refreshToken . ',$openId = ' . $openId);
			//保证access_token有效性，无效就重新请求, 
			$aToken = $this->_outmodedAccessToken($accessToken, $openId, $refreshToken);
			if(!$aToken){
				return false;
			}	
			$aResult = [
				'access_token' => $aToken['access_token'],
				'refresh_token' => $aToken['refresh_token'],
				'openid' => $openId,
			];
		}
		if($onlyOpenid){
			return ['openid' => $aResult['openid']];
		}
		$basicUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $aResult['access_token'] . '&openid=' . $aResult['openid'] . '&lang=zh_CN';
		$aUserInfo = $this->_httpGet($basicUrl);
		if(isset($aUserInfo['errcode'])){
			Yii::error('"errcode": ' . $aUserInfo['errcode'] . ' ,"errmsg": ' . $aUserInfo['errmsg']);
			//return $aResult;
			return false;
		}
		if($isBasic){
			return $aUserInfo;
		}
		
		//获取获取用户是否关注 的信息
		$codeName = 'access_token';
		$accessToken = $this->_getMsnVerifyCodeFromRedis($codeName);
		if(!$accessToken){
			$accessTokenBasicUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret;
			//先获取access_token这个和上面的不一样，有效时间为2小时
			$aAccessData = $this->_httpGet($accessTokenBasicUrl);
			if(isset($aAccessData['errcode'])){
				Yii::error('"errcode": ' . $aAccessData['errcode'] . ' ,"errmsg": ' . $aAccessData['errmsg']);
				return false;
			}
			$this->_saveMsnVerifyCodeToRedis($codeName, $aAccessData['access_token']);
			$accessToken = $aAccessData['access_token'];
		}
		
		$subscribeUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $aResult['openid'] . '&lang=zh_CN';
		$aSubscribe = $this->_httpGet($subscribeUrl);
		if(isset($aSubscribe['errcode'])){
			Yii::error('"errcode": ' . $aSubscribe['errcode'] . ' ,"errmsg": ' . $aSubscribe['errmsg']);
			//return $aResult;
			return false;
		}
		/*$aUserInfo['subscribe'] = $aSubscribe['subscribe'];
		$aUserInfo['subscribe_time'] = $aSubscribe['subscribe_time'];
		$aUserInfo['remark'] = $aSubscribe['remark'];
		$aUserInfo['groupid'] = $aSubscribe['groupid'];*/
		return array_merge($aUserInfo, $aSubscribe);
	}

	/*
	 * 微信JSDDK信息获取， Yii::$app->weiXin->jSDDKInfo
	 */
	public function getJSDDKInfo(){
		//先获取access_token，时效是7200秒
		$codeName = 'access_token';
		$accessToken = $this->_getMsnVerifyCodeFromRedis($codeName);
		if(!$accessToken){
			$accessTokenBasicUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret;
			//先获取access_token这个和上面的不一样，有效时间为2小时
			$aAccessData = $this->_httpGet($accessTokenBasicUrl);
			if(isset($aAccessData['errcode'])){
				Yii::error('"errcode": ' . $aAccessData['errcode'] . ' ,"errmsg": ' . $aAccessData['errmsg']);
				return false;
			}
			$this->_saveMsnVerifyCodeToRedis($codeName, $aAccessData['access_token']);
			$accessToken = $aAccessData['access_token'];
		}
		
		//通过access_token获得jsapi_ticket，时效是7200秒
		$jsapiTicketName = 'jsapi_ticket';
		$jsapiTicket = $this->_getMsnVerifyCodeFromRedis($jsapiTicketName);
		if(!$jsapiTicket){
			$jsapiTicketBasicUrl = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=' . $accessToken;
			//先获取access_token这个和上面的不一样，有效时间为2小时
			$aJsapiTicketData = $this->_httpGet($jsapiTicketBasicUrl);
			if(isset($aJsapiTicketData['errcode']) && $aJsapiTicketData['errcode']){
				Yii::error('"errcode": ' . $aJsapiTicketData['errcode'] . ' ,"errmsg": ' . $aJsapiTicketData['errmsg']);
				return false;
			}
			$this->_saveMsnVerifyCodeToRedis($jsapiTicketName, $aJsapiTicketData['ticket']);
			$jsapiTicket = $aJsapiTicketData['ticket'];
		}
		//https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken;

		//组织url
		//注意URL一定要动态获取，不能 hardcode.
		//$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
		//$protocol = (Yii::$app->request->isSecureConnection || Yii::$app->request->serverPort == 443) ? 'https://' : 'http://';
		//$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$url = Yii::$app->request->hostInfo . Yii::$app->request->url;//pathInfo
		//时间戳
		$timestamp = time();
		$nonceStr = $this->_createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = 'jsapi_ticket=' . $jsapiTicket . '&noncestr=' . $nonceStr . '&timestamp=' . $timestamp . '&url=' . $url;

		$signature = sha1($string);

		$signPackage = [
		  'appId'     => $this->appId,
		  'nonceStr'  => $nonceStr,
		  'timestamp' => $timestamp,
		  'url'       => $url,
		  'signature' => $signature,
		  'rawString' => $string
		];
		return $signPackage; 
	}
	
	/*
	 * 主动推送信息到指定用户,注意，只有关注公众号的用户才能接收到信息, 可以 Yii::$app->weiXin->getUserInfo(false) 获取用户信息 来判断是否关注
	 * $aUserSns = [
	 *		user_open_id => 用户的微信openid
	 *		title => 标题
	 *		content => 要发送的内容
	 *		url => 要跳转的url 比如 h5t.uexiao.dev
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * return [
	 *		'errcode' => 结果状态码 0表示成功,非0表示其他错误
	 *		'errmsg' => 错误提示语
	 * ]
	 * Yii::$app->weiXin->sendSnsToUser();
	 */
	public function sendSnsToUser($aUserSns){
		//先获取access_token，时效是7200秒
		$codeName = 'access_token';
		$accessToken = $this->_getMsnVerifyCodeFromRedis($codeName);
		if(!$accessToken){
			$accessTokenBasicUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret;
			//先获取access_token这个和上面的不一样，有效时间为2小时
			$aAccessData = $this->_httpGet($accessTokenBasicUrl);
			if(isset($aAccessData['errcode'])){
				Yii::error('"errcode": ' . $aAccessData['errcode'] . ' ,"errmsg": ' . $aAccessData['errmsg']);
				return $aAccessData;//false;
			}
			$this->_saveMsnVerifyCodeToRedis($codeName, $aAccessData['access_token']);
			$accessToken = $aAccessData['access_token'];
		}
		//组装微信参数信息
		$wxUrl = $this->getWebPassUrl($aUserSns['url'], $aUserSns['user_info']['id'], $aUserSns['user_info']['role'], $aUserSns['user_open_id']);
		$str = '{"touser": "' . $aUserSns['user_open_id'] . '", "msgtype": "news", "news": {"articles": [{"title": "' . $aUserSns['title'] . '", "description": "' . $aUserSns['content'] . '", "url": "' . $wxUrl . '", "picurl": ""}]}}';
		$sendSnsUrl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . $accessToken;
		$aResult = $this->_httpPost($sendSnsUrl, $str);
		return $aResult;
		/*if(isset($aResult['errcode']) && $aResult['errcode']){
			Yii::error('"errcode": ' . $aResult['errcode'] . ' ,"errmsg": ' . $aResult['errmsg']);
			//return $aResult;
			return false;
		}
		return $aResult;
		*/
	}
	
	/*
	 * 主动推送模板信息到指定用户,注意，只有关注公众号的用户才能接收到信息, 可以 Yii::$app->weiXin->getUserInfo(false) 获取用户信息 来判断是否关注
	 * 【用户咨询提醒】的参数  $aUserSns = [
	 *		type			=> 消息类型， 值为 1,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语，如: Xxx老师您好，你有一条用户咨询待解决
	 *		name			=> 用户名称
	 *		content			=> 咨询内容
	 *		remark			=> 结束语，如: 点击了解详情
	 *		url				=> 要跳转的相关url 比如 h5t.umantang.dev/xxx/xx.html
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 【新订单通知】提醒   $aUserSns = [
	 *		type			=> 消息类型， 值为 2,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：您有一个新订单
	 *		name			=> 学员姓名
	 *		course			=> 课程名称
	 *		money			=> 订单金额
	 *		status			=> 订单状态,未支付，已支付 可以自定义
	 *		create_time		=> 订单时间, 如 2014-12-11 12:00:00
	 *		remark			=> 结束语，如:请及时确认
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 【用户订单变更状态提醒】提醒 $aUserSns = [ //进行中、已结束、投诉中、退款中、已退款变更
	 *		type			=> 消息类型， 值为 4,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：您有一个订单状态发生变更
	 *		content			=> 订单内容
	 *		order_num		=> 订单编号
	 *		status			=> 订单状态,未支付，已支付 可以自定义
	 *		time			=> 订单服务时间
	 *		remark			=> 结束语，如: 点击了解详情
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * 【订单评价提醒】 $aUserSns = [ //新评价、评价回复 采用订单评价提醒模板
	 *		type			=> 消息类型， 值为 5,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：Xxx老师您好，收到一条用户评价
	 *		order_num		=> 订单编号
	 *		time			=> 订单服务时间
	 *		remark			=> 结束语，如: 点击了解详情
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * 【课程变更通知】 $aUserSns = [ //附加业务审核结果，采用课程变更通知模板
	 *		type			=> 消息类型， 值为 6,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：Xxx老师您好，您提交的附加业务审核已有结果
	 *		class_name		=> 课程名称
	 *		status			=> 变更事项
	 *		remark			=> 结束语，如: 点击了解详情
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * 【报告生成通知】 $aUserSns = [ //收到教师报告，采用报告生成通知模板
	 *		type			=> 消息类型， 值为 7,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：Xxx老师您好，您提交的附加业务审核已有结果
	 *		type_name		=> 报告类型,如 :每日报告
	 *		create_time		=> 生成时间
	 *		remark			=> 结束语，如: 点击了解详情
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * 【投诉处理结果通知】 $aUserSns = [
	 *		type			=> 消息类型， 值为 8,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：Xxx老师您好，您提交的附加业务审核已有结果
	 *		content			=> 投诉内容
	 *		result			=> 处理结果
	 *		remark			=> 结束语，如: 点击了解详情
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * 【商家回复提醒】 $aUserSns = [
	 *		type			=> 消息类型， 值为 9,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：Xxx您好，您的咨询/评价有了新回复
	 *		name			=> 商家名称
	 *		content			=> 回复内容
	 *		create_time		=> 回复时间
	 *		remark			=> 结束语，如: 点击了解详情
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * 【课程即将到期提醒】 $aUserSns = [
	 *		type			=> 消息类型， 值为 10,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：Xxx家长您好，您有一项服务内容即将到期
	 *		student_name	=> 学生姓名
	 *		class_name		=> 课程名称
	 *		only_time		=> 剩余课时
	 *		remark			=> 结束语，如: 为避免服务中断，请提前订购新的服务，感谢您对优满堂的支持。
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * 【教师通知】 $aUserSns = [
	 *		type			=> 消息类型， 值为 11,
	 *		user_open_id	=> 被通知用户的微信openid
	 *		tis				=> 提示语, 如：Xxx家长您好，收到一条教师通知
	 *		inform			=> 通知类型
	 *		content			=> 内容
	 *		remark			=> 结束语，如: 点击查看详情和照片
	 *		url				=> 要跳转相关的url 比如 h5t.uexiao.dev/xxx-xxx.hmtl
	 *		user_info		=> [
	 *			id => 用户id， 这里的用户是指被通知用户的id
	 *			role => 角色， 这里的角色是指被通知用户的角色，有 teacher，parent
	 *		]
	 * ]
	 * 
	 * return [
	 *		'errcode' => 结果状态码 0表示成功,非0表示其他错误
	 *		'errmsg' => 错误提示语
	 * ]
	 * Yii::$app->weiXin->sendTemplateSns();
	 */
	public function sendTemplateSns($aUserSns){
		if(!YII_ENV_PROD){
			return false;
		}
		//先获取access_token，时效是7200秒
		$codeName = 'access_token';
		$accessToken = $this->_getMsnVerifyCodeFromRedis($codeName);
		if(!$accessToken){
			$accessTokenBasicUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret;
			//先获取access_token这个和上面的不一样，有效时间为2小时
			$aAccessData = $this->_httpGet($accessTokenBasicUrl);
			if(isset($aAccessData['errcode'])){
				Yii::error('"errcode": ' . $aAccessData['errcode'] . ' ,"errmsg": ' . $aAccessData['errmsg']);
				return $aAccessData;//false;
			}
			$this->_saveMsnVerifyCodeToRedis($codeName, $aAccessData['access_token']);
			$accessToken = $aAccessData['access_token'];
		}
		//组装微信url
		$wxUrl = $this->getWebPassUrl($aUserSns['url'], $aUserSns['user_info']['id'], $aUserSns['user_info']['role'], $aUserSns['user_open_id']);
		$aParams = [
			'touser' => $aUserSns['user_open_id'],
			'template_id' => '',
			'url' => $wxUrl,//$aUserSns['url'],            
			'data' => []
		];
		$aGetSmsData = $this->_getSmsData($aUserSns);
		$aParams['template_id'] = $aGetSmsData['template_id'];
		$aParams['data'] = $aGetSmsData['data'];
		$url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=' . $accessToken;
		$aResult = $this->_httpPost($url, json_encode($aParams));
		if(isset($aResult['errcode']) && $aResult['errcode'] != 0){
			Yii::error('"errcode": ' . $aResult['errcode'] . ' ,"errmsg": ' . $aResult['errmsg'] . '用户微信openid为:' . $aUserSns['user_open_id']);
		}
		return $aResult;
	}
	
	//post请求
	private function _httpPost($url, $xParams){
//		$oHttp = new Http($url);
//		$oHttp->setAcceptType(Http::CONTENT_TYPE_JSON);
//		return $oHttp->post($aParams);
		$oCurl = curl_init();
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($oCurl, CURLOPT_POST, 1);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS, $xParams);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($oCurl);
		if(!$result){
			throw new \Exception(curl_errno($oCurl));
		}
		curl_close($oCurl);
//		$aReturlData = json_decode($result, true);
//		if(json_last_error()){
//			throw Yii::$app->buildError('解析json失败', false, $result);
//		}
//		return $aReturlData;
	}
	
	//随机字符串
	private function _createNonceStr($length = 16){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$str = '';
		for($i = 0; $i < $length; $i++){
		  $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	
	/*
	 * 保证access_token有效性，无效就重新请求, 
	 * @param $accessToken  网页授权接口调用凭证
	 * @param $openid  用户的唯一标识
	 */
	private function _outmodedAccessToken($accessToken, $openid, $refreshToken){
		$url = 'https://api.weixin.qq.com/sns/auth?access_token=' . $accessToken . '&openid=' . $openid;
		$aResult = $this->_httpGet($url);
		if($aResult['errcode'] == 0){
			return [
				'access_token' => $accessToken,
				'refresh_token' => $refreshToken,
			];
		}
		//如果失效就重新请求access_token
		$url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $this->appId . '&grant_type=refresh_token&refresh_token=' . $refreshToken;
		$aResult = $this->_httpGet($url);
		if(isset($aResult['errcode'])){
			Yii::error('"errcode": ' . $aResult['errcode'] . ' ,"errmsg": ' . $aResult['errmsg']);
			return [];
		}
		return [
			'access_token' => $aResult['access_token'],
			'refresh_token' => $aResult['refresh_token'],
		];
	}

	//gei请求
	private function _httpGet($url){
		$oHttp = new Http($url);
		$oHttp->setAcceptType(Http::CONTENT_TYPE_JSON);
		return $oHttp->get();
	}
	
	/**
	 * 组装redis的键
	 * @author jay
	 * @return string
	 */
	private function _getRedisKeyString($name){
		return 'umantang_weixin_code_' . $name;
	}

	/**
	 * 保存access_token到redis
	 * @author jay
	 * @param $name access_token名字
	 * @param $code 验证码
	 * @return null
	 */
	private function _saveMsnVerifyCodeToRedis($name, $code){
		$expireTime = 3600;
		$key = $this->_getRedisKeyString($name);
		$aResult = Yii::$app->redisCache->getOne($key);
		$aParam = [
			'code' => $code,
			'create_time' => NOW_TIME,
		];
		if(!$aResult){
			Yii::$app->redisCache->add($key, $aParam);
		}else{
			Yii::$app->redisCache->update($key, $aParam);
		}
		Yii::$app->redisCache->expireOne($key, $expireTime);
	}

	/**
	 * 从redis获取access_token
	 * @author jay
	 * @param $name key
	 */
	private function _getMsnVerifyCodeFromRedis($name){
		$key = $this->_getRedisKeyString($name);
		$aResult = Yii::$app->redisCache->getOne($key);
		if(isset($aResult['code']) && $aResult['code']){
			return $aResult['code'];
		}

		return '';
	}

	/**
	 * 从redis获取access_token创建时间
	 * @author jay
	 * @param $name key
	 */
	private function _getMsnVerifyCodeSaveTimeFromRedis($name){
		$key = $this->_getRedisKeyString($name);
		$aResult = Yii::$app->redisCache->getOne($key);
		if(isset($aResult['create_time']) && $aResult['create_time']){
			return $aResult['create_time'];
		}

		return '';
	}
	
	//测试组件
	public function getTestUserInfo(){
		$code = Yii::$app->request->get('code');
		//设置cookie名称
		$accessTokenName = Xxtea::xcrypt('wx_access_token');
		$refreshTokenName = Xxtea::xcrypt('wx_refresh_token');
		$openIdName = Xxtea::xcrypt('wx_openid');
		//$state = Yii::$app->request->get('state');
		//此处代码是用来预防code失效还被传过来报错-----start
		$codeOk = true;
		if(!Cookie::getDecrypt($accessTokenName) || !Cookie::getDecrypt($refreshTokenName) || !Cookie::getDecrypt($openIdName)){
			$codeOk = false;
		}
		//此处代码是用来预防code失效还被传过来报错-----end
		if(!empty($code)){//!$codeOk && 
			$tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code';
			$aResult = $this->_httpGet($tokenUrl);
			if(isset($aResult['errcode'])){
				Yii::error('"errcode": ' . $aResult['errcode'] . ' ,"errmsg": ' . $aResult['errmsg']);
				return '目前返回的是第1个return,提示错误信息是' . ' "errcode": ' . $aResult['errcode'] . ' ,"errmsg": ' . $aResult['errmsg'];
			}
			//保证access_token有效性，无效就重新请求, 
			$aToken = $this->_outmodedAccessToken($aResult['access_token'], $aResult['openid'], $aResult['refresh_token']);
			if(!$aToken){
				return '目前返回的是第2个return,提示错误信息是 通过微笑服务器得到相关信息后 在获取 access_token 时 失败';
			}
			$aResult['access_token'] = $aToken['access_token'];
			$aResult['refresh_token'] = $aToken['refresh_token'];
			
			//存相关数据到cookie
			Cookie::setEncrypt($accessTokenName, $aResult['access_token']);
			Cookie::setEncrypt($refreshTokenName, $aResult['refresh_token']);
			Cookie::setEncrypt($openIdName, $aResult['openid']);
		}else{
			//throw Yii::$app->buildError('授权失败');
			//Yii::error('授权失败');
			$accessToken = Cookie::getDecrypt($accessTokenName);
			$refreshToken = Cookie::getDecrypt($refreshTokenName);
			$openId = Cookie::getDecrypt($openIdName);
			if(!$accessToken || !$refreshToken || !$openId){
				return '目前返回的是第3个return,提示错误信息是 从cookie中获取$accessToken = '. $accessToken . '，$refreshToken = '. $refreshToken . '和$openId = '. $openId . '失败';
			}
			//保证access_token有效性，无效就重新请求, 
			$aToken = $this->_outmodedAccessToken($accessToken, $openId, $refreshToken);
//			if(!$aToken){
//				//不排除退出关注后，refreshToken虽然还存在cookie，其实已经失效了
//				$tokenUrl = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $this->appId . '&secret=' . $this->appSecret . '&code=' . $code . '&grant_type=authorization_code';
//				$aResult = $this->_httpGet($tokenUrl);
//				debug($tokenUrl);
//				debug($aResult);
//				if(!isset($aResult['errcode'])){
//					//保证access_token有效性，无效就重新请求, 
//					$aToken = $this->_outmodedAccessToken($aResult['access_token'], $aResult['openid'], $aResult['refresh_token']);
//				}
//			}
			if(!$aToken){
				return '目前返回的是第4个return,提示错误信息是 通过cookie得到相关信息后 在获取 access_token 时 失败$aToken=' . json_encode($aToken);
			}	
			$aResult = [
				'access_token' => $aToken['access_token'],
				'refresh_token' => $aToken['refresh_token'],
				'openid' => $openId,
			];
		}
		
		$basicUrl = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $aResult['access_token'] . '&openid=' . $aResult['openid'] . '&lang=zh_CN';
		$aUserInfo = $this->_httpGet($basicUrl);
		if(isset($aUserInfo['errcode'])){
			Yii::error('"errcode": ' . $aUserInfo['errcode'] . ' ,"errmsg": ' . $aUserInfo['errmsg']);
			//return $aResult;
			return '目前返回的是第5个return,提示错误信息是 获取 用户信息失败';
		}
		
		//获取获取用户是否关注 的信息
		$codeName = 'access_token';
		$accessToken = $this->_getMsnVerifyCodeFromRedis($codeName);
		if(!$accessToken){
			$accessTokenBasicUrl = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $this->appId . '&secret=' . $this->appSecret;
			//先获取access_token这个和上面的不一样，有效时间为2小时
			$aAccessData = $this->_httpGet($accessTokenBasicUrl);
			if(isset($aAccessData['errcode'])){
				Yii::error('"errcode": ' . $aAccessData['errcode'] . ' ,"errmsg": ' . $aAccessData['errmsg']);
				return '目前返回的是第6个return,提示错误信息是 获取access_token失败';
			}
			$this->_saveMsnVerifyCodeToRedis($codeName, $aAccessData['access_token']);
			$accessToken = $aAccessData['access_token'];
		}
		
		$subscribeUrl = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . $accessToken . '&openid=' . $aResult['openid'] . '&lang=zh_CN';
		$aSubscribe = $this->_httpGet($subscribeUrl);
		if(isset($aSubscribe['errcode'])){
			Yii::error('"errcode": ' . $aSubscribe['errcode'] . ' ,"errmsg": ' . $aSubscribe['errmsg']);
			//return $aResult;
			return '目前返回的是第7个return,提示错误信息是 获取详细用户信息失败，$aUserInfo=' . json_encode($aUserInfo);
		}
		/*$aUserInfo['subscribe'] = $aSubscribe['subscribe'];
		$aUserInfo['subscribe_time'] = $aSubscribe['subscribe_time'];
		$aUserInfo['remark'] = $aSubscribe['remark'];
		$aUserInfo['groupid'] = $aSubscribe['groupid'];*/
		return array_merge($aUserInfo, $aSubscribe);
	}
	
	private function _getSmsData($aUserSns){
		$templateId = '';
		if($aUserSns['type'] == 1){
			$templateId = 'Ap8vn_tAkBPx7qkegsGagwrbhPsyL4Z51yzB9CMKzok';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['name'],//用户名称
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['content'],//咨询内容
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => isset($aUserSns['remark']) ? $aUserSns['remark'] : '点击处理咨询',
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 2){
			$templateId = 'dfiqaTudIIClrIxuvhoitjKCRdcatxK8Y7tZQ8nZdK4';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['name'],//学员姓名
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['course'],//课程名称
					'color' => '#3C3C3C'
				],
				'keyword3' => [
					'value' => $aUserSns['money'],//订单金额,
					'color' => '#3C3C3C'
				],
				'keyword4' => [
					'value' => $aUserSns['status'],//订单状态,
					'color' => '#3C3C3C'
				],
				'keyword5' => [
					'value' => $aUserSns['create_time'],//订单时间,
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//'请尽快查看处理。',
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 3){
			$templateId = 'TLaQgVJi79PC5TFVFeIYzS3wFfCmmIuddJZs5PJECTE';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['content'],//订单内容
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['order_num'],//订单编号
					'color' => '#3C3C3C'
				],
				'keyword3' => [
					'value' => $aUserSns['status'],//订单状态
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 4){
			$templateId = 'q_rbTTgz4OgQWN87DFZ-u_EnyOQILwPNHKGoNwu_rmE';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['content'],//订单内容
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['order_num'],//订单编号
					'color' => '#3C3C3C'
				],
				'keyword3' => [
					'value' => $aUserSns['status'],//订单状态
					'color' => '#3C3C3C'
				],
				'keyword4' => [
					'value' => $aUserSns['time'],//服务时间
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 5){
			$templateId = 'OqGUxJEPCzLksOADNiaqApAm0qOxmazrVJJKTMT2-rI';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['order_num'],//订单编号
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['time'],//服务时间
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 6){
			$templateId = '3uaZvDE0z8pOH-KYtbccHURUHD1poUh81f-1PY0WOZo';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['class_name'],//课程名称
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['status'],//变更事项
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 7){
			$templateId = 'BYDMm-HELcunHnSinVPDIVazNM5et9Mt0Q36_o3GDfo';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['type_name'],//报告类型
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['create_time'],//生成时间
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 8){
			$templateId = '6IgRGYq2SUUjY_XlrA27b-lepS09Ploq9-E5vaCgHVQ';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['content'],//投诉内容
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['result'],//处理结果
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 9){
			$templateId = 'hOKLEgEZJY3MxdSD4Q4OuBGHFC4BiGeq5TDTqj_iN18';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['name'],//商家名称
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['content'],//内容
					'color' => '#3C3C3C'
				],
				'keyword3' => [
					'value' => $aUserSns['create_time'],//回复时间
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 10){
			$templateId = '9jLgY1tcxmQzp2ZaEjsQSYyTC_y4hpGChI882qO-Rww';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['student_name'],//学生姓名
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['class_name'],//课程名称
					'color' => '#3C3C3C'
				],
				'keyword3' => [
					'value' => $aUserSns['only_time'],//剩余课时
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}elseif($aUserSns['type'] == 11){
			$templateId = '9jLgY1tcxmQzp2ZaEjsQSYyTC_y4hpGChI882qO-Rww';
			$aData = [
				'first' => [
					'value' => $aUserSns['tis'],//提示语
					'color' => '#3C3C3C'
				],
				'keyword1' => [
					'value' => $aUserSns['inform_type'],//通知类型
					'color' => '#3C3C3C'
				],
				'keyword2' => [
					'value' => $aUserSns['content'],//内容
					'color' => '#3C3C3C'
				],
				'remark' => [
					'value' => $aUserSns['remark'],//结束语
					'color' => '#3C3C3C'
				]
			];
		}
		if(!$templateId){
			Yii::error('$templateId变量为空');
		}
		return [
			'template_id' => $templateId,
			'data' => $aData,
		];
	}
}
