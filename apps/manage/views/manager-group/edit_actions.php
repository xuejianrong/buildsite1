<?php
use umeworld\lib\Url;
use manage\widgets\Table;
use yii\widgets\LinkPager;

$siteTitle = '权限管理';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'name' => ['title' => '角色名称', 'class' => 'col-sm-11'],
						'operate' =>	[
							'title' => '操作',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return '<a href="javascript:;" onclick="editManagerGroupActions(this, ' . $aData['id'] . ');">权限分配</a>';
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
	var aManagerGroupList = <?php echo json_encode($aList); ?>;
	var aManagerGroupActionsList = <?php echo json_encode($aManagerGroupActionsList); ?>;
	
	function buildManagerGroupActionsBoxHtml(aData){
		var html = '';
		for(var i in aManagerGroupActionsList){
			html += '<div class="form-group">';
				html += '<label>' + aManagerGroupActionsList[i].title + '</label>';
				html += '<label class="checkbox-inline" style="margin-left:10px;-webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none;"><input type="checkbox" class="J-cb-action-select-all">全选</label>';
			html += '</div>';
			if(aManagerGroupActionsList[i].action_list.length != 0){
				html += '<div class="form-group">';
				for(var j in  aManagerGroupActionsList[i].action_list){
					html += '<label class="checkbox-inline" style="margin:0 10px 10px 0;-webkit-user-select:none; -moz-user-select:none; -ms-user-select:none; user-select:none;">';
						html += '<input type="checkbox" class="J-cb-action" ' + (Tools.inArray(j, aData.actions) ? 'checked' : '') + ' value="' + j + '">' + aManagerGroupActionsList[i].action_list[j];
					html += '</label>';
				}
				html += '</div>';
			}
		}
		return html;
	}
	
	function editManagerGroupActions(o, id){
		id = parseInt(id);
		var aData = {};
		for(var i in aManagerGroupList){
			if(aManagerGroupList[i].id == id){
				aData = aManagerGroupList[i];
				break;
			}
		}
		$.teninedialog({
			title : id == 0 ? '新增<?php echo $siteTitle; ?>' : '编辑<?php echo $siteTitle; ?>',
			content : buildManagerGroupActionsBoxHtml(aData),
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
				$('.J-cb-action-select-all').each(function(){
					Tools.bindSelectAll($(this), $(this).parent().parent().next().find('.J-cb-action'));
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
					var aActions = [];
					modal.find('.J-cb-action:checked').each(function(){
						aActions.push($(this).val());
					});
					var o = sender;
					if($(o).attr('disabled')){
						return;
					}
					ajax({
						url : Tools.url('manage', 'manager-group/save-actions'),
						data : {
							id : id,
							actions : aActions
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
	
	$(function(){
		
	});
</script>