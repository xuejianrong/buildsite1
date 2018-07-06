<?php
use umeworld\lib\Url;
$this->registerCssFile(Yii::getAlias('@r.url') . '/css/scroll.css');

$siteTitle = '产品详细';
$this->setTitle($siteTitle);

?>
<div class="about">
	<div class="prowz">首页 > 产品中心 > <?php echo $productsCategory; ?> </div>
    <div class="pro_add fl">
    <h6><?php echo $aProducts['name']; ?></h6>
    <div class="proaddlist">
		<ul>
			<li>产品型号：	<?php echo $aProducts['product_model']; ?> </li>
			<li>原产地：	<?php echo $aProducts['produce_place']; ?></li> 
			<li>商标品牌：	<?php echo $aProducts['brand']; ?> </li>
			<li>参考价格：	<?php echo $aProducts['price']; ?></li> 
			<li>交货地点：	<?php echo $aProducts['delivery_address']; ?></li> 
			<li>是否提供样品：	<?php echo $aProducts['has_sample'] ? '是' : '否'; ?> </li>
		</ul>
		<div class="clear"></div>
	</div>
	<p><?php echo $aProducts['description']; ?></p></div>
	<div class="pro_img fr"><img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $aProducts['shortcut']; ?>" width="266" height="400" /></div>
	<div class="clear"></div>
    <div class="pro_detailed">
		<h5>产品介绍</h5>
        <div class="pro_con">
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<?php if($aProducts['other_info']){ ?>
				<?php for($i = 0; $i < count($aProducts['other_info']) + 2; $i += 2){ ?>
					<tr>
					<?php if(isset($aProducts['other_info'][$i])){ ?>
						<td width="15%" align="right"><?php echo $aProducts['other_info'][$i]['key']; ?>：</td>
						<td width="35%"><?php echo $aProducts['other_info'][$i]['value']; ?></td>
						<td width="15%" align="right"><?php echo isset($aProducts['other_info'][$i + 1]) ? $aProducts['other_info'][$i + 1]['key'] . '：' : ''; ?></td>
						<td width="35%"><?php echo isset($aProducts['other_info'][$i + 1]) ? $aProducts['other_info'][$i + 1]['value'] : ''; ?></td>
					<?php } ?>
					</tr>
				<?php } ?>
			<?php } ?>
			</table>
        </div>
    </div>
    <div class="clear"></div>
</div>
<?php if($aRelateProductsList){ ?>
<div class="productkind">
	<ul>
		<li class="hover">相关产品 </li>
   </ul>
   <div class="clear"></div>
</div>
<div class="product">
	<div class="scrollpic">
		<div id="myscroll">
			<ul>
			<?php foreach($aRelateProductsList as $aRelateProducts){ ?>
				<li>
					<i></i>
					<a href="<?php echo Url::to(Yii::$app->id, 'products/detail', ['id' => $aRelateProducts['id']]); ?>">
						<img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $aRelateProducts['shortcut']; ?>" width="260" height="355">
						<span class="intro">
							<h5><?php echo $aRelateProducts['name']; ?></h5>
							<p><?php echo $aRelateProducts['description']; ?></p>
						</span>
					</a>
				</li>
			<?php } ?>
			</ul>
		</div>
	</div>
	<div class="clear"></div>
</div>
<?php } ?>