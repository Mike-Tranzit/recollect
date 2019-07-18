<?php

Yii::import('application.models._base.BasePayments');

class Payments extends BasePayments
{
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}
}