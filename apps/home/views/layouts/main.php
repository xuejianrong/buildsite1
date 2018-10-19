<?php 
use common\model\Setting;
$this->registerAssetBundle('common\assets\CoreAsset');
$this->registerCssFile(Yii::getAlias('@r.url') . '/css/style.css');
$this->beginPage(); 
$mUser = Yii::$app->user->getIdentity();
$aUser = [];
if($mUser){
	$aUser = $mUser->toArray();
	unset($aUser['password']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?php echo $this->title; ?></title>
	<meta name="keywords" content="<?php echo Yii::$app->siteSetting->aBaseSetting['siteSeoKeywords']; ?>" />
	<meta name="description" content="<?php echo Yii::$app->siteSetting->aBaseSetting['siteSeoDescription']; ?>" />
	<?php $this->head(); ?>
	<script type="text/javascript">
		if(window.App && !App.inited){
			App.config({
				appid : '<?php echo Yii::$app->id; ?>',
				isGuest : <?php echo $mUser ? 0 : 1; ?>,
				url : {
					resource : '<?php echo Yii::getAlias('@r.url'); ?>'
				},
				oCurrentUser : <?php echo json_encode($aUser); ?>
			});
		}
	</script>
</head>
<body>
	<?php $this->beginBody(); ?>
	<?php echo \home\widgets\HeaderNav::widget(); ?>
	<div id="framework"><?php echo $content; ?></div>
	<?php echo \home\widgets\FooterNav::widget(); ?>
	<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();
