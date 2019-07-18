<?php

Yii::import('application.models._base.BaseSanctions');

class Sanctions extends BaseSanctions
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public static function setSanctionStatus($post){

        $model = Sanctions::model()->find("`obj_id`=:obj_id and `glonass` = 1",array(":obj_id"=>(int)$post->id));
        if($model && $model->status == $post->status) return $model;
        if(!$model) {
            $model = new Sanctions();
            $model->obj_id = $post->id;
            $model->date_create = new CDbExpression("now()");
            $model->comment = 'Äîáàâëåí óñòàíîâùèêîì ÃËÎÍÀÑÑ. Ïğîáëåìà ñ óñòğîéñòâîì';
            $model->user_id_add = (int)$post->user;
            $model->glonass_id = $post->glonass_id;
        }
        $model->status = (int)$post->status;
        if($model->status == 1){
            $model->date_out = new CDbExpression("now()");
            $model->user_id_remove = (int)$post->user;
        }

        $model->glonass = 1;

        $model->save(false);
        return $model;
    }

	public static function getSanctionStatus($id){
	    return self::model()->find("`obj_id`=:obj_id and status = 0 and glonass = 1 order by id desc",array(":obj_id"=>$id));
    }
}