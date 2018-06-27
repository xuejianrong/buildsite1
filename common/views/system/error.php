<?php
use yii\helpers\Html;
$this->registerCssFile('@r.css.error');
$statusCode = 1;
if(!($oException instanceof \ErrorException)){
	$statusCode = $oException->statusCode;
}

$this->beginPage();
?>
<!doctype html>
<html>
<head>
<title>Error</title>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="format-detection"content="telephone=no">
<?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
<div class="container">
	<div class="title">
		<h1>!</h1>
		<h2><?php echo 'Http ' . $statusCode; ?></h2>
	</div>
	<div class="content">
		<?php if($statusCode == 404){ ?>
			<h4>您可能输错了网址或您访问的页面已经被删除，如需帮助请联系客服人员，或返回请重新进行操作！</h4>
		<?php } ?>
	</div>
	<div class="redirect">
		<?php
			$referer = Yii::$app->request->headers->get('referer');
			if($referer){
				echo Html::a('回上一页', $referer);
			}
		?>
	</div>
</div>

<?php if(!YII_ENV_PROD): ?>
<div class="wrapDump">
(以下信息只会在调试或测试环境中输出): <br />
	<pre>
<?php print_r($oException->__toString());
	?>
	</pre>
	<h4>请求相关数据:</h4>
	<?php echo Yii::$app->errorHandler->renderRequest(); ?>
</div>
<?php endif;
$this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>