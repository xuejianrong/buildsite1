<?php
namespace common\model;

use Yii;
use common\model\Setting;

class SiteSetting extends \yii\base\Object{
	public $aBaseSetting = [];
	public $aContactusSetting = [];
	public $aAboutSetting = [];
	public $aFriendsLink = [];
	public $aCooperativePartners = [];
	public $aSiteTemplate = [];
	public $aZhaopinList = [];
	public $talentConcept = '';
	
	public function init(){
		$this->aBaseSetting = Setting::getSetting(Setting::SITE_SETTING_CACHE_KEY);
		$this->aContactusSetting = Setting::getSetting(Setting::CONTACTUS_CACHE_KEY);
		$this->aAboutSetting = Setting::getSetting(Setting::ABOUTUS_CACHE_KEY);
		$this->aFriendsLink = Setting::getSetting(Setting::FRIENDS_LINK_CACHE_KEY);
		$this->aCooperativePartners = Setting::getSetting(Setting::COOPERATIVE_PARTNERS_CACHE_KEY);
		$this->aSiteTemplate = Setting::getSetting(Setting::SITE_TEMPLATE_CACHE_KEY);
		$this->aZhaopinList = Setting::getSetting(Setting::ZHAOPIN_CACHE_KEY);
		$this->talentConcept = Setting::getSetting(Setting::ZHAOPIN_TALENT_CONCEPT_CACHE_KEY);
	}
}