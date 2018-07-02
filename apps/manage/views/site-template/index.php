<?php
use umeworld\lib\Url;
use manage\widgets\Table;
$this->registerAssetBundle('common\assets\FileAsset');

$siteTitle = '网站模板';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editSiteTemplate(this, '');">+ 新建</button></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'name' => ['title' => '模板名称', 'class' => 'col-sm-2'],
						'shortcut' =>	[
							'title' => '模板截图',
							'class' => 'col-sm-8',
							'content' => function($aData){
								return '<a href="' . $aData['link'] . '" target="_blank"><img class="img-thumbnail" style="width:295px;height:375px;" src="' . Yii::getAlias('@r.url') . '/' . $aData['shortcut'] . '" alt=""></a>';
							}
						],
						'order' => ['title' => '排序', 'class' => 'col-sm-1'],
						'operate' =>	[
							'title' => '操作',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return '<a href="javascript:;" onclick="editSiteTemplate(this, \'' . $aData['id'] . '\');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteSiteTemplate(this, \'' . $aData['id'] . '\');">删除</a>';
							}
						],
					],
					'aDataList'	=>	$aSiteTemplate,
				]);
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var aSiteTemplate = <?php echo json_encode($aSiteTemplate); ?>;
	
	function buildSiteTemplateBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>模板名称</label>';
			html += '<input class="J-cp-name form-control" placeholder="" value="' + (typeof(aData.name) != 'undefined' ? aData.name : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>模板链接</label>';
			html += '<input class="J-cp-link form-control" placeholder="" value="' + (typeof(aData.link) != 'undefined' ? aData.link : 'http://') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>模板价格</label>';
			html += '<input class="J-cp-price form-control" placeholder="" value="' + (typeof(aData.price) != 'undefined' ? aData.price : '0') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>排序</label>';
			html += '<input class="J-cp-order form-control" placeholder="" value="' + (typeof(aData.order) != 'undefined' ? aData.order : '0') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>模板截图</label>';
			html += '<button type="button" class="btn btn-primary" onclick="commonUploadImage(this);">选择图片</button><input type="file" class="J-common-upload-image" style="display:none;" />';
			html += '<p class="help-block">建议上传尺寸: 295 x 375 像素</p>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<img class="J-cp-shortcut img-thumbnail" style="width:295px;height:375px;" data-path="' + (typeof(aData.shortcut) != 'undefined' ? aData.shortcut : '') + '" src="' + (typeof(aData.shortcut) != 'undefined' ? '<?php echo Yii::getAlias('@r.url') . '/'; ?>' + aData.shortcut : 'http://placehold.it/295x375') + '" alt="">';
		html += '</div>';
		return html;
	}
	
	function editSiteTemplate(o, id){
		var aData = {};
		for(var i in aSiteTemplate){
			if(aSiteTemplate[i].id == id){
				aData = aSiteTemplate[i];
				break;
			}
		}
		$.teninedialog({
			title : id == '' ? '新增<?php echo $siteTitle; ?>' : '编辑<?php echo $siteTitle; ?>',
			content : buildSiteTemplateBoxHtml(aData),
			url : '',
			showCloseButton : false,
			otherButtons : ['保存', '取消'],
			otherButtonStyles : ['btn-primary'],
			bootstrapModalOption : {keyboard: true},
			dialogShow : function(){
				//alert('即将显示对话框');
			},
			dialogShown : function(){
				$('.J-common-upload-image').unbind();
				$('.J-common-upload-image').on('change', function(){
					Tools.showLoading();
					var self = this;
					Tools.uploadFileHandle('<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>', self['files'][0], function(aData){
						Tools.hideLoading();
						$('.J-cp-shortcut').attr('src', App.url.resource + aData.data);
						$('.J-cp-shortcut').attr('data-path', aData.data);
					});
				});
			},
			dialogHide : function(){
				//alert('即将关闭对话框');
			},
			dialogHidden : function(){
				//alert('关闭对话框');
			},
			clickButton : function(sender, modal, index){
				if(index == 0){
					var o = sender;
					if($(o).attr('disabled')){
						return;
					}
					ajax({
						url : Tools.url('manage', 'site-template/save'),
						data : {
							id : id,
							name : $('.J-cp-name').val(),
							link : $('.J-cp-link').val(),
							order : $('.J-cp-order').val(),
							price : $('.J-cp-price').val(),
							shortcut : $('.J-cp-shortcut').attr('data-path')
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
				}else{
					$(this).closeDialog(modal);
				}
			}
		});
	}

	function deleteSiteTemplate(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('manage', 'site-template/delete'),
				data : {
					id : id
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
		});
	}
	
	$(function(){
		
	});
</script>