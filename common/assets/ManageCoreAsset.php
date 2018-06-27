<?php
namespace common\assets;

/**
 * 核心资源包,主要含ajax和date函数(和PHP用法一样,实现了常用参数支持),App组件和Component这个带事件的组件
 */
class ManageCoreAsset extends \umeworld\lib\AssetBundle
{
    public $js = [
		'@r.js.core',
		'@r.js.ui',
		'@r.js.tools',
		'@r.js.content.viewer',
    ];

    public $css = [
    	'@r.css.ui',
    ];

	public $depends = [
		'common\assets\UBoxAsset',
		'common\assets\JQueryAsset',
		'common\assets\DataConfigAsset',
		'common\assets\BootstrapAsset',
	];

	public $jsOptions = [
		'position' => \yii\web\View::POS_HEAD,
	];
}
