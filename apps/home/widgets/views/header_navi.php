<?php 
use umeworld\lib\Url;
?>
<div class="header">
	<div class="logo fl"><img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo Yii::$app->siteSetting->aBaseSetting['siteLogo']; ?>"  /></div>
	<div class="menu fr">
		<ul>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'site/index'); ?>">首页<p>Home</p></a></li>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'site/aboutus'); ?>">关于我们<p>About us</p></a></li>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'products/index'); ?>">产品展示<p>Products</p></a></li>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'news/index'); ?>">新闻中心<p>News</p></a></li>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'site/zhaopin'); ?>">招贤纳士<p>Recruiting</p></a></li>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'site/contactus'); ?>">联系我们<p>contacts</p></a></li>
		</ul>
	</div>
</div>