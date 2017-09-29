<?php

Yii::import('application.models._base.BaseObjects');

class Objects extends BaseObjects
{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public static function getObjectsForDistechers(){
	return Yii::app()->db->createCommand()
           ->select("objects.num_auto,objects.status_m, objects.id, glonass.id as glonass_id, objects.tel, glonass.device_id, glonass.dates, glonass.description, glonass.date_last_coordinate")
           ->from("objects")
           ->leftJoin("glonass","glonass.num_auto = objects.num_auto")
           ->where("objects.nat = 1 and objects.galina=0 and objects.status_m = 1 and objects.del = 0 and glonass.deleted = 0 and glonass.provider = 2 and glonass.flag = 1 and (glonass.device_id = 0 OR glonass.date_last_coordinate < ( now() - interval 30 minute))")
           ->queryAll();


  /*  return Yii::app()->db->createCommand( "Select objects.num_auto,objects.status_m, objects.id, objects.tel, glonass.device_id, glonass.dates, glonass.description, glonass.date_last_coordinate from objects LEFT JOIN glonass on glonass.num_auto = objects.num_auto where objects.galina=0 and objects.status_m = 1 and objects.del = 0 and glonass.deleted = 0 and glonass.provider = 2 and glonass.flag = 1 and (glonass.device_id = 0 OR glonass.date_last_coordinate < ( now() - interval 30 minute))" )->queryAll();*/
    }



    public static function getComments($plate){
        $comments = Yii::app()->db->createCommand()
                    ->from("glonass.comment")
                    ->where("num_auto=:plate", array(':plate'=>$plate))
                    ->queryAll();
        $comment = '';
        foreach ($comments as $item){
            $comment .= "" . MYDate::showComments($item["dates"]) . " " . $item["comment"] . " ";
        }
        return $comment;
    }

    public static function getAmount($plate){
	    $record = Yii::app()->db->createCommand()
               ->select("SUM(glonass_crm.payments.amount_installation) as amount_inst")
               ->from("glonass_crm.payments")
               ->where('plate=:plate', array(':plate'=>$plate))
               ->queryRow();
	    return trim(strlen($record['amount_inst'])) ? $record['amount_inst'] : 0;
    }
}