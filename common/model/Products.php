<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class Products extends \common\lib\DbOrmModel{
	const STATUS_NOT_PUBLISH = 0;	//未发布
	const STATUS_PUBLISHED = 1;		//已发布
	
	protected $_aEncodeFields = ['other_info' => 'json'];
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@products');
	}
		
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'not_id' =>
	 *		'category_id' =>
	 *		'status' =>
	 *		'name_like' =>
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
		
		foreach($aList as $k => $v){
			$aList[$k]['other_info'] = json_decode($v['other_info'], true);
		}
		
		if(isset($aControl['with_category_info']) && $aControl['with_category_info']){
			$aCategoryList = ProductsCategory::findAll();
			foreach($aList as $k => $v){
				$aList[$k]['category_info'] = [];
				foreach($aCategoryList as $aCategory){
					if($v['category_id'] == $aCategory['id']){
						$aList[$k]['category_info'] = $aCategory;
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
		if(isset($aCondition['name_like']) && $aCondition['name_like']){
			$aWhere[] = ['like', 'name', $aCondition['name_like']];
		}
		if(isset($aCondition['not_id']) && $aCondition['not_id']){
			$aWhere[] = ['NOT IN', 'id', $aCondition['not_id']];
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