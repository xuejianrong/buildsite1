<?php
namespace common\assets;

use Yii;

class DataConfigAsset extends \umeworld\lib\AssetBundle{
	public $js = [
		'@r.js.config.data',
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_HEAD,
	];
}