<?php
namespace common\model;

use Yii;
use umeworld\lib\Query;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class User extends \common\lib\DbOrmModel implements IdentityInterface{
	
	public static function tableName(){
		return Yii::$app->db->parseTable('_@user');
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
	
}