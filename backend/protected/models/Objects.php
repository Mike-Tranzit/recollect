<?php

Yii::import('application.models._base.BaseObjects');

class Objects extends BaseObjects
{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public static function getObjectsForDistechers(){
	return Yii::app()->db->createCommand()
           ->select("objects.num_auto,objects.status_m, objects.id, glonass.users.name as name, glonass_crm.tasks_for_installers.dispatcher_comment as dispatcher_comment, glonass_crm.tasks_for_installers.installer_comment as installer_comment, glonass_crm.tasks_for_installers.status as stat, glonass_crm.tasks_for_installers.user_id_take as user_id_take, glonass_crm.tasks_for_installers.date_take as date_take, glonass.id as glonass_id, objects.tel, glonass.device_id, glonass.dates, glonass.description, glonass.date_last_coordinate")
           ->from("objects")
           ->leftJoin("glonass","glonass.num_auto = objects.num_auto")
           ->leftJoin("glonass_crm.tasks_for_installers","glonass_crm.tasks_for_installers.obj_id = objects.id")
           ->leftJoin("glonass.users","glonass.users.id = glonass_crm.tasks_for_installers.user_id_take")
           ->where("objects.nat = 1 and objects.galina=0 and objects.status_m = 1 and objects.del = 0 and glonass.deleted = 0 and glonass.provider in (2, 002) and glonass.flag = 1")
           ->queryAll();//  and (LENGTH(glonass.device_id) < 8 OR (glonass.date_last_coordinate is NULL OR (glonass.date_last_coordinate < NOW() - interval 3 hour)))
    }

    public static function getTimeslots(){

        return Yii::app()->db->createCommand()
                ->select("*, nztmodule3.glonass.device_id, nztmodule3.glonass.tel, nztmodule3.glonass.date_last_coordinate, nztmodule3.glonass.tel1, nztmodule3.glonass.description,  reminder_glonass_porttranzit.id as ids")
                ->from("glonass.reminder_glonass_porttranzit")
                ->leftJoin("cms.autos","cms.autos.num_auto = glonass.reminder_glonass_porttranzit.num_auto")
                ->leftJoin("nztmodule3.glonass","nztmodule3.glonass.num_auto = glonass.reminder_glonass_porttranzit.num_auto")
                ->where("date(cms.autos.windows) = date(now()) and hour(cms.autos.windows) < 19  and cms.autos.prov is not null and nztmodule3.glonass.deleted = 0")
                ->order("cms.autos.windows")
                ->queryAll();
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

    public static function getPayments($plate){
        return Yii::app()->db->createCommand()
            ->select("glonass_crm.payments.*, glonass_crm.trucks.balance_license_fee as balance_license_fee")
            ->from("glonass_crm.payments")
            ->leftJoin("glonass_crm.trucks","glonass_crm.trucks.plate = glonass_crm.payments.plate")
            ->where('glonass_crm.payments.plate=:plate', array(':plate'=>$plate))
            ->order('date desc')
            ->queryAll();
    }

    public static function deleteFromReminder($id){
        return Yii::app()->db->createCommand()
            ->delete('glonass.reminder_glonass_porttranzit','id=:id',array(":id"=>$id));
    }

    public static function getPhoneAutoByPlate($plate)
    {
       return Yii::app()->db->createCommand()
               ->select("CONCAT('8',`nztmodule3.objects.tel`)")
               ->from("nztmodule3.objects")
               ->where('num_auto=:plate', array(':plate'=>$plate))
               ->order("nztmodule3.objects.id desc")
               ->queryRow();
    }
}