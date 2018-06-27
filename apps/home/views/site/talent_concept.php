<?php
use umeworld\lib\Url;

$siteTitle = '招贤纳士';
$this->setTitle($siteTitle);

?>
<div class="banner_job">
	<div class="job_nav">
		<ul>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'site/zhaopin'); ?>">人才招聘</a></li>
			<li class="newshover"><a href="<?php echo Url::to(Yii::$app->id, 'site/talent-concept'); ?>" style="color:#005fd4;">人才理念</a></li>
		</ul>
	</div>
</div>
<div class="concept">
	<?php echo Yii::$app->siteSetting->talentConcept; ?>
   <div class="clear"></div>
</div>