<?php
require(__DIR__ . '/../../../common/config/config.php');
$oAppCreater = new \common\lib\AppCreater(['appId' => 'manage']);
$oAppCreater->createApp()->run();