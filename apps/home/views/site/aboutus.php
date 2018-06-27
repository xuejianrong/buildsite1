<?php
use umeworld\lib\Url;

$siteTitle = '关于我们';
$this->setTitle($siteTitle);

?>
<script language="javascript">
function ScrollImgLeft(){
	var speed=20
	var scroll_begin = document.getElementById("scroll_begin");
	var scroll_end = document.getElementById("scroll_end");
	var scroll_div = document.getElementById("scroll_div");
	scroll_end.innerHTML=scroll_begin.innerHTML
	function Marquee(){
		if(scroll_end.offsetWidth-scroll_div.scrollLeft<=0)
			scroll_div.scrollLeft-=scroll_begin.offsetWidth
		else
			scroll_div.scrollLeft++
	}
	var MyMar=setInterval(Marquee,speed)
	scroll_div.onmouseover=function() {clearInterval(MyMar)}
	scroll_div.onmouseout=function() {MyMar=setInterval(Marquee,speed)}
}
</script>

<div class="banner_about"></div>
<div class="about">
	<h2>Company profile</h2>
	<h1>公司简介</h1>
	<div class="aboutcon">
		<p><?php echo Yii::$app->siteSetting->aAboutSetting['companyProfile']; ?></p>
	</div>
  
	<div class="clear"></div>
</div>
<p class="pt40 tc"><?php echo Yii::$app->siteSetting->aAboutSetting['companyHistory']; ?></p>
<div class="product">
	<div id="scroll_div" class="scroll_div">
	<div id="scroll_begin">
		<ul>
		<?php foreach(Yii::$app->siteSetting->aAboutSetting['aCompanyCertificate'] as $companyCertificate){ ?>
			<li><a href="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $companyCertificate; ?>"  target="_blank"><img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $companyCertificate; ?>"/></a></li>
		<?php } ?>
		</ul>
	</div>
	<div id="scroll_end"></div>
	</div>
	<script type="text/javascript">ScrollImgLeft();</script>
</div>
<p class="pt40 tc"><?php echo Yii::$app->siteSetting->aAboutSetting['companyCulture']; ?></p>
<br />
<br />