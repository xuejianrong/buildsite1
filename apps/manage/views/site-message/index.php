<?php
use umeworld\lib\Url;
use manage\widgets\Table;
use yii\widgets\LinkPager;

$siteTitle = '在线留言';
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
						'contact_name' => ['title' => '联系姓名', 'class' => 'col-sm-1'],
						'tel' => ['title' => '电话', 'class' => 'col-sm-1'],
						'email' => ['title' => '邮箱', 'class' => 'col-sm-1'],
						'company_name' => ['title' => '联系单位', 'class' => 'col-sm-1'],
						'content' => ['title' => '需求描述', 'class' => 'col-sm-5'],
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
								return '<a href="javascript:;" onclick="deleteSiteMessage(this, \'' . $aData['id'] . '\');">删除</a>';
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
	
	function deleteSiteMessage(o, id){
		UBox.confirm('确定删除？', function(){
			if($(o).attr('disabled')){
				return;
			}
			ajax({
				url : Tools.url('manage', 'site-message/delete'),
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