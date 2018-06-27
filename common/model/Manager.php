<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class Manager extends \common\lib\DbOrmModel implements IdentityInterface{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@manager');
	}

	/**
     * @inheritdoc 必须要实现的方法
     */
	public function allow($permissionName){
		return true;
	}
	
	/**
     * @inheritdoc 必须要实现的方法
     */
    public static function findIdentity($id){
        return static::findOne($id);
    }
	
	/**
     * @inheritdoc 必须要实现的方法
     */
    public static function findIdentityByAccessToken($token, $type = null){
        throw new NotSupportedException('根据令牌找用户 的方法未实现');
    }
	
	/**
     * @inheritdoc 必须要实现的方法
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc 必须要实现的方法
     */
    public function getAuthKey(){
        return $this->_authKey;
    }

    /**
     * @inheritdoc 必须要实现的方法
     */
    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }
	
	public static function encryptPassword($password){
		return md5($password);
	}
	
	
	/**
	 *	获取列表
	 *	$aCondition = [
	 *		'id' =>
	 *		'start_time' =>
	 *		'end_time' =>
	 *	]
	 *	$aControl = [
	 *		'select' =>
	 *		'order_by' =>
	 *		'page' =>
	 *		'page_size' =>
	 *		'width_group_info' => true/false
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
		if(isset($aControl['width_group_info']) && $aControl['width_group_info']){
			$aManagerGroupList = ManagerGroup::findAll();
			foreach($aList as $k => $v){
				$aList[$k]['group_info'] = [];
				foreach($aManagerGroupList as $aManagerGroup){
					if($v['group_id'] == $aManagerGroup['id']){
						$aList[$k]['group_info'] = $aManagerGroup;
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
		if(isset($aCondition['start_time'])){
			$aWhere[] = ['>', 'create_time', $aCondition['start_time']];
		}
		if(isset($aCondition['end_time'])){
			$aWhere[] = ['<', 'create_time', $aCondition['end_time']];
		}
		return $aWhere;
	}
	
	public function checkAuthToAccess(){
		if($this->id == 1){
			return true;
		}
		$aManagerGroupActionsList = Yii::$app->params['manager_group_actions_list'];
		$aActionList = [];
		foreach($aManagerGroupActionsList as $v){
			$aActionList = array_merge($aActionList, $v['action_list']);
		}
		if(!isset($aActionList[Yii::$app->requestedRoute])){
			return true;
		}
		if(!$this->group_id){
			return false;
		}
		
		$mManagerGroup = ManagerGroup::findOne($this->group_id);
		if(!$mManagerGroup){
			return false;
		}
		if(!in_array(Yii::$app->requestedRoute, $mManagerGroup->actions)){
			return false;
		}
		return true;
	}
}