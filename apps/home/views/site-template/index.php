<?php
use umeworld\lib\Url;

$siteTitle = '网站模版';
$this->setTitle($siteTitle);

?>
<div class="content">
	<div class="case">
		<h1>网站模版</h1>
		<h2>高效、实用、简单的建站，满足您所有的建站需求</h2>
		<ul>
		<?php foreach(Yii::$app->siteSetting->aSiteTemplate as $value){ ?>
			<li><img src="<?php echo Yii::getAlias('@r.url') . '/' . $value['shortcut']; ?>" alt="" /><p><?php echo $value['name']; ?>：<a href="<?php echo $value['link']; ?>" target="_blank">预览</a><span class="jiage">价格：<?php echo $value['price']; ?>元</span></p></li>
		<?php } ?>
		</ul>
	</div>
	<div class="cb"></div>
</div>
<div class="cb"></div>