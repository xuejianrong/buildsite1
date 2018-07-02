<?php
use umeworld\lib\Url;
$this->registerAssetBundle('common\assets\FileAsset');

$siteTitle = '基本设置';
$this->setTitle($siteTitle);
?>
<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	
	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
				<label>标题</label>
				<input class="J-siteTitle form-control" placeholder="" value="<?php echo $aSiteSetting['siteTitle']; ?>" />
			</div>
			<div class="form-group">
				<label>Logo</label>
				<button type="button" class="btn btn-primary" onclick="commonUploadImage(this);">选择图片</button><input type="file" class="J-common-upload-image" style="display:none;" />
				<p class="help-block">建议上传尺寸: 163 x 55 像素</p>
			</div>
			<div class="form-group">
				<img class="J-siteLogo img-thumbnail" style="width:163px;height:55px;" data-path="<?php echo $aSiteSetting['siteLogo']; ?>" src="<?php echo $aSiteSetting['siteLogo'] ? Yii::getAlias('@r.url') . '/' . $aSiteSetting['siteLogo'] : 'http://placehold.it/163x55'; ?>" alt="">
			</div>
			<div class="form-group">
				<label>SEO标题</label>
				<input class="J-siteSeoTitle form-control" placeholder="" value="<?php echo $aSiteSetting['siteSeoTitle']; ?>" />
			</div>
			<div class="form-group">
				<label>SEO关键字</label>
				<input class="J-siteSeoKeywords form-control" placeholder="" value="<?php echo $aSiteSetting['siteSeoKeywords']; ?>" />
			</div>
			<div class="form-group">
				<label>SEO描述</label>
				<input class="J-siteSeoDescription form-control" placeholder="" value="<?php echo $aSiteSetting['siteSeoDescription']; ?>" />
			</div>
			<div class="form-group">
				<label>版权信息</label>
				<input class="J-siteCopyright form-control" placeholder="" value="<?php echo $aSiteSetting['siteCopyright']; ?>" />
			</div>
			<br />
			<div class="form-group"><button type="button" class="btn btn-primary" onclick="saveSiteSetting(this);">保存设置</button></div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
	function saveSiteSetting(o){
		if($(o).attr('disabled')){
			return;
		}
		ajax({
			url : Tools.url('manage', 'site-setting/save'),
			data : {
				siteTitle : $('.J-siteTitle').val(),
				siteLogo : $('.J-siteLogo').attr('data-path'),
				siteSeoTitle : $('.J-siteSeoTitle').val(),
				siteSeoKeywords : $('.J-siteSeoKeywords').val(),
				siteSeoDescription : $('.J-siteSeoDescription').val(),
				siteCopyright : $('.J-siteCopyright').val()
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
		$('.J-common-upload-image').on('change', function(){
			Tools.showLoading();
			var self = this;
			Tools.uploadFileHandle('<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>', self['files'][0], function(aData){
				Tools.hideLoading();
				$('.J-siteLogo').attr('src', App.url.resource + aData.data);
				$('.J-siteLogo').attr('data-path', aData.data);
			});
		});
	});
</script>