<?php

Yii::import('application.models._base.BaseTasksForInstallers');

class TasksForInstallers extends BaseTasksForInstallers
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public static function updateTaskByPk($id, $obj_id)
    {
        $model = self::model()->findByPk($id);
        $model->obj_id = (int)$obj_id;
        $model->save(false);
    }


    public static function updateTaskComment($post)
    {
        $model = self::model()->findByPk($post->id);
        $model->installer_comment = iconv("UTF-8", "windows-1251", $post->installer_comment);
        $model->save(false);
    }

    public static function getGlonassPhone($id)
    {
        return Yii::app()->db->createCommand()->from('glonass')->where('id=:id', array(':id' => $id))->queryRow();
    }

    public static function updateTask($post)
    {
        if (!$task = self::model()->findByPk($post->id)) return 0;
        if ((int)$post->status == 1) {
            $task->date_take = new CDbExpression("now()");
            $task->user_id_take = $post->user;
        } else {
            $task->date_take = NULL;
            $task->user_id_take = NULL;
        }
        $task->status = $post->status;
        if (!$task->save()) return 1;
        return 2;
    }

    public static function completeTask($post)
    {
        try {
            if (!$task = self::model()->findByPk($post->id)) return 0;
            $task->date_complete = new CDbExpression("now()");
            $task->user_id_complete = $post->user;
            $task->status = 2;
            if (!$task->save()) return 1;
            if ($sanction = $task->Sanctions) {
                if ($sanction->status == 0 and $sanction->glonass == 1) {
                    $sanction->status = 1;
                    $sanction->date_out = new CDbExpression("now()");
                    $sanction->user_id_remove = (int)$post->user;
                    $sanction->save();
                }
            }
            if (isset($post->tarifsChecked) && count($post->tarifsChecked)) {
                foreach ($post->tarifsChecked as $item) {
                    $i = new Payments();
                    $i->repair = 1;
                    $i->zreport = 1;
                    $i->comment =  iconv("UTF-8","windows-1251",$item->name);
                    $i->amount_fee_license = 0;
                    $i->amount_installation = (int)('-' . abs((int)$item->amount));
                    $i->plate = $task->plate;
                    $i->date = new CDbExpression("now()");
                    $i->save();
                }
            }
            return 2;
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }

    public static function getTasksForInstaller()
    {
        return Yii::app()->db->createCommand()
            ->select("glonass_crm.tasks_for_installers.*, glonass.users.name as name, objects.status_m as stat, CONCAT(objects.date_from_podskok,' ',objects.time_from_podskok) as date_fr")
            ->from("glonass_crm.tasks_for_installers")
            ->leftJoin("objects", "objects.id = glonass_crm.tasks_for_installers.obj_id")
            ->leftJoin("glonass.users", "glonass.users.id = glonass_crm.tasks_for_installers.user_id_take")
            ->where("`status`!=2 and ((objects.status_m in(1,2,3) and objects.del=0 and objects.galina=0) or glonass_crm.tasks_for_installers.obj_id = 0) and glonass_crm.tasks_for_installers.date_create > now() - interval 2 day")
            ->order("glonass_crm.tasks_for_installers.date_create DESC")
            ->queryAll();
    }

    public static function getTask($id)
    {
        return Yii::app()->db->createCommand()
            ->select("glonass_crm.tasks_for_installers.*, objects.tel as phone, glonass.device_id as deviceId, glonass_crm.trucks.act_number as act, SUBSTR(glonass_crm.trucks.number_sim, -6) as sim")
            ->from("glonass_crm.tasks_for_installers")
            ->where("glonass_crm.tasks_for_installers.`id`=:id", array(":id" => $id))
            ->leftJoin("glonass", "glonass.id = glonass_crm.tasks_for_installers.glonass_id")
            ->leftJoin("objects", "objects.id = glonass_crm.tasks_for_installers.obj_id")
            ->leftJoin("glonass_crm.trucks", "glonass_crm.trucks.plate = glonass_crm.tasks_for_installers.plate")
            ->queryRow();
    }

    public static function getLastPhone($plate)
    {
        return Yii::app()->db->createCommand()->select('tel, id, status_m')->from('objects')->
        where('num_auto=:num_auto and status_m in(1,2,3,4) and galina=0 and del = 0 and LENGTH(tel) > 2', array(":num_auto" => $plate))->order('id desc')->queryRow();
    }

    public static function getTasks()
    {
        return Yii::app()->db->createCommand()
            ->select("glonass_crm.tasks_for_installers.*,glonass.device_id as deviceId, glonass_crm.trucks.act_number as act")
            ->from("glonass_crm.tasks_for_installers")
            ->where("`status`!=3")
            ->leftJoin("glonass", "glonass.id = glonass_crm.tasks_for_installers.glonass_id")
            ->leftJoin("glonass_crm.trucks", "glonass_crm.trucks.plate = glonass_crm.tasks_for_installers.plate")
            ->queryAll();
    }

    public static function addTask($post)
    {
        $model = new TasksForInstallers();
        $model->plate = MYChtml::check_num($post->plate);
        $model->type = $post->type;
        if ((int)$post->glonass_id == 0) {
            $glonassModel = Yii::app()->db->createCommand()->select('id')->from('glonass')->where('flag=1 and deleted=0 and provider=2 and num_auto=:num_auto', array(":num_auto" => $model->plate))->queryRow();
            $model->glonass_id = (int)$glonassModel['id'];
        } else $model->glonass_id = $post->glonass_id;
        $model->user_id_add = $post->user;
        $model->status = 0;
        $model->obj_id = $post->ids;
        $model->dispatcher_comment = isset($post->comment) ? iconv("UTF-8", "windows-1251", $post->comment) : NULL;
        if ($model->dispatcher_comment == 'Ñîçäàíà óñòàíîâùèêîì' && $model->obj_id == 0) {
            $model->status = 1;
            $model->user_id_take = $model->user_id_add;
            $model->date_take = new CDbExpression("now()");
        }
        $model->date_create = new CDbExpression("now()");
        if ($model->glonass_id == 0) return 1;
        return $model->save() ? 2 : 3;
    }
}