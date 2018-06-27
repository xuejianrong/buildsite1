<?php
use umeworld\lib\Url;
use yii\widgets\LinkPager;

$this->registerCssFile(Yii::getAlias('@r.css.pagination'));

$siteTitle = '新闻中心';
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
	<ul>
	<?php foreach($aNewsList as $aNews){ ?>
		<li>
			<em>
				<a href="<?php echo Url::to(Yii::$app->id, 'news/detail', ['id' => $aNews['id']]); ?>" title="<?php echo $aNews['title']; ?>">
					<img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $aNews['shortcut']; ?>" width="300" height="225" alt="<?php echo $aNews['title']; ?>" />
				</a>
			</em>
			<a href="<?php echo Url::to(Yii::$app->id, 'news/detail', ['id' => $aNews['id']]); ?>"><?php echo $aNews['title']; ?></a>
			<p><span class="news_number"><?php echo $aNews['click_count']; ?></span>发布时间：<?php echo date('Y-m-d', $aNews['publish_time']); ?></p>
		</li>
	<?php } ?>
   </ul>
   <div class="clear"></div>
</div>
<center><?php echo LinkPager::widget(['pagination' => $oPage]); ?></center>
