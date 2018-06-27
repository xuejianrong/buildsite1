<?php 
use umeworld\lib\Url;
?>
<div class="footer">
   <div class="footmain">
    <div class="foot_menu fl">
       <dl>
        <dt>关于我们</dt>
        <dd><a href="<?php echo Url::to(Yii::$app->id, 'site/aboutus'); ?>">公司简介</a></dd>
        <dd><a href="<?php echo Url::to(Yii::$app->id, 'site/aboutus'); ?>">公司历程</a></dd>
        <dd><a href="<?php echo Url::to(Yii::$app->id, 'site/aboutus'); ?>">资质证书</a></dd>
        <dd><a href="<?php echo Url::to(Yii::$app->id, 'site/aboutus'); ?>">企业文化</a></dd>
       </dl>
       <dl>
        <dt>产品展示</dt>
		<?php foreach($aProductsCategoryList as $aProductsCategory){ ?>
        <dd><a href="<?php echo Url::to(Yii::$app->id, 'products/index', ['categoryId' => $aProductsCategory['id']]); ?>"><?php echo $aProductsCategory['name']; ?></a></dd>
        <?php } ?>
       </dl>
       <dl>
        <dt>新闻中心</dt>
		<?php foreach($aNewsCategoryList as $aNewsCategory){ ?>
        <dd><a href="<?php echo Url::to(Yii::$app->id, 'news/index', ['categoryId' => $aNewsCategory['id']]); ?>"><?php echo $aNewsCategory['name']; ?></a></dd>
        <?php } ?>
       </dl>
       <dl>
       <dt>招贤纳士</dt>
       <dd><a href="<?php echo Url::to(Yii::$app->id, 'site/zhaopin'); ?>">人才招聘</a></dd>
       <dd><a href="<?php echo Url::to(Yii::$app->id, 'site/talent-concept'); ?>">人才理念</a></dd>
       </dl>
       <dl>
       <dt>联系我们</dt>
       <dd><a href="<?php echo Url::to(Yii::$app->id, 'site/contactus'); ?>">联系信息</a></dd>
       <dd><a href="<?php echo Url::to(Yii::$app->id, 'site-message/index'); ?>">在线留言</a></dd>
       </dl>
        <div class="clear"></div>
    </div>
    <div class="contact fr">
       <h3>服务热线：<?php echo Yii::$app->siteSetting->aContactusSetting['hotsPhone']; ?></h3>
       <ul>
       <li>电话：<?php echo Yii::$app->siteSetting->aContactusSetting['phone']; ?> </li>
       <li>地址：<?php echo Yii::$app->siteSetting->aContactusSetting['address']; ?></li>
       <li>售后服务电话：<?php echo Yii::$app->siteSetting->aContactusSetting['servicePhone']; ?></li>
       <li>服务邮箱：<?php echo Yii::$app->siteSetting->aContactusSetting['email']; ?></li>
       </ul>
    </div>
    <div class="clear"></div>
</div>
</div>
<div class="copyright"><?php echo Yii::$app->siteSetting->aBaseSetting['siteCopyright']; ?></div>