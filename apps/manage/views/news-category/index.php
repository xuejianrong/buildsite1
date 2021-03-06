<?php
use umeworld\lib\Url;
use manage\widgets\Table;
use yii\widgets\LinkPager;

$siteTitle = '新闻分类';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editNewsCategory(this, 0);">+ 新建</button></div></div>
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
								return '<a href="javascript:;" onclick="editNewsCategory(this, ' . $aData['id'] . ');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteNewsCategory(this, ' . $aData['id'] . ');">删除</a>';
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
	var aNewsCategoryList = <?php echo json_encode($aList); ?>;
	
	function buildNewsCategoryBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>分类名称</label>';
			html += '<input class="J-cp-name form-control" placeholder="" value="' + (typeof(aData.name) != 'undefined' ? aData.name : '') + '" />';
		html += '</div>';
		
		return html;
	}
	
	function editNewsCategory(o, id){
		id = parseInt(id);
		var aData = {};
		for(var i in aNewsCategoryList){
			if(aNewsCategoryList[i].id == id){
				aData = aNewsCategoryList[i];
				break;
			}
		}
		$.teninedialog({
			title : id == 0 ? '新增<?php echo $siteTitle; ?>' : '编辑<?php echo $siteTitle; ?>',
			content : buildNewsCategoryBoxHtml(aData),
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
						url : Tools.url('manage', 'news-category/save'),
						data : {
							id : id,
							name : $('.J-cp-name').val()
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

	function deleteNewsCategory(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('manage', 'news-category/delete'),
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