<?php
return [
	'manager_group_actions_list' => [
		[
			'title' => '后台管理',
			'action_list' => [
				'manager/index' => '查看后台用户',
				'manager/save' => '编辑后台用户',
				'manager/update' => '更新后台用户',
				'manager/delete' => '删除后台用户',
				'manager-group/index' => '查看用户角色',
				'manager-group/save' => '编辑用户角色',
				'manager-group/update' => '更新用户角色',
				'manager-group/delete' => '删除用户角色',
				'manager-group/edit-actions' => '查看角色权限',
				'manager-group/save-actions' => '编辑角色权限',
			],
		],
		[
			'title' => '网站设置',
			'action_list' => [
				'site-setting/index' => '查看基本设置',
				'site-setting/save' => '编辑基本设置',
				/*'site-setting/friends-link' => '查看友情链接',
				'site-setting/save-friends-link' => '编辑友情链接',
				'site-setting/delete-friends-link' => '删除友情链接',
				'site-setting/cooperative-partners' => '查看合作伙伴',
				'site-setting/save-cooperative-partners' => '编辑合作伙伴',
				'site-setting/delete-cooperative-partners' => '删除合作伙伴',*/
			],
		],
		[
			'title' => '产品管理',
			'action_list' => [
				'products-category/index' => '查看产品分类',
				'products-category/save' => '编辑产品分类',
				'products-category/delete' => '删除产品分类',
				'products/index' => '查看产品',
				'products/save' => '编辑产品',
				'products/delete' => '删除产品',
			],
		],
		[
			'title' => '新闻管理',
			'action_list' => [
				'news-category/index' => '查看新闻分类',
				'news-category/save' => '编辑新闻分类',
				'news-category/delete' => '删除新闻分类',
				'news/index' => '查看新闻',
				'news/save' => '编辑新闻',
				'news/delete' => '删除新闻',
			],
		],
		[
			'title' => '招贤纳士',
			'action_list' => [
				'zhaopin/index' => '查看招聘信息',
				'zhaopin/save' => '编辑招聘信息',
				'zhaopin/delete' => '删除招聘信息',
				'zhaopin/talent-concept' => '查看人才理念',
				'zhaopin/save-talent-concept' => '编辑人才理念',
			],
		],
		/*[
			'title' => '内容管理',
			'action_list' => [
				'customer-case/index' => '查看客户案例',
				'content-item/save' => '编辑内容',
				'content-item/delete' => '删除内容',
				'site-template/index' => '查看网站模板',
				'site-template/save' => '编辑网站模板',
				'site-template/delete' => '删除网站模板',
			],
		],*/
		[
			'title' => '联系我们',
			'action_list' => [
				'contactus/index' => '查看联系我们',
				'contactus/save' => '编辑联系我们',
			],
		],
		[
			'title' => '关于我们',
			'action_list' => [
				'aboutus/index' => '查看关于我们',
				'aboutus/save' => '编辑关于我们',
			],
		],
		[
			'title' => '在线留言',
			'action_list' => [
				'site-message/index' => '查看在线留言',
				'site-message/delete' => '删除在线留言',
			],
		],
		[
			'title' => '其它',
			'action_list' => [
				'upload/upload-image' => '上传图片',
			],
		],
	],
];
