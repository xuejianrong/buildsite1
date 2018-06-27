<?php 
$this->registerAssetBundle('common\assets\ManageCoreAsset');
$this->registerCssFile('@r.url' . '/css/ace.min.css');
$this->beginPage(); 
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
			App.config({});
		}
	</script>
</head>
<body class="login-layout light-login" style="margin:0px;">
<div class="main-container">
    <div class="main-content">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
                <div class="login-container">
                    <div class="center">
                        <h1>
                            <span class="red">建站后台管理系统</span>
                        </h1>
                    </div>

                    <div class="space-6"></div>

                    <div class="position-relative">
                        <div id="login-box" class="login-box visible widget-box no-border">
                            <div class="widget-body">
                                <div class="widget-main">
                                    <h4 class="header blue lighter bigger">
                                        <i class="ace-icon fa fa-coffee green"></i>
                                        请输入您的登录信息
                                    </h4>
                                    <div class="space-6"></div>
                                    <!--登录表单-->
                                    <?= $content ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage();