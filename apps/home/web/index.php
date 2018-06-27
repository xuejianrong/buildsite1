<?php
require(__DIR__ . '/../../../config-local.php');
$oAppCreater = new \common\lib\AppCreater(['appId' => 'home']);
$oAppCreater->createApp()->run();