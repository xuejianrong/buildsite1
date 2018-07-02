<?php
use umeworld\lib\Url;
use manage\widgets\Table;
use yii\widgets\LinkPager;
$this->registerAssetBundle('common\assets\FileAsset');

$siteTitle = '产品分类';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editProductsCategory(this, 0);">+ 新建</button></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'name' => ['title' => '分类名称', 'class' => 'col-sm-11'],
						'operate' =>	[
							'title' => '操作',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return '<a href="javascript:;" onclick="editProductsCategory(this, ' . $aData['id'] . ');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteProductsCategory(this, ' . $aData['id'] . ');">删除</a>';
							}
						],
					],
					'aDataList'	=>	$aList,
				]);
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var aProductsCategoryList = <?php echo json_encode($aList); ?>;
	
	function buildProductsCategoryBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>分类名称</label>';
			html += '<input class="J-cp-name form-control" placeholder="" value="' + (typeof(aData.name) != 'undefined' ? aData.name : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>分类英文名称</label>';
			html += '<input class="J-cp-ename form-control" placeholder="" value="' + (typeof(aData.ename) != 'undefined' ? aData.ename : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>分类图片</label>';
			html += '<button type="button" class="btn btn-primary" onclick="commonUploadImage(this);">选择图片</button><input type="file" class="J-common-upload-image" style="display:none;" />';
			html += '<p class="help-block">建议上传尺寸: 300 x 300 像素</p>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<img class="J-cp-shortcut img-thumbnail" style="width:300px;height:300px;" data-path="' + (typeof(aData.shortcut) != 'undefined' ? aData.shortcut : '') + '" src="' + (typeof(aData.shortcut) != 'undefined' && aData.shortcut != '' ? '<?php echo Yii::getAlias('@r.url') . '/'; ?>' + aData.shortcut : 'http://placehold.it/300x300') + '" alt="">';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>所在父分类</label>';
			html += '<select class="J-cp-pid form-control">';
			html += '<option value="0">无</option>';
			for(var i in aProductsCategoryList){
				if(aProductsCategoryList[i].pid == 0){
					html += '<option value="' + aProductsCategoryList[i].id + '">' + aProductsCategoryList[i].name + '</option>';
				}
			}
			html += '</select>';
		html += '</div>';
		
		return html;
	}
	
	function editProductsCategory(o, id){
		id = parseInt(id);
		var aData = {};
		for(var i in aProductsCategoryList){
			if(aProductsCategoryList[i].id == id){
				aData = aProductsCategoryList[i];
				break;
			}
		}
		$.teninedialog({
			title : id == 0 ? '新增<?php echo $siteTitle; ?>' : '编辑<?php echo $siteTitle; ?>',
			content : buildProductsCategoryBoxHtml(aData),
			url : '',
			showCloseButton : false,
			otherButtons : ['保存', '取消'],
			otherButtonStyles : ['btn-primary'],
			bootstrapModalOption : {keyboard: true},
			dialogShow : function(model){
				//alert('即将显示对话框');
				//$('.modal-dialog').width(900);
				$('.modal-body').css({"max-height" : 500});
			},
			dialogShown : function(){
				if(aData.pid != 0){
					$('.J-cp-pid').val(aData.pid);
				}
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
						url : Tools.url('<?php echo Yii::$app->id; ?>', 'products-category/save'),
						data : {
							id : id,
							pid : $('.J-cp-pid').val(),
							name : $('.J-cp-name').val(),
							ename : $('.J-cp-ename').val(),
							shortcut : $('.J-cp-shortcut').attr('data-path'),
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

	function deleteProductsCategory(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('<?php echo Yii::$app->id; ?>', 'products-category/delete'),
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