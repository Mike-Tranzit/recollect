<?php

Yii::import('application.models._base.BaseTrucks');

class Trucks extends BaseTrucks
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	//public static function getAct

	public static function addAct($post){
	    if($model = Trucks::model()->find("`plate`=:plate",array(":plate"=>$post->plate))){
	        $model->act_number = $post->act;
	        return $model->save(false)?1:2;
        }else return 0;
    }
}