<?php

Yii::import('application.models._base.BaseTarifs');

class Tarifs extends BaseTarifs
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public static function getTarifts($type){
	    $arr = array();
        foreach (self::model()->findAll('`type`=:type',array(":type"=>$type)) as $item) {
            $arr[] = array('id'=>$item->id,'name'=>$item->name,'amount'=>$item->amount);
	    }
	    return $arr;
    }
}