<?php
use umeworld\lib\Url;
$this->registerAssetBundle('common\assets\FileAsset');

$siteTitle = '联系我们';
$this->setTitle($siteTitle);
?>
<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	
	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
				<label>公司名称</label>
				<input class="J-companyName form-control" placeholder="" value="<?php echo $aSetting['companyName']; ?>" />
			</div>
			<div class="form-group">
				<label>电话</label>
				<input class="J-phone form-control" placeholder="" value="<?php echo $aSetting['phone']; ?>" />
			</div>
			<div class="form-group">
				<label>服务热线</label>
				<input class="J-hotsPhone form-control" placeholder="" value="<?php echo $aSetting['hotsPhone']; ?>" />
			</div>
			<div class="form-group">
				<label>售后服务电话</label>
				<input class="J-servicePhone form-control" placeholder="" value="<?php echo $aSetting['servicePhone']; ?>" />
			</div>
			<div class="form-group" style="display:none;">
				<label>手机</label>
				<input class="J-mobile form-control" placeholder="" value="<?php echo $aSetting['mobile']; ?>" />
			</div>
			<div class="form-group">
				<label>邮箱</label>
				<input class="J-email form-control" placeholder="" value="<?php echo $aSetting['email']; ?>" />
			</div>
			<div class="form-group" style="display:none;">
				<label>QQ</label>
				<input class="J-qq form-control" placeholder="" value="<?php echo $aSetting['qq']; ?>" />
			</div>
			<div class="form-group">
				<label>地址</label>
				<input class="J-address form-control" placeholder="" value="<?php echo $aSetting['address']; ?>" />
			</div>
			<div class="form-group" style="display:none;">
				<label>欢迎语</label>
				<textarea class="J-intro form-control" placeholder="" ><?php echo $aSetting['intro']; ?></textarea>
			</div>
			<div class="form-group">
				<label>地图接口地址</label>
				<input class="J-mapApi form-control" placeholder="" value="<?php echo $aSetting['mapApi']; ?>" />
			</div>
			<div class="form-group">
				<label>经度</label>
				<input class="J-lng form-control" placeholder="" value="<?php echo $aSetting['lng']; ?>" />
			</div>
			<div class="form-group">
				<label>纬度</label>
				<input class="J-lat form-control" placeholder="" value="<?php echo $aSetting['lat']; ?>" />
			</div>
			<br />
			<div class="form-group"><button type="button" class="btn btn-primary" onclick="saveContactusSetting(this);">保存设置</button></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
	function saveContactusSetting(o){
		if($(o).attr('disabled')){
			return;
		}
		ajax({
			url : Tools.url('manage', 'contactus/save'),
			data : {
				companyName : $('.J-companyName').val(),
				phone : $('.J-phone').val(),
				hotsPhone : $('.J-hotsPhone').val(),
				servicePhone : $('.J-servicePhone').val(),
				mobile : $('.J-mobile').val(),
				email : $('.J-email').val(),
				qq : $('.J-qq').val(),
				address : $('.J-address').val(),
				mapApi : $('.J-mapApi').val(),
				lng : $('.J-lng').val(),
				lat : $('.J-lat').val(),
				intro : $('.J-intro').val()
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

	$(function(){
		
	});
</script>