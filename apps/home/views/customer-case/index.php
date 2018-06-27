<?php
use umeworld\lib\Url;
use yii\widgets\LinkPager;

$this->registerCssFile(Yii::getAlias('@r.css.pagination'));

$siteTitle = '客户案例';
$this->setTitle($siteTitle);

?>
<div class="buze">
	<div class="content">
		<div class="case">
			<h1>精品案例</h1>
			<h2>高效、实用、简单的建站，满足您所有的建站需求</h2>
			<ul>
			<?php foreach(Yii::$app->siteSetting->aSiteTemplate as $value){ ?>
				<li><img src="<?php echo Yii::getAlias('@r.url') . '/' . $value['shortcut']; ?>" alt="" /><p><?php echo $value['name']; ?>：<a href="<?php echo $value['link']; ?>" target="_blank">预览</a><span class="jiage">价格：<?php echo $value['price']; ?>元</span></p></li>
			<?php } ?>
			</ul>
		</div>
		<div class="cb"></div>
	</div>
</div>
<div class="cb"></div>
<center><?php echo LinkPager::widget(['pagination' => $oPage]); ?></center>