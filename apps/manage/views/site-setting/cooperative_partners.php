<?php
use umeworld\lib\Url;
use manage\widgets\Table;
$this->registerAssetBundle('common\assets\FileAsset');

$siteTitle = '合作伙伴';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editCooperativePartners(this, '');">+ 新建</button></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'name' => ['title' => '合作伙伴', 'class' => 'col-sm-2'],
						'linkLogo' =>	[
							'title' => '链接图片',
							'class' => 'col-sm-8',
							'content' => function($aData){
								return '<a href="' . $aData['link'] . '" target="_blank"><img class="img-thumbnail" style="width:190px;height:100px;" src="' . Yii::getAlias('@r.url') . '/' . $aData['linkLogo'] . '" alt=""></a>';
							}
						],
						'order' => ['title' => '排序', 'class' => 'col-sm-1'],
						'operate' =>	[
							'title' => '操作',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return '<a href="javascript:;" onclick="editCooperativePartners(this, \'' . $aData['id'] . '\');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteCooperativePartners(this, \'' . $aData['id'] . '\');">删除</a>';
							}
						],
					],
					'aDataList'	=>	$aCooperativePartners,
				]);
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var aCooperativePartners = <?php echo json_encode($aCooperativePartners); ?>;
	
	function buildCooperativePartnersBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>合作伙伴</label>';
			html += '<input class="J-cp-name form-control" placeholder="" value="' + (typeof(aData.name) != 'undefined' ? aData.name : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>链接</label>';
			html += '<input class="J-cp-link form-control" placeholder="" value="' + (typeof(aData.link) != 'undefined' ? aData.link : 'http://') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>排序</label>';
			html += '<input class="J-cp-order form-control" placeholder="" value="' + (typeof(aData.order) != 'undefined' ? aData.order : '0') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>Logo</label>';
			html += '<button type="button" class="btn btn-primary" onclick="commonUploadImage(this);">选择图片</button><input type="file" class="J-common-upload-image" style="display:none;" />';
			html += '<p class="help-block">建议上传尺寸: 190 x 100 像素</p>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<img class="J-cp-linkLogo img-thumbnail" style="width:190px;height:100px;" data-path="' + (typeof(aData.linkLogo) != 'undefined' ? aData.linkLogo : '') + '" src="' + (typeof(aData.linkLogo) != 'undefined' ? '<?php echo Yii::getAlias('@r.url') . '/'; ?>' + aData.linkLogo : 'http://placehold.it/190x100') + '" alt="">';
		html += '</div>';
		return html;
	}
	
	function editCooperativePartners(o, id){
		var aData = {};
		for(var i in aCooperativePartners){
			if(aCooperativePartners[i].id == id){
				aData = aCooperativePartners[i];
				break;
			}
		}
		$.teninedialog({
			title : id == '' ? '新增<?php echo $siteTitle; ?>' : '编辑<?php echo $siteTitle; ?>',
			content : buildCooperativePartnersBoxHtml(aData),
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
						$('.J-cp-linkLogo').attr('src', App.url.resource + '/' + aData.data);
						$('.J-cp-linkLogo').attr('data-path', aData.data);
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
						url : Tools.url('manage', 'site-setting/save-cooperative-partners'),
						data : {
							id : id,
							name : $('.J-cp-name').val(),
							link : $('.J-cp-link').val(),
							order : $('.J-cp-order').val(),
							linkLogo : $('.J-cp-linkLogo').attr('data-path')
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

	function deleteCooperativePartners(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('manage', 'site-setting/delete-cooperative-partners'),
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