<?php
use umeworld\lib\Url;

$siteTitle = '客户案例详细';
$this->setTitle($siteTitle);

?>
<div class="content">
	<div class="service" style="margin:0 0 50px 0;">
		<div class="news_title"><?php echo $aCustomerCase['title']; ?></div>
		<div class="news_time">来源：<?php echo $aCustomerCase['source']; ?> &nbsp;&nbsp; 发布时间：<?php echo date('Y/m/d H:i:s', $aCustomerCase['create_time']); ?></div>
		<div class="news_detail"><?php echo $aCustomerCase['content']; ?></div>
		<div class="cb"></div>
	</div>
	<div class="cb"></div> 
</div>
<div class="cb"></div>