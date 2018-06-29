<?php
use umeworld\lib\Url;
use yii\widgets\LinkPager;

$this->registerCssFile(Yii::getAlias('@r.url') . '/css/scroll.css');
$this->registerCssFile(Yii::getAlias('@r.css.pagination'));

$siteTitle = '产品展示';
$this->setTitle($siteTitle);
$i = 0;
?>
<div class="banner"></div>
<?php if($aRootProductsCategory){ ?>
<div class="about">
	<h2><?php echo $aRootProductsCategory['ename']; ?></h2>
	<h1><?php echo $aRootProductsCategory['name']; ?></h1>
</div>
<div class="productkind">
	<ul>
	<?php foreach($aProductsCategoryList as $aProductsCategory){ ?>
		<?php if($aProductsCategory['pid'] == $aRootProductsCategory['id']){ ?>
		<li class="<?php echo $activeCategoryId == $aProductsCategory['id'] ? 'hover' : ''; ?>"><a href="<?php echo Url::to(Yii::$app->id, 'products/index', ['categoryId' => $aProductsCategory['id']]); ?>"><?php echo $aProductsCategory['name']; ?> </a></li>
		<?php $i++;} ?>
	<?php } ?>
	</ul>
	<div class="clear"></div>
</div>
<?php }else{ ?>
<div class="mynav">
	<ul>
	<?php foreach($aProductsCategoryList as $aProductsCategory){ ?>
		<?php if(!$aProductsCategory['pid']){ ?>
		<li><a href="<?php echo Url::to(Yii::$app->id, 'products/index', ['categoryId' => $aProductsCategory['id']]); ?>" title="<?php echo $aProductsCategory['name']; ?>"><img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $aProductsCategory['shortcut']; ?>" alt="<?php echo $aProductsCategory['name']; ?>" /></a></li>
		<?php } ?>
	<?php } ?>
	</ul>
  <div class="clear"></div>
</div>
<?php } ?>
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
