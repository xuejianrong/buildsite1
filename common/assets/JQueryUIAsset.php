<?php
namespace common\assets;

/**
 * 提示信息组件
 */
class JQueryUIAsset extends \umeworld\lib\AssetBundle
{
    public $js = [
		'@r.js.jquery-ui',
		'@r.js.jquery-ui-datepicker-lang',
    ];

    public $css = [
    	'@r.css.jquery-ui',
    ];

	public $depends = [
		'common\assets\JQueryAsset',
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_HEAD,
	];
}
