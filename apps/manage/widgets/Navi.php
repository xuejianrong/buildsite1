<?php
namespace manage\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Inflector;
use ReflectionClass;

class Navi extends Widget{
	public function run(){
		$mManager = Yii::$app->manager->getIdentity();
		$aManager = [];
		if($mManager){
			$aManager = $mManager->toArray();
		}
		$controllerId = Yii::$app->controller->id;
		$actionId = Yii::$app->controller->action->id;
		$router = $controllerId . '/' . $actionId;
		
		$aMenuConfig = [
			[
				'title' => '后台管理',
				'en_title' => 'backend_manage',
				'url' => '#',
				'icon_class' => 'fa-cogs',	
				'child' => [
					[
						'title' => '后台用户',
						'en_title' => 'manager_user',
						'url' => ['manager/index'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '角色管理',
						'en_title' => 'role_manager',
						'url' => ['manager-group/index'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '权限管理',
						'en_title' => 'auth_manager',
						'url' => ['manager-group/edit-actions'],
						'icon_class' => 'fa-caret-right',	
					],
				],
			],
			[
				'title' => '网站设置',
				'en_title' => 'site_setting',
				'url' => '#',
				'icon_class' => 'fa-cog',	
				'child' => [
					[
						'title' => '基本设置',
						'en_title' => 'site_base_setting',
						'url' => ['site-setting/index'],
						'icon_class' => 'fa-caret-right',	
					],
					/*[
						'title' => '友情链接',
						'en_title' => 'friends_link',
						'url' => ['site-setting/friends-link'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '合作伙伴',
						'en_title' => 'cooperative_partners',
						'url' => ['site-setting/cooperative-partners'],
						'icon_class' => 'fa-caret-right',	
					],*/
				],
			],
			/*[
				'title' => '分类管理',
				'en_title' => 'cate_manage',
				'url' => '#',
				'icon_class' => 'fa-list',	
				'child' => [
					[
						'title' => '服务',
						'en_title' => 'cate1',
						'url' => ['cate/culture'],
						'icon_class' => 'fa-caret-right',	
					],
				],
			],
			[
				'title' => '内容管理',
				'en_title' => 'content_manage',
				'url' => '#',
				'icon_class' => 'fa-list',	
				'child' => [
					[
						'title' => '客户案例',
						'en_title' => 'customer_case',
						'url' => ['customer-case/index'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '网站模板',
						'en_title' => 'site_template',
						'url' => ['site-template/index'],
						'icon_class' => 'fa-caret-right',	
					],
				],
			],*/
			[
				'title' => '产品管理',
				'en_title' => 'products_manage',
				'url' => '#',
				'icon_class' => 'fa-th-large',	
				'child' => [
					[
						'title' => '产品分类',
						'en_title' => 'products_category',
						'url' => ['products-category/index'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '产品列表',
						'en_title' => 'products',
						'url' => ['products/index'],
						'icon_class' => 'fa-caret-right',	
					],
				],
			],
			[
				'title' => '新闻管理',
				'en_title' => 'news_manage',
				'url' => '#',
				'icon_class' => 'fa-list',	
				'child' => [
					[
						'title' => '新闻分类',
						'en_title' => 'news_category',
						'url' => ['news-category/index'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '新闻列表',
						'en_title' => 'news',
						'url' => ['news/index'],
						'icon_class' => 'fa-caret-right',	
					],
				],
			],
			[
				'title' => '招贤纳士',
				'en_title' => 'zhaopin_manage',
				'url' => '#',
				'icon_class' => 'fa-coffee',	
				'child' => [
					[
						'title' => '招聘管理',
						'en_title' => 'zhaopin',
						'url' => ['zhaopin/index'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '人才理念',
						'en_title' => 'news',
						'url' => ['zhaopin/talent-concept'],
						'icon_class' => 'fa-caret-right',	
					],
				],
			],
			[
				'title' => '联系我们',
				'en_title' => 'contact_us',
				'url' => ['contactus/index'],
				'icon_class' => 'fa-phone',	
				'child' => [],
			],
			[
				'title' => '关于我们',
				'en_title' => 'about_us',
				'url' => ['aboutus/index'],
				'icon_class' => 'fa-info',	
				'child' => []
			],
			[
				'title' => '在线留言',
				'en_title' => 'site_message',
				'url' => ['site-message/index'],
				'icon_class' => 'fa-comments',	
				'child' => [],
			],
			/*[
				'title' => '关于我们',
				'en_title' => 'about_us',
				'url' => '#',
				'icon_class' => 'fa-info',	
				'child' => [
					[
						'title' => '文化',
						'en_title' => 'commpany_culture',
						'url' => ['aboutus/culture'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '证书',
						'en_title' => 'commpany_certificate',
						'url' => ['aboutus/certificate'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '历程',
						'en_title' => 'commpany_history',
						'url' => ['aboutus/history'],
						'icon_class' => 'fa-caret-right',	
					],
					[
						'title' => '简介',
						'en_title' => 'commpany_profile',
						'url' => ['aboutus/profile'],
						'icon_class' => 'fa-caret-right',	
					],
				],
			],*/
		];
		foreach($aMenuConfig as $k => $v){
			$aMenuConfig[$k]['show_child'] = false;
			$aMenuConfig[$k]['is_current'] = false;
			if($v['url'] != '#' && is_array($v['url'])){
				$aMenuConfig[$k]['url'] = $this->_parseMenuConfigUrl($v['url'][0]);
				if(in_array($router, $aMenuConfig[$k]['url'])){
					$aMenuConfig[$k]['is_current'] = true;
				}
			}
			if($v['child']){
				foreach($v['child'] as $kk => $child){
					$aMenuConfig[$k]['child'][$kk]['is_current'] = false;
					if($child['url'] != '#' && is_array($child['url'])){
						$aMenuConfig[$k]['child'][$kk]['url'] = $this->_parseMenuConfigUrl($child['url'][0]);
						if(in_array($router, $aMenuConfig[$k]['child'][$kk]['url'])){
							$aMenuConfig[$k]['show_child'] = true;
							$aMenuConfig[$k]['child'][$kk]['is_current'] = true;
						}
					}
				}
			}
		}
		return $this->render('navi', [
			'aManager' => $aManager,
			'aMenuConfig' => $aMenuConfig,
		]);
	}
	
	private function _parseMenuConfigUrl($router){
		if(!$router){
			return [];
		}
		$aRouter = explode('/', $router);
		$modulesId = '';
		$controllerId = '';
		$actionId = '';
		$class = '';
		if(count($aRouter) == 3){
			$modulesId = $aRouter[0];
			$controllerId = $aRouter[1];
			$actionId = $aRouter[2];
			$class = "manage\\modules\\" . $modulesId . "\\controllers\\" . Inflector::id2camel($controllerId) . 'Controller';
		}else{
			$controllerId = $aRouter[0];
			$actionId = $aRouter[1];
			$class = "manage\\controllers\\" . Inflector::id2camel($controllerId) . 'Controller';
		}
		
		if(!class_exists($class)){
			return [$router];
		}
		$aList = [$router];
		$oReflectionClass = new ReflectionClass($class);
		$aMethodList = $oReflectionClass->getMethods();
		foreach($aMethodList as $oMethod){
			if($class == $oMethod->class && strpos($oMethod->name, 'action') === 0 && strlen($oMethod->name) > strlen('action') && !(strpos($oMethod->name, Inflector::id2camel($actionId)) === false)){
				$tmpRouter = '';
				if($modulesId){
					$tmpRouter .= $modulesId . '/';
				}
				$tmpRouter .= $controllerId . '/';
				$tmpRouter .= Inflector::camel2id(str_replace('action', '', $oMethod->name));
				if($router != $tmpRouter){
					array_push($aList, $tmpRouter);
				}
			}
		}
		return $aList;
	}

}