<?php
use umeworld\lib\Url;
$this->registerAssetBundle('common\assets\FileAsset');
$this->registerAssetBundle('common\assets\UmeditorAsset');

$siteTitle = '关于我们';
$this->setTitle($siteTitle);
?>
<style type="text/css"> 
	.J-companyCertificate-wrap{float:left; margin-bottom: 10px;}
	.J-companyCertificate-wrap i{position: relative; display: block; float: right; font-size: 24px; cursor: pointer; right: 26px; top: 5px;}
</style>
<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	
	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
				<label>公司简介</label>
				<div id="J-cp-companyProfile" class="J-cp-companyProfile" type="text/plain" style="width:800px;height:300px;"></div>
			</div>
			<div class="form-group">
				<label>公司历程</label>
				<div id="J-cp-companyHistory" class="J-cp-companyHistory" type="text/plain" style="width:800px;height:300px;"></div>
			</div>
			<div class="form-group">
				<label>企业文化</label>
				<div id="J-cp-companyCulture" class="J-cp-companyCulture" type="text/plain" style="width:800px;height:300px;"></div>
			</div>
			<div class="form-group">
				<label>资质证书</label>
				<button type="button" class="btn btn-primary" onclick="commonUploadImage(this);">选择图片</button><input type="file" class="J-common-upload-image" style="display:none;" />
				<p class="help-block">建议上传尺寸: 400 x 300 像素</p>
			</div>
			<div class="J-companyCertificate-list row" style="width:1000px;">
			<?php foreach($aSetting['aCompanyCertificate'] as $path){ ?>
				<div class="J-companyCertificate-wrap"><img class="J-companyCertificate img-thumbnail" style="width:400px;height:300px;" data-path="<?php echo $path; ?>" src="<?php echo Yii::getAlias('@r.url') . '/' . $path; ?>" alt=""><i class="fa fa-trash" onclick="removeTrashBtn(this);"></i></div>
			<?php } ?>
			</div>
			<br />
			<div class="form-group"><button type="button" class="btn btn-primary" onclick="saveAboutusSetting(this);">保存设置</button></div>
		</div>
	</div>
</div>
<div class="J-x-companyProfile" style="display:none"><?php echo $aSetting['companyProfile']; ?></div>
<div class="J-x-companyHistory" style="display:none"><?php echo $aSetting['companyHistory']; ?></div>
<div class="J-x-companyCulture" style="display:none"><?php echo $aSetting['companyCulture']; ?></div>
<script type="text/javascript">
	
	function saveAboutusSetting(o){
		if($(o).attr('disabled')){
			return;
		}
		var aCompanyCertificate = [];
		$('.J-companyCertificate').each(function(){
			aCompanyCertificate.push($(this).attr('data-path'));
		});
		ajax({
			url : Tools.url('manage', 'aboutus/save'),
			data : {
				companyProfile : $('.J-cp-companyProfile').html(),
				companyHistory : $('.J-cp-companyHistory').html(),
				companyCulture : $('.J-cp-companyCulture').html(),
				aCompanyCertificate : aCompanyCertificate
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

	function removeTrashBtn(o){
		$(o).parent().remove();
	}
	
	$(function(){
		$('.J-common-upload-image').on('change', function(){
			Tools.showLoading();
			var self = this;
			Tools.uploadFileHandle('<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>', self['files'][0], function(aData){
				Tools.hideLoading();
				$('.J-companyCertificate-list').append('<div class="J-companyCertificate-wrap"><img class="J-companyCertificate img-thumbnail" style="width:400px;height:300px;" data-path="' + aData.data + '" src="' + App.url.resource + '/' + aData.data + '" alt=""><i class="fa fa-trash" onclick="removeTrashBtn(this);"></i></div>');
			});
		});
		
		UM.getEditor('J-cp-companyProfile', {
			toolbar:[
				'source | emotion image insertvideo | bold forecolor | justifyleft justifycenter justifyright  | removeformat |',
				'link'
			],
			imageUrl : '<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>?_is_ajax=1',
			imagePath : '<?php echo Yii::getAlias('@r.url'); ?>/',
			imageFieldName : 'filecontent'
		}).ready(function() {
		   this.setContent($('.J-x-companyProfile').html());
		   $('.J-x-companyProfile').remove();
		});
		UM.getEditor('J-cp-companyHistory', {
			toolbar:[
				'source | emotion image insertvideo | bold forecolor | justifyleft justifycenter justifyright  | removeformat |',
				'link'
			],
			imageUrl : '<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>?_is_ajax=1',
			imagePath : '<?php echo Yii::getAlias('@r.url'); ?>/',
			imageFieldName : 'filecontent'
		}).ready(function() {
		   this.setContent($('.J-x-companyHistory').html());
		   $('.J-x-companyHistory').remove();
		});
		UM.getEditor('J-cp-companyCulture', {
			toolbar:[
				'source | emotion image insertvideo | bold forecolor | justifyleft justifycenter justifyright  | removeformat |',
				'link'
			],
			imageUrl : '<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>?_is_ajax=1',
			imagePath : '<?php echo Yii::getAlias('@r.url'); ?>/',
			imageFieldName : 'filecontent'
		}).ready(function() {
		   this.setContent($('.J-x-companyCulture').html());
		   $('.J-x-companyCulture').remove();
		});
		
	});
</script>