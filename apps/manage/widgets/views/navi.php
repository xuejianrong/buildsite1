<?php 
use umeworld\lib\Url;

$controllerId = Yii::$app->controller->id;
$actionId = Yii::$app->controller->action->id;
?>
<style type="text/css">
	#J-navbar-brand{color:#ffffff;}
	#J-navbar-brand:hover{color:#d8dbdf;}
	#J-navbar-right-top-nav:hover{background:#374354;}
	.J-side-menu{position: fixed;top: 51px;left: 225px;width: 225px;margin-left: -225px;border: none;border-radius: 0;overflow-y: auto;}
	.J-side-menu li{position: relative;display: block;float: left;margin-left: -40px;}
	.J-top-child a{outline:none;text-decoration:none;position: relative;padding: 10px 15px;line-height: 20px;display: block;padding-top: 15px;padding-bottom: 15px;width: 225px;}
	.J-top-child a:hover{color:#1f2226;}
	.J-top-child .item-normal{color:#636c7b;background:#edf0f1;border-bottom:1px solid #dfe4e6;border-right:1px solid #dfe4e6;}
	.J-top-child .active{background:none;border-bottom:1px solid #dfe4e6;color:#1f2226;border-right:none;}
	.J-child-ul{display:none;width: 225px;}
	.J-child-ul li{float:left;width: 225px;height:37px;line-height:37px;}
	.J-child-ul li a{padding:0px;padding-left:30px;color:#636c7b;background:#edf0f1;height:37px;line-height:37px;border-bottom:1px solid #dfe4e6;border-right:1px solid #dfe4e6;outline:none;}
	.J-child-ul li a.active{border-right:none;}
</style>
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<a id="J-navbar-brand" class="navbar-brand" href="<?php echo Url::to(Yii::$app->id, 'site/index'); ?>">建站后台管理系统</a>
	</div>
	<!-- Top Menu Items -->
	<ul class="J-navbar-right-top-nav nav navbar-right top-nav">
		<li class="J-dropdown dropdown">
			<a id="J-navbar-right-top-nav" href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" style="display: block;color: #ffffff;"><i class="fa fa-user"></i> <?php echo $aManager['nick_name']; ?> <b class="caret"></b></a>
			<ul class="dropdown-menu" style="min-width: 125px;">
				<li>
					<a href="<?php echo Url::to('home', 'site/index'); ?>"><i class="fa fa-fw fa-home"></i> 返回前端</a>
				</li>
				<li>
					<a href="<?php echo Url::to(Yii::$app->id, 'site/logout'); ?>"><i class="fa fa-fw fa-power-off"></i> 退出登录</a>
				</li>
			</ul>
		</li>
	</ul>
	<!-- Sidebar Menu Items - These collapse to the responsive navigation menu on small screens -->
	<div class="collapse navbar-collapse navbar-ex1-collapse" style="background: #3d4a5d;border-color: #3d4a5d;">
		<ul class="J-side-menu" style="background:#fafafa;padding-bottom:100px;z-index:1;">
		<?php foreach($aMenuConfig as $aMenu){ ?>
			<li class="J-top-child">
				<a href="<?php echo $aMenu['url'] == '#' ? 'javascript:;' : Url::to(Yii::$app->id, $aMenu['url'][0]); ?>" class="J-memu-left-item-top J-memu-left-item item-normal <?php echo $aMenu['is_current'] ? 'active' : ''; ?>"><i class="fa fa-fw <?php echo $aMenu['icon_class']; ?>"></i> <?php echo $aMenu['title']; ?></a>
			<?php if($aMenu['child']){ ?>
				<ul class="J-child-ul" style="<?php echo $aMenu['show_child'] ? 'display:block;' : ''; ?>">
				<?php foreach($aMenu['child'] as $child){ ?>
					<li><a href="<?php echo $child['url'] == '#' ? 'javascript:;' : Url::to(Yii::$app->id, $child['url'][0]); ?>" class="J-memu-left-item <?php echo $child['is_current'] ? 'active' : ''; ?>"><i class="fa fa-fw <?php echo $child['icon_class']; ?>"></i> <?php echo $child['title']; ?></a></li>
				<?php } ?>
				</ul>
			<?php } ?>
			</li>
		<?php } ?>
		</ul>
	</div>
	<!-- /.navbar-collapse -->
</nav>
<script type="text/javascript">	
	$(function(){
		var documentHeight = $(document).height();
		$('#page-wrapper').css({"min-height": (documentHeight - 100) + 'px'});
		$('.J-side-menu').css({'max-height': documentHeight - 50});
		$('.J-side-menu').css({'min-height': $(window).height() - 50});
		$('.J-side-menu').css({'overflow-x': 'hidden'});
		
		$('.J-memu-left-item').click(function(){
			$('.J-memu-left-item-top').removeClass('active');
			$(this).addClass('active');
			if(!$(this).parent().parent().hasClass('J-child-ul') && !$(this).parent().parent().is(':hidden')){
				if($(this).parent().find('.J-child-ul').length != 0 && !$(this).parent().find('.J-child-ul').is(':hidden')){
					$(this).removeClass('active');
				}
				$('.J-side-menu').find('.J-child-ul').slideUp(200);
			}
			
			if($(this).parent().find('.J-child-ul').length != 0 && $(this).parent().find('.J-child-ul').is(':hidden')){
				$(this).parent().find('.J-child-ul').slideDown(200);
			}
			if($(this).next().length != 0 && $(this).next().find('.active').length != 0){
				$(this).removeClass('active');
			}
		});
	});
</script>