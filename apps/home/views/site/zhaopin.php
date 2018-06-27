<?php
use umeworld\lib\Url;

$siteTitle = '招贤纳士';
$this->setTitle($siteTitle);

?>
<div class="banner_job">
	<div class="job_nav">
		<ul>
			<li class="newshover"><a href="<?php echo Url::to(Yii::$app->id, 'site/zhaopin'); ?>" style="color:#005fd4;">人才招聘</a></li>
			<li><a href="<?php echo Url::to(Yii::$app->id, 'site/talent-concept'); ?>">人才理念</a></li>
		</ul>
	</div>
</div>
<div class="about">
	<div class="hr">有意来我司面试的求职者可以发送简历到人事部的邮箱:zhhema@163.com ,人事部尽快安排面试时间，或拔打人事部电话咨询0756－3882261。</div>
	<div class="job">
		<ul>
		<?php foreach(Yii::$app->siteSetting->aZhaopinList as $aZhaopin){ ?>
			<li>
				<div class="job_title">招聘职位：<?php echo $aZhaopin['position']; ?></div>
				<div class="job_kind"><span>人数：<?php echo $aZhaopin['count']; ?></span><span>工作地点：<?php echo $aZhaopin['workplace']; ?></span><span>学历要求：<?php echo $aZhaopin['qualifications']; ?></span><span>工作经验：<?php echo $aZhaopin['expirence']; ?></span><span>发布时间：<?php echo $aZhaopin['publishTime']; ?></span></div>
				<div class="job_con"><?php echo $aZhaopin['description']; ?></div>
			</li>
		<?php } ?>	
		</ul>
	</div>
	<div class="clear"></div>
</div>