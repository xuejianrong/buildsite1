<?php
use umeworld\lib\Url;
$this->setTitle('账号登陆');
?>
<fieldset>
	<label class="block clearfix">
		<span class="block input-icon input-icon-right">
			<div class="form-group field-adminform-username required">
				<input type="text" id="adminform-username" class="form-control" placeholder="管理员账号">
			</div>
			<i class="ace-icon fa fa-user"></i>
		</span>
	</label>

	<label class="block clearfix">
		<span class="block input-icon input-icon-right">
			<div class="form-group field-adminform-password required">
				<input type="password" id="adminform-password" class="form-control" placeholder="管理员密码">
			</div>
			<i class="ace-icon fa fa-lock"></i>
		</span>
	</label>
	<label class="block clearfix">
		<span class="block input-icon input-icon-right">
			<div class="form-group">
				<img id="verifyImg" class="pull-left" style="position: relative; top: 0px;width: 120px; height: 34px;cursor:pointer;" src="<?php echo Url::to(Yii::$app->id, 'site/captcha') . '?v=' . NOW_TIME; ?>" alt="" onclick="refreshCaptcha(this);">
				<input class="form-control" type="text" id="verifycode" name="verifycode" style="width:170px;height:34px;position: relative;top: 0px;right: -10px;" placeholder="验证码" />
			</div>
		</span>
	</label>
	<div class="clearfix">
		<label class="inline">
			<div class="form-group field-adminform-rememberme">
				<div class="checkbox">
					<label for="adminform-rememberme">
						<input type="checkbox" id="adminform-rememberme" value="1" checked="">记住登录
					</label>
					<p class="help-block help-block-error"></p>
				</div>
			</div>
		</label>
		<button class="J-login-btn btn bg-olive btn-block width-35 pull-right btn btn-sm btn-primary" onclick="doLogin(this);">登录</button>
	</div>
	<div class="space-4"></div>
</fieldset>

<script type="text/javascript">
	function refreshCaptcha(o){
		$(o).attr('src', Tools.url('manage', 'site/captcha') + '?v=' + (parseInt(Date.parse(new Date())) + parseInt(Math.random(1000, 9999) * 1000)));
	}
	
	function doLogin(o){
		if($(o).attr('disabled')){
			return;
		}
		ajax({
			url : Tools.url('manage', 'site/login'),
			data : {
				account : $('#adminform-username').val(),
				password : $('#adminform-password').val(),
				captcha : $('#verifycode').val(),
				rememberme : $('#adminform-rememberme').is(':checked') ? 1 : 0
			},
			beforeSend : function(){
				$(o).attr('disabled', 'disabled');
			},
			complete : function(){
				$(o).attr('disabled', false);
			},
			error : function(aResult){
				UBox.show('出错啦！', 0);
			},
			success : function(aResult){
				if(aResult.status == 1){
					UBox.show(aResult.msg, aResult.status, function(){
						location.href = aResult.data;
					}, 1);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
	
	
	$(function(){
		$('input').keyup(function(e){
			if(e.keyCode == 13){
				$('.J-login-btn').click();	
			}
		});
	});
</script>