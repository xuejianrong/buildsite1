<?php
use umeworld\lib\Url;
$this->registerCssFile(Yii::getAlias('@r.url') . '/css/scroll.css');

$siteTitle = '新闻详细';
$this->setTitle($siteTitle);

?>
<style type="text/css">
	#framework .newshover {
		background: #fff;
		color: #000;
	}
	#framework .newshover a{
		color:#005fd4;
	}
</style>
<div class="banner_news">
	<div class="news_nav">
		<ul>
		<?php foreach($aNewsCategoryList as $aNewsCategory){ ?>
			<li class="<?php echo $categoryId == $aNewsCategory['id'] ? 'newshover' : ''; ?>"><a href="<?php echo Url::to(Yii::$app->id, 'news/index', ['categoryId' => $aNewsCategory['id']]); ?>"><?php echo $aNewsCategory['name']; ?></a></li>
		<?php } ?>
		</ul>
	</div>
</div>
<div class="news">
 
  <h4><?php echo $aNews['title']; ?></h4>
 <div class="aboutcon">
    <?php echo $aNews['content']; ?>
  </div>
  <div class="newsbtn">
  <?php if($aNewsPre){ ?>
  <p>上一篇：<a href="<?php echo Url::to(Yii::$app->id, 'news/detail', ['id' => $aNewsPre['id']]); ?>"><?php echo $aNewsPre['title']; ?></a></p>
  <?php } ?>
   <?php if($aNewsNext){ ?>
  <p>下一篇：<a href="<?php echo Url::to(Yii::$app->id, 'news/detail', ['id' => $aNewsNext['id']]); ?>"><?php echo $aNewsNext['title']; ?></a></p>
   <?php } ?>
  </div>
  <div class="clear"></div>
</div>