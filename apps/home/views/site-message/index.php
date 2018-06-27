<?php
use umeworld\lib\Url;

$siteTitle = '在线留言';
$this->setTitle($siteTitle);

?>
<div class="banner_about">
     <div class="job_nav">
        <ul>
        <li><a href="<?php echo Url::to(Yii::$app->id, 'site/contactus'); ?>">联系我们</a></li>
        <li class="newshover"><a href="<?php echo Url::to(Yii::$app->id, 'site-message/index'); ?>" style="color:#005fd4;">在线留言</a></li>
        </ul>
        </div>
</div>
<div class="J-site-message-form massage">
   <p style="font-size:14px; width:800px; margin:20px auto; line-height:40px; border-bottom:1px solid #ccc; ">请在此留言我们会尽快与您联系。</p>
  <dl>
   <dt>姓名：</dt>
   <dd><input type="text" class="J-fb-contactName input"/>
   <span>* 请输入您的真实姓名</span>
   </dd>
   <dt>电话：</dt>
   <dd><input name="" type="text" class="J-fb-tel input"/>
   <span>* 请输入您的电话号码</span></dd>
   <dt>地址：</dt>
   <dd><input name="" type="text" class="J-fb-address input"/>
  <span>* 请输入您所在地</span></dd>
   <dt>邮箱：</dt>
   <dd><input name="" type="text" class="J-fb-email input"/>
   <span>* 请输入您的邮箱</span></dd>
   <dt>内容：</dt>
   <dd><textarea cols=""  class="J-fb-content input"></textarea><span>* 请输入您的留言内容</span></dd>
   <dt></dt>
   <dd><input type="button" value="  提 交  "  class="btn" onclick="addFeedback(this);" />    
     <input type="button" value="  重 填  " class="btn1" onclick="resetFillForm();" /></dd>
  </dl>
   <div class="clear"></div>
</div>
<script type="text/javascript">
	function resetFillForm(){
		$('.J-site-message-form input[type=text],.J-site-message-form textarea').val('');
	}
	
	function addFeedback(o){
		if($(o).attr('disabled')){
			return;
		}
		ajax({
			url : Tools.url('<?php echo Yii::$app->id; ?>', 'site-message/add'),
			data : {
				contactName : $('.J-fb-contactName').val(),
				tel : $('.J-fb-tel').val(),
				address : $('.J-fb-address').val(),
				email : $('.J-fb-email').val(),
				content : $('.J-fb-content').val()
			},
			beforeSend : function(){
				$(o).attr('disabled', 'disabled');
				Tools.showLoading();
			},
			complete : function(){
				$(o).attr('disabled', false);
				Tools.hideLoading();
			},
			error : function(aResult){
				UBox.show('出错啦！', 0);
			},
			success : function(aResult){
				if(aResult.status == 1){
					UBox.show(aResult.msg, aResult.status, function(){
						location.reload();
					}, 1);
				}else{
					UBox.show(aResult.msg, aResult.status);
				}
			}
		});
	}
</script>