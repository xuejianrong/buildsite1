<?php
namespace manage\model\form;

use Yii;
use umeworld\lib\Pagination;
use common\model\News;

class NewsListForm extends \yii\base\Model{
	public $page = 1;
	public $pageSize = 15;
	public $categoryId = 0;
	public $status = -1;
	public $titleLike = '';
	public $startTime = '';
	public $endTime = '';

	public function rules(){
		return [
			['page', 'compare', 'compareValue' => 0, 'operator' => '>'],
			['categoryId', 'noCheck'],
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
			'order_by' => ['id' => SORT_DESC],
			'with_category_info' => true,
		];
		$aList = News::getList($aCondition, $aControl);
				
		return $aList;
	}

	public function getListCondition(){
		$aCondition = ['status' => [News::STATUS_NOT_PUBLISH, News::STATUS_PUBLISHED]];
		if($this->categoryId){
			$aCondition['categoryId'] = $this->categoryId;
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
		$count = News::getCount($aCondition);
		return new Pagination(['totalCount' => $count, 'pageSize' => $this->pageSize]);
	}
	
}