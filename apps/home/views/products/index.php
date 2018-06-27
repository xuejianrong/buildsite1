<?php
use umeworld\lib\Url;
use yii\widgets\LinkPager;

$this->registerCssFile(Yii::getAlias('@r.url') . '/css/scroll.css');
$this->registerCssFile(Yii::getAlias('@r.css.pagination'));

$siteTitle = '产品展示';
$this->setTitle($siteTitle);

?>
<div class="banner"></div>
<div class="mynav">
	<ul>
	<?php foreach($aProductsCategoryList as $aProductsCategory){ ?>
		<li><a href="products_list.html" title="<?php echo $aProductsCategory['name']; ?>"><img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $aProductsCategory['shortcut']; ?>" alt="<?php echo $aProductsCategory['name']; ?>" /></a></li>
	<?php } ?>
	</ul>
  <div class="clear"></div>
</div>
<div class="product">
	<div class="scrollpic">
		<div id="myscroll">
			<ul>
			<?php foreach($aProductsList as $aProducts){ ?>
				<li>
					<a href="<?php echo Url::to(Yii::$app->id, 'products/detail', ['id' => $aProducts['id']]); ?>"><i></i>
						<img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $aProducts['shortcut']; ?>" width="200" height="305">
						<span class="intro">
							<h5><?php echo $aProducts['name']; ?></h5>
							<p><?php echo $aProducts['description']; ?></p>
						</span>
					</a>
				</li>
			<?php } ?>
			</ul>
		</div>
	</div>
</div>
<center><?php echo LinkPager::widget(['pagination' => $oPage]); ?></center>
