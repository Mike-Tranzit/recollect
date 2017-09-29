<?php

Yii::import('application.models._base.BaseUsers');

class Users extends BaseUsers
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public static function getCurrentModel($login,$password){
	return self::model()->find(array('condition'=>'`login`=:login and `password`=:password and `admin`=1 and `isProvider`=2 and `confirm`=1','params'=>array(":login"=>"+".trim($login),":password"=>md5($password))));
    }
}