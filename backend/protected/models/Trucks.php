<?php

Yii::import('application.models._base.BaseTrucks');

class Trucks extends BaseTrucks
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }



    public static function getData($post)
    {
        return (ctype_digit($post->param)) ?
             Yii::app()->db->createCommand()
                ->select("glonass_crm.trucks.phone_sim as phone, glonass_crm.trucks.plate as pl")
                ->from("glonass_crm.trucks")
                ->leftJoin("glonass", "glonass.num_auto = glonass_crm.trucks.plate")
                ->where('glonass_crm.trucks.phone_sim is not null and glonass.device_id=:device_id and glonass.flag=1 and glonass.deleted=0', array(':device_id' => $post->param))
                ->queryRow()
        :
             Yii::app()->db->createCommand()
                ->select("glonass_crm.trucks.phone_sim as phone")
                ->from("glonass_crm.trucks")
                ->where('glonass_crm.trucks.phone_sim is not null and glonass_crm.trucks.plate=:plate', array(':plate' => MYChtml::check_num( iconv("UTF-8","windows-1251", $post->param) )))
                ->queryRow();
    }
//Y431CA123
    public static function addAct($post)
    {
        if ($model = Trucks::model()->find("`plate`=:plate", array(":plate" => $post->plate))) {
            $model->act_number = $post->act;
            $model->is_act = 1;
            return $model->save(false) ? 1 : 2;
        } else return 0;
    }


    /*                      */

    public static function updateSim($data){
        $model = Trucks::model()->findByPk( $data->id );
        if($data->old_sim_number != trim($data->sim) ){
            if(!$s = Sims::model()->find("SUBSTR(sim_num, -6) = :sim_num", array(':sim_num' => $data->sim))) return 0;
            $model->number_sim = $s->sim_num;
            $model->phone_sim = $s->phone_num;
            $model->save(false);
            return 2;
        }
        return 2;
    }

    

    public static function getTrucksSim(){
        return Yii::app()->db->createCommand()
            ->select("glonass_crm.trucks.*")
            ->from("glonass_crm.trucks")
            ->leftJoin("nztmodule3.objects", "nztmodule3.objects.num_auto = glonass_crm.trucks.plate")
            ->where('glonass_crm.trucks.`type` < 2 and glonass_crm.trucks.`phone_sim` is null and glonass_crm.trucks.`number_sim` is null and glonass_crm.trucks.is_act = 1 and (nztmodule3.objects.status_m = 1 and nztmodule3.objects.galina = 0 and nztmodule3.objects.del=0)')
            ->queryAll();
    }

    /*                      */
}