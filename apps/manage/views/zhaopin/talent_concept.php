<?php
use umeworld\lib\Url;
use manage\widgets\Table;
$this->registerAssetBundle('common\assets\FileAsset');
$this->registerAssetBundle('common\assets\UmeditorAsset');

$siteTitle = '人才理念';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	
	<div class="row">
		<div class="col-lg-6">
			<div class="form-group">
				<label>人才理念</label>
				<div id="J-cp-talentConcept" class="J-cp-talentConcept" type="text/plain" style="width:800px;height:300px;"></div>
			</div>
			<br />
			<div class="form-group"><button type="button" class="btn btn-primary" onclick="saveTalentConcept(this);">保存设置</button></div>
		</div>
	</div>
</div>
<div class="J-x-talentConcept" style="display:none"><?php echo $talentConcept; ?></div>
<script type="text/javascript">
	
	function saveTalentConcept(o){
		if($(o).attr('disabled')){
			return;
		}
		ajax({
			url : Tools.url('<?php echo Yii::$app->id; ?>', 'zhaopin/save-talent-concept'),
			data : {
				talentConcept : $('.J-cp-talentConcept').html()
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
		
		UM.getEditor('J-cp-talentConcept', {
			toolbar:[
				'source | emotion image insertvideo | bold forecolor | justifyleft justifycenter justifyright  | removeformat |',
				'link'
			],
			imageUrl : '<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>?_is_ajax=1',
			imagePath : '<?php echo Yii::getAlias('@r.url'); ?>/',
			imageFieldName : 'filecontent'
		}).ready(function() {
		   this.setContent($('.J-x-talentConcept').html());
		   $('.J-x-talentConcept').remove();
		});
				
	});
</script>