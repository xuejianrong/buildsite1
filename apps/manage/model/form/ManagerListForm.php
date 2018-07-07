<?php
namespace manage\model\form;

use Yii;
use umeworld\lib\Pagination;
use common\model\Manager;

class ManagerListForm extends \yii\base\Model{
	public $page = 1;
	public $pageSize = 15;
	public $startTime = '';
	public $endTime = '';

	public function rules(){
		return [
			['page', 'compare', 'compareValue' => 0, 'operator' => '>'],
			['startTime', 'noCheck'],
			['endTime', 'noCheck'],
		];
	}
	
	public function noCheck(){
		return true;
	}
	
	public function getList(){
		$aCondition = $this->getListCondition();
		$aControl = [
			'page' => $this->page,
			'page_size' => $this->pageSize,
			'order_by' => ['id' => SORT_DESC],
			'width_group_info' => true,
		];
		$aList = Manager::getList($aCondition, $aControl);
				
		return $aList;
	}

	public function getListCondition(){
		$aCondition = [];
		if($this->startTime){
			$aCondition['start_time'] = strtotime($this->startTime);
		}
		if($this->endTime){
			$aCondition['end_time'] = strtotime($this->endTime);
		}
				
		return $aCondition;
	}

	public function getPageObject(){
		$aCondition = $this->getListCondition();
		$count = Manager::getCount($aCondition);
		return new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);
	}
	
}