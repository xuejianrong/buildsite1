<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class News extends \common\lib\DbOrmModel{
	const STATUS_NOT_PUBLISH = 0;	//未发布
	const STATUS_PUBLISHED = 1;		//已发布
	
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@news');
	}
		
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'category_id' =>
	 *		'status' =>
	 *		'title_like' =>
	 *		'start_time' =>
	 *		'end_time' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *		'with_category_info' => true/false
	 *	]
	 */
	public static function getList($aCondition = [], $aControl = []){
		$aWhere = static::_parseWhereCondition($aCondition);
		$oQuery = new Query();
		if(isset($aControl['select'])){
			$oQuery->select($aControl['select']);
		}
		$oQuery->from(static::tableName())->where($aWhere);
		if(isset($aControl['order_by'])){
			$oQuery->orderBy($aControl['order_by']);
		}
		if(isset($aControl['page']) && isset($aControl['page_size'])){
			$offset = ($aControl['page'] - 1) * $aControl['page_size'];
			$oQuery->offset($offset)->limit($aControl['page_size']);
		}
		$aList = $oQuery->all();
		if(!$aList){
			return [];
		}
		
		if(isset($aControl['with_category_info']) && $aControl['with_category_info']){
			$aNewsCategoryList = NewsCategory::findAll();
			foreach($aList as $k => $v){
				$aList[$k]['category_info'] = [];
				foreach($aNewsCategoryList as $aNewsCategory){
					if($v['category_id'] == $aNewsCategory['id']){
						$aList[$k]['category_info'] = $aNewsCategory;
						break;
					}
				}
			}
		}
		
		return $aList;
	}
	
	/**
	 *	获取数量
	 */
	public static function getCount($aCondition = []){
		$aWhere = static::_parseWhereCondition($aCondition);
		return (new Query())->from(static::tableName())->where($aWhere)->count();
	}
	
	private static function _parseWhereCondition($aCondition = []){
		$aWhere = ['and'];
		if(isset($aCondition['id'])){
			$aWhere[] = ['id' => $aCondition['id']];
		}
		if(isset($aCondition['category_id'])){
			$aWhere[] = ['category_id' => $aCondition['category_id']];
		}
		if(isset($aCondition['status'])){
			$aWhere[] = ['status' => $aCondition['status']];
		}
		if(isset($aCondition['title_like']) && $aCondition['title_like']){
			$aWhere[] = ['like', 'title', $aCondition['title_like']];
		}
		if(isset($aCondition['start_time'])){
			$aWhere[] = ['>', 'create_time', $aCondition['start_time']];
		}
		if(isset($aCondition['end_time'])){
			$aWhere[] = ['<', 'create_time', $aCondition['end_time']];
		}
		return $aWhere;
	}
	
}