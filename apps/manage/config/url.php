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
	'baseUrl' => Yii::getAlias('@url.manage'),
	'rules' => [
		'backend.html'											=> 'site/index',
		'<lang:.*>backend.html'									=> 'site/index',
		
		'backend/captcha.html'									=> 'site/captcha',
		'backend/login.html'									=> 'site/show-login',
		'backend/login.json'									=> 'site/login',
		'backend/logout.html'									=> 'site/logout',
		
		
		'backend/upload/upload-image.json'						=> 'upload/upload-image',
		
		'backend/site-setting/index.html'						=> 'site-setting/index',
		'backend/site-setting/save.json'						=> 'site-setting/save',
		'backend/site-setting/friends-link.html'				=> 'site-setting/friends-link',
		'backend/site-setting/save-friends-link.json'			=> 'site-setting/save-friends-link',
		'backend/site-setting/delete-friends-link.json'			=> 'site-setting/delete-friends-link',
		'backend/site-setting/cooperative-partners.html'		=> 'site-setting/cooperative-partners',
		'backend/site-setting/save-cooperative-partners.json'	=> 'site-setting/save-cooperative-partners',
		'backend/site-setting/delete-cooperative-partners.json'	=> 'site-setting/delete-cooperative-partners',
		
		
		'backend/contactus/index.html'							=> 'contactus/index',
		'backend/contactus/save.json'							=> 'contactus/save',
		
		
		'backend/aboutus/index.html'							=> 'aboutus/index',
		'backend/aboutus/save.json'								=> 'aboutus/save',
		
		
		'backend/content-item/save.json'						=> 'content-item/save',
		'backend/content-item/delete.json'						=> 'content-item/delete',
		
		
		'backend/news-category/index.html'						=> 'news-category/index',
		'backend/news-category/save.json'						=> 'news-category/save',
		'backend/news-category/delete.json'						=> 'news-category/delete',
		
		
		
		'backend/news/index.html'								=> 'news/index',
		'backend/news/save.json'								=> 'news/save',
		'backend/news/delete.json'								=> 'news/delete',
		
		
		'backend/products-category/index.html'					=> 'products-category/index',
		'backend/products-category/save.json'					=> 'products-category/save',
		'backend/products-category/delete.json'					=> 'products-category/delete',
		
		
		
		'backend/products/index.html'							=> 'products/index',
		'backend/products/save.json'							=> 'products/save',
		'backend/products/delete.json'							=> 'products/delete',
		
		
		'backend/site-message/index.html'						=> 'site-message/index',
		'backend/site-message/delete.json'						=> 'site-message/delete',
		
		
		'backend/zhaopin/index.html'							=> 'zhaopin/index',
		'backend/zhaopin/save.json'								=> 'zhaopin/save',
		'backend/zhaopin/delete.json'							=> 'zhaopin/delete',
		'backend/zhaopin/talent-concept.html'					=> 'zhaopin/talent-concept',
		'backend/zhaopin/save-talent-concept.json'				=> 'zhaopin/save-talent-concept',
		
		
		'backend/manager/index.html'							=> 'manager/index',
		'backend/manager/save.json'								=> 'manager/save',
		'backend/manager/update.json'							=> 'manager/update',
		'backend/manager/delete.json'							=> 'manager/delete',
		
		
		'backend/manager-group/index.html'						=> 'manager-group/index',
		'backend/manager-group/save.json'						=> 'manager-group/save',
		'backend/manager-group/update.json'						=> 'manager-group/update',
		'backend/manager-group/delete.json'						=> 'manager-group/delete',
		'backend/manager-group/edit-actions.html'				=> 'manager-group/edit-actions',
		'backend/manager-group/save-actions.json'				=> 'manager-group/save-actions',
		
		
	],
];