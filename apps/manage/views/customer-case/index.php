<?php
use umeworld\lib\Url;
use manage\widgets\Table;
use yii\widgets\LinkPager;
use common\model\ContentItem;
$this->registerAssetBundle('common\assets\UmeditorAsset');
$this->registerAssetBundle('common\assets\FileAsset');

$siteTitle = '客户案例';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editCustomerCase(this, 0);">+ 新建</button></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'title' => ['title' => '标题', 'class' => 'col-sm-5'],
						'source' => ['title' => '来源', 'class' => 'col-sm-1'],
						'order' => ['title' => '排序', 'class' => 'col-sm-1'],
						'is_jinping' => [
							'title' => '是否精品',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return $aData['is_jinping'] ? '是' : '否';
							}
						],
						'status' => [
							'title' => '发布状态',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return $aData['status'] ? '已发布' : '未发布';
							}
						],
						'create_time' => [
							'title' => '发布时间', 
							'class' => 'col-sm-2',
							'content' => function($aData){
								return date('Y-m-d H:i:s', $aData['create_time']);
							}
						],
						'operate' =>	[
							'title' => '操作',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return '<a href="javascript:;" onclick="editCustomerCase(this, ' . $aData['id'] . ');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteCustomerCase(this, ' . $aData['id'] . ');">删除</a>';
							}
						],
					],
					'aDataList'	=>	$aList,
				]);
				echo LinkPager::widget(['pagination' => $oPage]);
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var aCustomerCase = <?php echo json_encode($aList); ?>;
	
	function buildCustomerCaseBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>标题</label>';
			html += '<input class="J-cp-title form-control" placeholder="" value="' + (typeof(aData.title) != 'undefined' ? aData.title : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>案例截图</label>';
			html += '<button type="button" class="btn btn-primary" onclick="commonUploadImage(this);">选择图片</button><input type="file" class="J-common-upload-image" style="display:none;" />';
			html += '<p class="help-block">建议上传尺寸: 270 x 375 像素</p>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<img class="J-cp-shortcut img-thumbnail" style="width:270px;height:375px;" data-path="' + (typeof(aData.other_info) != 'undefined' && typeof(aData.other_info.shortcut) != 'undefined' ? aData.other_info.shortcut : '') + '" src="' + (typeof(aData.other_info) != 'undefined' && typeof(aData.other_info.shortcut) != 'undefined' ? '<?php echo Yii::getAlias('@r.url') . '/'; ?>' + aData.other_info.shortcut : 'http://placehold.it/270x375') + '" alt="">';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>来源</label>';
			html += '<input class="J-cp-source form-control" placeholder="" value="' + (typeof(aData.source) != 'undefined' ? aData.source : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>链接</label>';
			html += '<input class="J-cp-url form-control" placeholder="" value="' + (typeof(aData.other_info) != 'undefined' && typeof(aData.other_info.url) != 'undefined' ? aData.other_info.url : 'http://') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>是否精品</label>';
			html += '<select class="J-cp-isJinping form-control"><option value="0">否</option><option value="1">是</option></select>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>发布状态</label>';
			html += '<select class="J-cp-status form-control"><option value="<?php echo ContentItem::STATUS_NOT_PUBLISH; ?>">未发布</option><option value="<?php echo ContentItem::STATUS_PUBLISHED; ?>">已发布</option></select>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>排序</label>';
			html += '<input class="J-cp-order form-control" placeholder="" value="' + (typeof(aData.order) != 'undefined' ? aData.order : '0') + '" />';
		html += '</div>';
		html += '<div class="J-cp-content-wrap form-group">';
			html += '<label>内容</label>';
			html += '<div id="J-cp-content" class="J-cp-content" type="text/plain" style="height:400px;width:850px;"></div>';
		html += '</div>';
		html += '<div class="J-x-content" style="display:none">' + (typeof(aData.content) != 'undefined' ? aData.content : '') + '</div>';
		return html;
	}
	
	function editCustomerCase(o, id){
		id = parseInt(id);
		var aData = {};
		for(var i in aCustomerCase){
			if(aCustomerCase[i].id == id){
				aData = aCustomerCase[i];
				break;
			}
		}
		$.teninedialog({
			title : id == 0 ? '新增<?php echo $siteTitle; ?>' : '编辑<?php echo $siteTitle; ?>',
			content : buildCustomerCaseBoxHtml(aData),
			url : '',
			showCloseButton : false,
			otherButtons : ['保存', '取消'],
			otherButtonStyles : ['btn-primary'],
			bootstrapModalOption : {keyboard: true},
			dialogShow : function(model){
				//alert('即将显示对话框');
				$('.modal-dialog').width(900);
				$('.modal-body').css({"max-height" : 500});
			},
			dialogShown : function(){
				if(id){
					$('.J-cp-status').val(aData.status);
				}
				$('.J-cp-isJinping').val(aData.is_jinping);
				$('.J-common-upload-image').unbind();
				$('.J-common-upload-image').on('change', function(){
					Tools.showLoading();
					var self = this;
					Tools.uploadFileHandle('<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>', self['files'][0], function(aData){
						Tools.hideLoading();
						$('.J-cp-shortcut').attr('src', App.url.resource + '/' + aData.data);
						$('.J-cp-shortcut').attr('data-path', aData.data);
					});
				});
				
				UM.getEditor('J-cp-content', {
					toolbar:[
						'source | emotion image insertvideo | bold forecolor | justifyleft justifycenter justifyright  | removeformat |',
						'link'
					],
					imageUrl : '<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>?_is_ajax=1',
					imagePath : '<?php echo Yii::getAlias('@r.url'); ?>/',
					imageFieldName : 'filecontent'
				}).ready(function() {
				   this.setContent($('.J-x-content').html());
				   $('.J-x-content').remove();
				});
			},
			dialogHide : function(){
				//alert('即将关闭对话框');
				UM.getEditor('J-cp-content').destroy();
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
						url : Tools.url('manage', 'content-item/save'),
						data : {
							id : id,
							type : <?php echo ContentItem::TYPE_CUSTOMER_CASE; ?>,
							title : $('.J-cp-title').val(),
							source : $('.J-cp-source').val(),
							status : $('.J-cp-status').val(),
							order : $('.J-cp-order').val(),
							isJinping : $('.J-cp-isJinping').val(),
							content : $('.J-cp-content').html(),
							aOtherInfo : {
								url : $('.J-cp-url').val(),
								shortcut : $('.J-cp-shortcut').attr('data-path')
							}
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

	function deleteCustomerCase(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('manage', 'content-item/delete'),
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