<?php 
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->beginPage(); 
$mManager = Yii::$app->manager->getIdentity();
$aManager = [];
if($mManager){
	$aManager = $mManager->toArray();
	unset($aManager['password']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?php echo $this->title; ?></title>
	<?php $this->head(); ?>
	<script type="text/javascript">
		if(window.App && !App.inited){
			App.config({
				isGuest : <?php echo $mManager ? 0 : 1; ?>,
				url : {
					resource : '<?php echo Yii::getAlias('@r.url'); ?>'
				},
				oCurrentUser : <?php echo json_encode($aManager); ?>
			});
		}
	</script>
	<style type="text/css">
		.table-responsive .table {
			font-size: 14px;
		}
	</style>
</head>
<body style="background:#ffffff;">
	<?php echo \manage\widgets\Navi::widget(); ?>
	<?php $this->beginBody(); ?>
	<div id="wrapper">
		<div id="page-wrapper" style="overflow-x: auto;">
			<div id="framework"><?php echo $content; ?></div>
		</div>
	</div>
	<footer style="height:50px;width:100%;background:#3d4a5d;position: relative;z-index:10000;"></footer>
	<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();