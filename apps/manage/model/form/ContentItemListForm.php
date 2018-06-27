<?php
namespace manage\model\form;

use Yii;
use yii\data\Pagination;
use common\model\ContentItem;

class ContentItemListForm extends \yii\base\Model{
	public $page = 1;
	public $pageSize = 15;
	public $type = 0;
	public $status = -1;
	public $titleLike = '';
	public $startTime = '';
	public $endTime = '';

	public function rules(){
		return [
			['page', 'compare', 'compareValue' => 0, 'operator' => '>'],
			['type', 'noCheck'],
			['titleLike', 'noCheck'],
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
			'order_by' => ['order' => SORT_ASC, 'id' => SORT_DESC],
		];
		$aList = ContentItem::getList($aCondition, $aControl);
				
		return $aList;
	}

	public function getListCondition(){
		$aCondition = ['status' => [ContentItem::STATUS_NOT_PUBLISH, ContentItem::STATUS_PUBLISHED]];
		if($this->type){
			$aCondition['type'] = $this->type;
		}
		if($this->status >= 0){
			$aCondition['status'] = $this->status;
		}
		if($this->titleLike){
			$aCondition['title_like'] = $this->titleLike;
		}
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
		$count = ContentItem::getCount($aCondition);
		return new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);
	}
	
}