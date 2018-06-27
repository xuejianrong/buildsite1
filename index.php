<?php
if(strpos($_SERVER['REQUEST_URI'], 'backend') === false){
	require 'apps/home/web/index.php';
}else{
	require 'apps/manage/web/index.php';
}
exit;
