<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class NewsCategory extends \common\lib\DbOrmModel{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@news_category');
	}
	
}