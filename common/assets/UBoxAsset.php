<?php
namespace common\assets;

/**
 * UBox插件资源包
 */
class UBoxAsset extends \umeworld\lib\AssetBundle
{

    public $css = [
		'@r.css.ubox',
    ];

    public $js = [
		'@r.js.ubox',
    ];

	public $depends = [
		'common\assets\JQueryAsset',
	];
}
