<?php
/**
 * URL配置控制
 * class : 解析器
 * enablePrettyUrl : 是否开启伪静态
 * showScriptName : 生成的URL是否带入口脚本名称
 * enableStrictParsing : 是否开启严格匹配
 * baseUrl 域名
 */
return [
	'class' => 'yii\web\UrlManager',
	'enablePrettyUrl' => true,
	'showScriptName' => false,
	'enableStrictParsing' => true,
	'baseUrl' => Yii::getAlias('@url.home'),
	'rules' => [
		
		'about.html'											=> 'site/aboutus',
		'contactus.html'										=> 'site/contactus',
		'build-site.html'										=> 'site/build-site',
		'zhaopin.html'											=> 'site/zhaopin',
		'talent-concept.html'									=> 'site/talent-concept',
		
		
		
		'products/index.html'									=> 'products/index',
		'products/index-<categoryId:.*>.html'					=> 'products/index',
		'products/index-<page:.*>-<perpage:.*>.html'			=> 'products/index',
		'products/index-<page:.*>-<perpage:.*>-<categoryId:.*>.html'=> 'products/index',
		'products/detail/<id:.*>.html'							=> 'products/detail',
		
		'news/index.html'										=> 'news/index',
		'news/index-<categoryId:.*>.html'						=> 'news/index',
		'news/index-<page:.*>-<perpage:.*>.html'				=> 'news/index',
		'news/index-<page:.*>-<perpage:.*>-<categoryId:.*>.html'=> 'news/index',
		'news/detail/<id:.*>.html'								=> 'news/detail',
		
		
		'site-template/index.html'								=> 'site-template/index',
		
		
		'site-message/index.html'								=> 'site-message/index',
		'site-message/add.json'									=> 'site-message/add',
		
		
		//这一条要在最下面 
		''														=> 'site/index',
		'<lang:.*>'												=> 'site/index',
		
		
		
	],
];