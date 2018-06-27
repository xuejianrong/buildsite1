<?php
use yii\helpers\Html;
$this->registerCssFile('@r.css.error');
$this->beginPage();
$this->setTitle('提示');
?>
<div class="error-container">
	<div class="title">
		<h2 style="color: #F60;display: inline-block; height: 40px; font-size: 40px; line-height: 40px;"><?php echo '提示'; ?></h2>
	</div>
	<div class="content">
		<h3>无权限访问</h3>
	</div>
	<div class="redirect">
		<?php
			$referer = Yii::$app->request->headers->get('referer');
			if($referer){
				echo Html::a('回上一页', $referer);
			}else{
				echo '<a href="javascript:(history.back())">回上一页</a>';
			}
		?>
		<a href="<?php echo \umeworld\lib\Url::to(Yii::$app->id, 'site/index'); ?>">回到首页</a>
	</div>
</div>
