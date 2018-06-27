<?php
use umeworld\lib\Url;
use manage\widgets\Table;
$this->registerAssetBundle('common\assets\FileAsset');
$this->registerAssetBundle('common\assets\UmeditorAsset');

$siteTitle = '招聘管理';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editZhaopin(this, '');">+ 新建</button></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'position' => ['title' => '招聘职位', 'class' => 'col-sm-4'],
						'count' => ['title' => '招聘人数', 'class' => 'col-sm-1'],
						'workplace' => ['title' => '工作地点', 'class' => 'col-sm-3'],
						'qualifications' => ['title' => '学历要求', 'class' => 'col-sm-1'],
						'expirence' => ['title' => '工作经验', 'class' => 'col-sm-1'],
						'publishTime' => ['title' => '发布时间', 'class' => 'col-sm-1'],
						'operate' =>	[
							'title' => '操作',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return '<a href="javascript:;" onclick="editZhaopin(this, \'' . $aData['id'] . '\');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteZhaopin(this, \'' . $aData['id'] . '\');">删除</a>';
							}
						],
					],
					'aDataList'	=>	$aZhaopinList,
				]);
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	var aZhaopinList = <?php echo json_encode($aZhaopinList); ?>;
	
	function buildZhaopinBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>招聘职位</label>';
			html += '<input class="J-cp-position form-control" placeholder="" value="' + (typeof(aData.position) != 'undefined' ? aData.position : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>招聘人数</label>';
			html += '<input class="J-cp-count form-control" placeholder="" value="' + (typeof(aData.count) != 'undefined' ? aData.count : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>工作地点</label>';
			html += '<input class="J-cp-workplace form-control" placeholder="" value="' + (typeof(aData.workplace) != 'undefined' ? aData.workplace : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>学历要求</label>';
			html += '<input class="J-cp-qualifications form-control" placeholder="" value="' + (typeof(aData.qualifications) != 'undefined' ? aData.qualifications : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>工作经验</label>';
			html += '<input class="J-cp-expirence form-control" placeholder="" value="' + (typeof(aData.expirence) != 'undefined' ? aData.expirence : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>发布时间</label>';
			html += '<input class="J-cp-publishTime form-control" placeholder="" value="' + (typeof(aData.publishTime) != 'undefined' ? aData.publishTime : '<?php echo date('Y-m-d'); ?>') + '" />';
		html += '</div>';
		html += '<div class="J-cp-content-wrap form-group">';
			html += '<label>职位描述</label>';
			html += '<div id="J-cp-description" class="J-cp-description" type="text/plain" style="height:400px;width:850px;"></div>';
		html += '</div>';
		html += '<div class="J-x-description" style="display:none">' + (typeof(aData.description) != 'undefined' ? aData.description : '') + '</div>';
		return html;
	}
	
	function editZhaopin(o, id){
		var aData = {};
		for(var i in aZhaopinList){
			if(aZhaopinList[i].id == id){
				aData = aZhaopinList[i];
				break;
			}
		}
		$.teninedialog({
			title : id == '' ? '新增招聘信息' : '编辑招聘信息',
			content : buildZhaopinBoxHtml(aData),
			url : '',
			showCloseButton : false,
			otherButtons : ['保存', '取消'],
			otherButtonStyles : ['btn-primary'],
			bootstrapModalOption : {keyboard: true},
			dialogShow : function(){
				//alert('即将显示对话框');
				$('.modal-dialog').width(900);
				$('.modal-body').css({"max-height" : 500});
			},
			dialogShown : function(){
				UM.getEditor('J-cp-description', {
					toolbar:[
						'source | emotion image insertvideo | bold forecolor | justifyleft justifycenter justifyright  | removeformat |',
						'link'
					],
					imageUrl : '<?php echo Url::to(Yii::$app->id, 'upload/upload-image'); ?>?_is_ajax=1',
					imagePath : '<?php echo Yii::getAlias('@r.url'); ?>/',
					imageFieldName : 'filecontent'
				}).ready(function() {
				   this.setContent($('.J-x-description').html());
				   $('.J-x-description').remove();
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
						url : Tools.url('<?php echo Yii::$app->id; ?>', 'zhaopin/save'),
						data : {
							id : id,
							position : $('.J-cp-position').val(),
							count : $('.J-cp-count').val(),
							workplace : $('.J-cp-workplace').val(),
							qualifications : $('.J-cp-qualifications').val(),
							expirence : $('.J-cp-expirence').val(),
							publishTime : $('.J-cp-publishTime').val(),
							description : $('.J-cp-description').html()
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

	function deleteZhaopin(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('<?php echo Yii::$app->id; ?>', 'zhaopin/delete'),
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