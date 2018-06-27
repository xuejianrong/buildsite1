<?php
use umeworld\lib\Url;
use manage\widgets\Table;
use yii\widgets\LinkPager;

$siteTitle = '后台用户';
$this->setTitle($siteTitle);
?>

<div class="container-fluid">
	<div class="row"><div class="col-lg-12"><h1 class="page-header"><?php echo $siteTitle; ?></h1></div></div>
	<div class="row"><div class="col-lg-12"><button type="button" class="btn btn-primary" onclick="editManager(this, 0);">+ 新建</button></div></div>
	<br />
	<div class="row">
		<div class="table-responsive" style="margin-left:10px;">
			<?php
				echo Table::widget([
					'aColumns' => [
						'nick_name' => ['title' => '姓名', 'class' => 'col-sm-4'],
						'account' => ['title' => '账号', 'class' => 'col-sm-3'],
						'group_id' => [
							'title' => '角色',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return $aData['group_info'] ? $aData['group_info']['name'] : '无';
							}
						],
						'is_forbidden' => [
							'title' => '是否禁用',
							'class' => 'col-sm-1',
							'content' => function($aData){
								return $aData['is_forbidden'] ? '是' : '否';
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
								return '<a href="javascript:;" onclick="editManager(this, ' . $aData['id'] . ');">编辑</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="javascript:;" onclick="deleteManager(this, ' . $aData['id'] . ');">删除</a>';
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
	var aManagerList = <?php echo json_encode($aList); ?>;
	
	function buildManagerBoxHtml(aData){
		var html = '';
		html += '<div class="form-group">';
			html += '<label>姓名</label>';
			html += '<input class="J-cp-nickName form-control" placeholder="" value="' + (typeof(aData.nick_name) != 'undefined' ? aData.nick_name : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>账号</label>';
			html += '<input class="J-cp-account form-control" placeholder="" value="' + (typeof(aData.account) != 'undefined' ? aData.account : '') + '" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>角色</label>';
			html += '<select class="J-cp-groupId form-control"><option value="0">请选择</option><?php foreach($aManagerGroupList as $aManagerGroup){ ?><option value="<?php echo $aManagerGroup['id']; ?>"><?php echo $aManagerGroup['name']; ?></option><?php } ?></select>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>是否禁用</label>';
			html += '<select class="J-cp-isForbidden form-control"><option value="0">否</option><option value="1">是</option></select>';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>密码</label>';
			html += '<input type="password" class="J-cp-password form-control" placeholder="" value="" />';
		html += '</div>';
		html += '<div class="form-group">';
			html += '<label>确认密码</label>';
			html += '<input type="password" class="J-cp-enpassword form-control" placeholder="" value="" />';
		html += '</div>';
		
		return html;
	}
	
	function editManager(o, id){
		id = parseInt(id);
		var aData = {};
		for(var i in aManagerList){
			if(aManagerList[i].id == id){
				aData = aManagerList[i];
				break;
			}
		}
		$.teninedialog({
			title : id == 0 ? '新增<?php echo $siteTitle; ?>' : '编辑<?php echo $siteTitle; ?>',
			content : buildManagerBoxHtml(aData),
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
				if(id){
					$('.J-cp-isForbidden').val(aData.is_forbidden);
					$('.J-cp-groupId').val(aData.group_id);
				}
				
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
						url : Tools.url('manage', 'manager/save'),
						data : {
							id : id,
							nickName : $('.J-cp-nickName').val(),
							account : $('.J-cp-account').val(),
							groupId : $('.J-cp-groupId').val(),
							password : $('.J-cp-password').val(),
							enpassword : $('.J-cp-enpassword').val(),
							isForbidden : $('.J-cp-isForbidden').val()
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

	function deleteManager(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('manage', 'manager/delete'),
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