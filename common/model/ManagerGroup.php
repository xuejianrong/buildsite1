<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class ManagerGroup extends \common\lib\DbOrmModel{
	
	protected $_aEncodeFields = ['actions' => 'json'];
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@manager_group');
	}
	
}