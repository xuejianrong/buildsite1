<?php
use umeworld\lib\Url;
use manage\widgets\Table;
use yii\widgets\LinkPager;
use common\model\Products;
$this->registerAssetBundle('common\assets\FileAsset');

$siteTitle = '产品列表';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editProducts(this, 0);">+ 新建</button></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'name' => ['title' => '产品名称', 'class' => 'col-sm-6'],
						'category_id' => [
							'title' => '分类',
							'class' => 'col-sm-2',
							'content' => function($aData){
								return isset($aData['category_info']['name']) ? $aData['category_info']['name'] : '';
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
							'title' => '创建时间', 
							'class' => 'col-sm-2',
							'content' => function($aData){
								return date('Y-m-d H:i:s', $aData['create_time']);
							}
						],
						'operate' =>	[
							'title' => '操作',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return '<a href="javascript:;" onclick="editProducts(this, ' . $aData['id'] . ');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteProducts(this, ' . $aData['id'] . ');">删除</a>';
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
	var aProductsList = <?php echo json_encode($aList); ?>;
	var aCategoryList = <?php echo json_encode($aCategoryList); ?>;
	
	function buildProductsBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>产品名称</label>';
			html += '<input class="J-cp-name form-control" placeholder="" value="' + (typeof(aData.name) != 'undefined' ? aData.name : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>分类</label>';
			html += '<table style="border:none;"><tr style="border:none;"><td style="border:none;"><select class="J-cp-pid form-control" style="width:200px;">';
			for(var i in aCategoryList){
				html += '<option value="' + aCategoryList[i].id + '">' + aCategoryList[i].name + '</option>';
			}
			html += '</select></td><td style="border:none;">';
			html += '<select class="J-cp-categoryId form-control" style="margin-left:10px;width:200px;"></select></td></tr></table>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>产品图片</label>';
			html += '<button type="button" class="btn btn-primary" onclick="commonUploadImage(this);">选择图片</button><input type="file" class="J-common-upload-image" style="display:none;" />';
			html += '<p class="help-block">建议上传尺寸: 200 x 305 像素</p>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<img class="J-cp-shortcut img-thumbnail" style="width:200px;height:305px;" data-path="' + (typeof(aData.shortcut) != 'undefined' ? aData.shortcut : '') + '" src="' + (typeof(aData.shortcut) != 'undefined' ? '<?php echo Yii::getAlias('@r.url') . '/'; ?>' + aData.shortcut : 'http://placehold.it/200x305') + '" alt="">';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>产品型号</label>';
			html += '<input class="J-cp-productModel form-control" placeholder="" value="' + (typeof(aData.product_model) != 'undefined' ? aData.product_model : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>原产地</label>';
			html += '<input class="J-cp-producePlace form-control" placeholder="" value="' + (typeof(aData.produce_place) != 'undefined' ? aData.produce_place : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>商标品牌</label>';
			html += '<input class="J-cp-brand form-control" placeholder="" value="' + (typeof(aData.brand) != 'undefined' ? aData.brand : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>参考价格</label>';
			html += '<input class="J-cp-price form-control" placeholder="" value="' + (typeof(aData.price) != 'undefined' ? aData.price : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>交货地点</label>';
			html += '<input class="J-cp-deliveryAddress form-control" placeholder="" value="' + (typeof(aData.delivery_address) != 'undefined' ? aData.delivery_address : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>是否提供样品</label>';
			html += '<select class="J-cp-hasSample form-control"><option value="0">否</option><option value="1">是</option></select>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>发布状态</label>';
			html += '<select class="J-cp-status form-control"><option value="<?php echo Products::STATUS_NOT_PUBLISH; ?>">未发布</option><option value="<?php echo Products::STATUS_PUBLISHED; ?>">已发布</option></select>';
		html += '</div>';
		html += '<div class="J-cp-content-wrap form-group">';
			html += '<label>产品描述</label>';
			html += '<textarea class="J-cp-description form-control" style="height:100px;width:850px;">' + (typeof(aData.description) != 'undefined' ? aData.description : '') + '</textarea>';
		html += '</div>';
		html += '<div class="J-cp-content-wrap form-group">';
			html += '<label>其它信息项</label>';
			html += '<div class="col-lg-12" style="padding-left:0;padding-right:0;">';
				html += '<div class="table-responsive">';
					html += '<table class="table table-bordered table-hover">';
						html += '<thead>';
							html += '<tr>';
								html += '<th>项</th>';
								html += '<th>值</th>';
								html += '<th>操作</th>';
							html += '</tr>';
						html += '</thead>';
						html += '<tbody>';
						if(typeof(aData.other_info) != 'undefined' && aData.other_info.length != 0){
							for(var j in aData.other_info){
								html += '<tr class="J-other-info-item">';
									html += '<td><input type="text" class="J-other-info-key form-control" value="' + aData.other_info[j].key + '" /></td>';
									html += '<td><input type="text" class="J-other-info-value form-control" value="' + aData.other_info[j].value + '" /></td>';
									html += '<td><button type="button" class="btn btn-danger" onclick="removeOtherInfoItem(this);">删除</button></td>';
								html += '</tr>';
							}
						}
							html += '<tr>';
								html += '<td><input type="text" class="J-other-info-key form-control" /></td>';
								html += '<td><input type="text" class="J-other-info-value form-control" /></td>';
								html += '<td><button type="button" class="J-other-info-add btn btn-primary">添加</button></td>';
							html += '</tr>';
						html += '</tbody>';
					html += '</table>';
				html += '</div>';
			html += '</div>';
		html += '</div>';
		
		return html;
	}
	
	function editProducts(o, id){
		id = parseInt(id);
		var aData = {};
		for(var i in aProductsList){
			if(aProductsList[i].id == id){
				aData = aProductsList[i];
				break;
			}
		}
		$.teninedialog({
			title : id == 0 ? '新增产品' : '编辑产品',
			content : buildProductsBoxHtml(aData),
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
					$('.J-cp-hasSample').val(aData.has_sample);
					$('.J-cp-pid').change(function(){
						var flag = false;
						var html = '';
						for(var j in aCategoryList){
							if(aCategoryList[j].pid == $(this).val()){
								if(aData.category_id == aCategoryList[j].id){
									flag = true;
								}
								html += '<option value="' + aCategoryList[j].id + '">' + aCategoryList[j].name + '</option>';
							}
						}
						$('.J-cp-categoryId').html(html);
						if(flag){
							$('.J-cp-categoryId').val(aData.category_id);
						}
					});
					for(var i in aCategoryList){
						if(aCategoryList[i].id == aData.category_id){
							if(aCategoryList[i].pid != 0){
								$('.J-cp-pid').val(aCategoryList[i].pid);
							}else{
								$('.J-cp-pid').val(aCategoryList[i].id);
							}
							$('.J-cp-pid').change();
							break;
						}
					}
				}
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
				
				$('.J-other-info-add').click(function(){
					var oParent = $(this).parent().parent();
					var key = oParent.find('.J-other-info-key').val();
					var value = oParent.find('.J-other-info-value').val();
					if(key == ''){
						UBox.show('请填写项', -1);
						return;
					}
					var html = '';
					html += '<tr class="J-other-info-item">';
						html += '<td><input type="text" class="J-other-info-key form-control" value="' + key + '" /></td>';
						html += '<td><input type="text" class="J-other-info-value form-control" value="' + value + '" /></td>';
						html += '<td><button type="button" class="btn btn-danger" onclick="removeOtherInfoItem(this);">删除</button></td>';
					html += '</tr>';
					oParent.before(html);
					oParent.find('.J-other-info-key').val('');
					oParent.find('.J-other-info-value').val('');
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
					var aOtherInfo = [];
					$('.J-other-info-item').each(function(){
						aOtherInfo.push({
							key : $(this).find('.J-other-info-key').val(),
							value : $(this).find('.J-other-info-value').val()
						});
					});
					
					ajax({
						url : Tools.url('<?php echo Yii::$app->id; ?>', 'products/save'),
						data : {
							id : id,
							name : $('.J-cp-name').val(),
							categoryId : $('.J-cp-categoryId').val() == null ? $('.J-cp-pid').val() : $('.J-cp-categoryId').val(),
							shortcut : $('.J-cp-shortcut').attr('data-path'),
							productModel : $('.J-cp-productModel').val(),
							producePlace : $('.J-cp-producePlace').val(),
							brand : $('.J-cp-brand').val(),
							price : $('.J-cp-price').val(),
							deliveryAddress : $('.J-cp-deliveryAddress').val(),
							hasSample : $('.J-cp-hasSample').val(),
							status : $('.J-cp-status').val(),
							description : $('.J-cp-description').val(),
							aOtherInfo : aOtherInfo
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

	function deleteProducts(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('<?php echo Yii::$app->id; ?>', 'products/delete'),
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
	
	function removeOtherInfoItem(o){
		$(o).parent().parent().remove();
	}
	
	$(function(){
		
	});
</script>