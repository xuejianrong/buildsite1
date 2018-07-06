<?php
use umeworld\lib\Url;
$this->registerCssFile(Yii::getAlias('@r.url') . '/css/scroll.css');

$siteTitle = '医疗行业网站建设_企业网站建设_官方网站建设';
$this->setTitle($siteTitle);

?>
<div class="banner"></div>
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
<div class="product">
	<h2>Star Product</h2>
	<h1>明星产品</h1>
	<div class="scrollpic pt40">
	<div id="mybtns">
		<a href="javascript:;" id="right"></a>
		<a href="javascript:;" id="left"></a>
	</div>
	<div id="myscroll">
		<div id="myscrollbox">
			<ul>
			<?php foreach($aProductsList as $aProducts){ ?>
				<li>
					<i></i>
					<a href="<?php echo Url::to(Yii::$app->id, 'products/detail', ['id' => $aProducts['id']]); ?>">
						<img src="<?php echo Yii::getAlias('@r.url'); ?>/<?php echo $aProducts['shortcut']; ?>" width="260" height="355">
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
	<div class="clear"></div>
</div>
<div class="news">
	<h2>News Center</h2>
	<h1>新闻中心</h1>
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
<script type="text/javascript">
$(document).ready(function() {
	  var blw=$("#myscrollbox li").width();
	  //获取单个子元素所需宽度
	  var liArr = $("#myscrollbox ul").children("li");
	  //获取子元素数量
	  var mysw = $("#myscroll").width();
	  //获取子元素所在区域宽度
	  var mus = parseInt(mysw/blw);
	  //计算出需要显示的子元素的数量
	  var length = liArr.length-mus;
	  //计算子元素可移动次数（被隐藏的子元素数量）
	  var i=0
	  $("#right").click(function(){
		  i++
		  //点击i加1
		  if(i<length){
		      $("#myscrollbox").css("left",-(blw*i));
			  //子元素集合向左移动，距离为子元素的宽度乘以i。
		  }else{
			  i=length;
			  $("#myscrollbox").css("left",-(blw*length));
			  //超出可移动范围后点击不再移动。最后几个隐藏的元素显示时i数值固定位已经移走的子元素数量。
	      }
      });
	  $("#left").click(function(){
		  i--
		  //点击i减1
		  if(i>=0){
		     $("#myscrollbox").css("left",-(blw*i));
			 //子元素集合向右移动，距离为子元素的宽度乘以i。
		  }else{
			 i=0;
			 $("#myscrollbox").css("left",0);
			 //超出可移动范围后点击不再移动。最前几个子元素被显示时i为0。
	      }
      });
});
</script>