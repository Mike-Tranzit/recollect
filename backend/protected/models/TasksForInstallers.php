<?php

Yii::import('application.models._base.BaseTasksForInstallers');

class TasksForInstallers extends BaseTasksForInstallers
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public static function completeTask($post){
	    if(!$task = self::model()->findByPk($post->id)) return 0;
        $task->date_complete = new CDbExpression("now()");
        $task->user_id_complete = $post->user;
        $task->status = 1;
        if(!$task->save()) return 1;
        if($sanctions = $task->Sanctions){
            foreach ($sanctions as $sanction) {
                if($sanction->status == 0 and $sanction->glonass == 1){
                    $sanction->status = 1;
                    $sanction->date_out = new CDbExpression("now()");
                    $sanction->user_id_remove = (int)$post->user;
                    $sanction->save();
                }
            }
        }
        return 2;
    }

	public static function getTasksForInstaller(){
        return Yii::app()->db->createCommand()
            ->select("glonass_crm.tasks_for_installers.*")
            ->from("glonass_crm.tasks_for_installers")
            ->leftJoin("objects","objects.id = glonass_crm.tasks_for_installers.obj_id")
            ->where("`status`=0 and objects.status_m = 1")
            ->queryAll();
    }

    public static function getTask($id){
        return Yii::app()->db->createCommand()
            ->select("glonass_crm.tasks_for_installers.*, objects.tel as phone, glonass.device_id as deviceId, glonass_crm.trucks.act_number as act")
            ->from("glonass_crm.tasks_for_installers")
            ->where("glonass_crm.tasks_for_installers.`id`=:id",array(":id"=>$id))
            ->leftJoin("glonass","glonass.id = glonass_crm.tasks_for_installers.glonass_id")
            ->leftJoin("objects","objects.id = glonass_crm.tasks_for_installers.obj_id")
            ->leftJoin("glonass_crm.trucks","glonass_crm.trucks.plate = glonass_crm.tasks_for_installers.plate")
            ->queryRow();
    }



	public static function getTasks(){
        return Yii::app()->db->createCommand()
               ->select("glonass_crm.tasks_for_installers.*,glonass.device_id as deviceId, glonass_crm.trucks.act_number as act")
               ->from("glonass_crm.tasks_for_installers")
               ->where("`status`=0")
               ->leftJoin("glonass","glonass.id = glonass_crm.tasks_for_installers.glonass_id")
               ->leftJoin("glonass_crm.trucks","glonass_crm.trucks.plate = glonass_crm.tasks_for_installers.plate")
               ->queryAll();
    }

    public static function addTask($post){
        $model = new TasksForInstallers();
        $model->plate = $post->plate;
        $model->type = $post->type;
        $model->glonass_id = $post->glonass_id;
        $model->user_id_add = $post->user;
        $model->status = 0;
        $model->obj_id = $post->ids;
        $model->date_create = new CDbExpression("now()");
        return $model->save();
    }
}