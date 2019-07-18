<?php

Yii::import('application.models._base.BaseAutos');

class Autos extends BaseAutos
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

// date(cms.autos.windows) = date(now()) and hour(cms.autos.windows) < 19  and cms.autos.prov is not null and nztmodule3.glonass.deleted = 0
    public static function getTimeslots()
    {
        return Yii::app()->db->createCommand()
            ->select("cms.autos.windows,cms.autos.window_to as to,cms.autos.window_from as fr,nztmodule3.glonass.device_id as dev,cms.autos.num_auto, nztmodule3.glonass.id as gl, cms.autos.id as ia")
            ->from("cms.autos")
            ->leftJoin("nztmodule3.glonass", "nztmodule3.glonass.num_auto = cms.autos.num_auto")
            ->where("cms.autos.confirm=1 and cms.autos.windows > now() - interval 1 hour and cms.autos.windows < now() + interval 12 hour and (nztmodule3.glonass.deleted = 0 and nztmodule3.glonass.provider = 2 and nztmodule3.glonass.flag = 1 and (nztmodule3.glonass.date_last_coordinate is null or nztmodule3.glonass.date_last_coordinate < now() - interval 5 HOUR) )")
            ->order("cms.autos.windows")
            ->queryAll();
    }

    public static function platesExistInInstallers()
    {
        $r = Yii::app()->db->createCommand()
            ->select('glonass_id')
            ->from('glonass_crm.tasks_for_installers')
            ->where('`status`!=2 and glonass_crm.tasks_for_installers.date_create > now() - interval 2 day and obj_id = 0')
            ->queryAll();
        if(!$r) return array();
        $id = array();
        foreach ($r as $item) {
            $id[] = $item['glonass_id'];
        }
        return $id;
    }
}
