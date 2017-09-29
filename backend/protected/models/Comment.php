<?php

Yii::import('application.models._base.BaseComment');

class Comment extends BaseComment
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public static function addComment($post){
        $model = new Comment();
        $model->dates = new CDbExpression("now()");
        $model->comment = iconv("UTF-8","windows-1251",$post->text);
        $model->num_auto = $post->plate;
        return $model->save();
    }
}