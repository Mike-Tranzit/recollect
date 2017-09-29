<?php

/**
 * This is the model base class for the table "objects".
 * DO NOT MODIFY THIS FILE! It is automatically generated by giix.
 * If any changes are necessary, you must set or override the required
 * property or method in class "Objects".
 *
 * Columns in table "objects" available as properties of the model,
 * followed by relations of table "objects" available as properties of the model.
 *
 * @property integer $id
 * @property integer $pid
 * @property integer $status_m
 * @property integer $status_person
 * @property integer $status_auto
 * @property string $date_from
 * @property string $time_from
 * @property string $date_to
 * @property string $time_to
 * @property string $num_doc
 * @property string $num_auto
 * @property string $fio
 * @property integer $firm
 * @property integer $culture
 * @property integer $station
 * @property double $mas
 * @property string $date_to_podskok
 * @property string $time_to_podskok
 * @property string $date_from_podskok
 * @property string $time_from_podskok
 * @property string $date_to_nzt
 * @property string $time_to_nzt
 * @property string $date_from_nzt
 * @property string $time_from_nzt
 * @property string $primech
 * @property string $date_cre
 * @property integer $contract
 * @property integer $contract_status
 * @property integer $otkat
 * @property integer $black_list
 * @property integer $notice
 * @property string $tel
 * @property integer $pinokb
 * @property integer $pinokp
 * @property string $timepinokb
 * @property string $timepinokp
 * @property string $datepinokb
 * @property string $datepinokp
 * @property integer $pinok
 * @property string $time_appro_to
 * @property string $date_appro_to
 * @property string $date_from_punkt
 * @property string $time_from_punkt
 * @property string $remont
 * @property integer $remontstatus
 * @property integer $remonttime
 * @property integer $del
 * @property string $del_time
 * @property string $del_date
 * @property integer $nat
 * @property integer $glonass
 * @property integer $nocash
 * @property integer $is_nkhp
 * @property integer $galina
 * @property integer $isReturn
 * @property string $dateReturn
 * @property string $date_from_nat
 * @property string $orderFromStividor
 * @property string $date_weighing_start
 * @property string $date_weighing_end
 * @property integer $provider
 * @property integer $allow_stevedore_change
 * @property integer $organization
 *
 * @property Mototelecommain[] $mototelecommains
 * @property NkhpExt[] $nkhpExts
 * @property ObjectStatus[] $objectStatuses
 */
abstract class BaseObjects extends GxActiveRecord {

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'objects';
	}

	public static function label($n = 1) {
		return Yii::t('app', 'Objects|Objects', $n);
	}

	public static function representingColumn() {
		return 'date_to';
	}

	public function rules() {
		return array(
			array('num_doc, fio, date_cre', 'required'),
			array('pid, status_m, status_person, status_auto, firm, culture, station, contract, contract_status, otkat, black_list, notice, pinokb, pinokp, pinok, remontstatus, remonttime, del, nat, glonass, nocash, is_nkhp, galina, isReturn, provider, allow_stevedore_change, organization', 'numerical', 'integerOnly'=>true),
			array('mas', 'numerical'),
			array('num_auto', 'length', 'max'=>15),
			array('fio, tel', 'length', 'max'=>255),
			array('date_from, time_from, date_to, time_to, date_to_podskok, time_to_podskok, date_from_podskok, time_from_podskok, date_to_nzt, time_to_nzt, date_from_nzt, time_from_nzt, primech, timepinokb, timepinokp, datepinokb, datepinokp, time_appro_to, date_appro_to, date_from_punkt, time_from_punkt, remont, del_time, del_date, dateReturn, date_from_nat, orderFromStividor, date_weighing_start, date_weighing_end', 'safe'),
			array('pid, status_m, status_person, status_auto, date_from, time_from, date_to, time_to, num_auto, firm, culture, station, mas, date_to_podskok, time_to_podskok, date_from_podskok, time_from_podskok, date_to_nzt, time_to_nzt, date_from_nzt, time_from_nzt, primech, contract, contract_status, otkat, black_list, notice, tel, pinokb, pinokp, timepinokb, timepinokp, datepinokb, datepinokp, pinok, time_appro_to, date_appro_to, date_from_punkt, time_from_punkt, remont, remontstatus, remonttime, del, del_time, del_date, nat, glonass, nocash, is_nkhp, galina, isReturn, dateReturn, date_from_nat, orderFromStividor, date_weighing_start, date_weighing_end, provider, allow_stevedore_change, organization', 'default', 'setOnEmpty' => true, 'value' => null),
			array('id, pid, status_m, status_person, status_auto, date_from, time_from, date_to, time_to, num_doc, num_auto, fio, firm, culture, station, mas, date_to_podskok, time_to_podskok, date_from_podskok, time_from_podskok, date_to_nzt, time_to_nzt, date_from_nzt, time_from_nzt, primech, date_cre, contract, contract_status, otkat, black_list, notice, tel, pinokb, pinokp, timepinokb, timepinokp, datepinokb, datepinokp, pinok, time_appro_to, date_appro_to, date_from_punkt, time_from_punkt, remont, remontstatus, remonttime, del, del_time, del_date, nat, glonass, nocash, is_nkhp, galina, isReturn, dateReturn, date_from_nat, orderFromStividor, date_weighing_start, date_weighing_end, provider, allow_stevedore_change, organization', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'nkhpExts' => array(self::HAS_MANY, 'NkhpExt', 'pid'),
			'objectStatuses' => array(self::HAS_MANY, 'ObjectStatus', 'pid'),

		);
	}

	public function pivotModels() {
		return array(
		);
	}

	public function attributeLabels() {
		return array(
			'id' => Yii::t('app', 'ID'),
			'pid' => Yii::t('app', 'Pid'),
			'status_m' => Yii::t('app', 'Status M'),
			'status_person' => Yii::t('app', 'Status Person'),
			'status_auto' => Yii::t('app', 'Status Auto'),
			'date_from' => Yii::t('app', 'Date From'),
			'time_from' => Yii::t('app', 'Time From'),
			'date_to' => Yii::t('app', 'Date To'),
			'time_to' => Yii::t('app', 'Time To'),
			'num_doc' => Yii::t('app', 'Num Doc'),
			'num_auto' => Yii::t('app', 'Num Auto'),
			'fio' => Yii::t('app', 'Fio'),
			'firm' => Yii::t('app', 'Firm'),
			'culture' => Yii::t('app', 'Culture'),
			'station' => Yii::t('app', 'Station'),
			'mas' => Yii::t('app', 'Mas'),
			'date_to_podskok' => Yii::t('app', 'Date To Podskok'),
			'time_to_podskok' => Yii::t('app', 'Time To Podskok'),
			'date_from_podskok' => Yii::t('app', 'Date From Podskok'),
			'time_from_podskok' => Yii::t('app', 'Time From Podskok'),
			'date_to_nzt' => Yii::t('app', 'Date To Nzt'),
			'time_to_nzt' => Yii::t('app', 'Time To Nzt'),
			'date_from_nzt' => Yii::t('app', 'Date From Nzt'),
			'time_from_nzt' => Yii::t('app', 'Time From Nzt'),
			'primech' => Yii::t('app', 'Primech'),
			'date_cre' => Yii::t('app', 'Date Cre'),
			'contract' => Yii::t('app', 'Contract'),
			'contract_status' => Yii::t('app', 'Contract Status'),
			'otkat' => Yii::t('app', 'Otkat'),
			'black_list' => Yii::t('app', 'Black List'),
			'notice' => Yii::t('app', 'Notice'),
			'tel' => Yii::t('app', 'Tel'),
			'pinokb' => Yii::t('app', 'Pinokb'),
			'pinokp' => Yii::t('app', 'Pinokp'),
			'timepinokb' => Yii::t('app', 'Timepinokb'),
			'timepinokp' => Yii::t('app', 'Timepinokp'),
			'datepinokb' => Yii::t('app', 'Datepinokb'),
			'datepinokp' => Yii::t('app', 'Datepinokp'),
			'pinok' => Yii::t('app', 'Pinok'),
			'time_appro_to' => Yii::t('app', 'Time Appro To'),
			'date_appro_to' => Yii::t('app', 'Date Appro To'),
			'date_from_punkt' => Yii::t('app', 'Date From Punkt'),
			'time_from_punkt' => Yii::t('app', 'Time From Punkt'),
			'remont' => Yii::t('app', 'Remont'),
			'remontstatus' => Yii::t('app', 'Remontstatus'),
			'remonttime' => Yii::t('app', 'Remonttime'),
			'del' => Yii::t('app', 'Del'),
			'del_time' => Yii::t('app', 'Del Time'),
			'del_date' => Yii::t('app', 'Del Date'),
			'nat' => Yii::t('app', 'Nat'),
			'glonass' => Yii::t('app', 'Glonass'),
			'nocash' => Yii::t('app', 'Nocash'),
			'is_nkhp' => Yii::t('app', 'Is Nkhp'),
			'galina' => Yii::t('app', 'Galina'),
			'isReturn' => Yii::t('app', 'Is Return'),
			'dateReturn' => Yii::t('app', 'Date Return'),
			'date_from_nat' => Yii::t('app', 'Date From Nat'),
			'orderFromStividor' => Yii::t('app', 'Order From Stividor'),
			'date_weighing_start' => Yii::t('app', 'Date Weighing Start'),
			'date_weighing_end' => Yii::t('app', 'Date Weighing End'),
			'provider' => Yii::t('app', 'Provider'),
			'allow_stevedore_change' => Yii::t('app', 'Allow Stevedore Change'),
			'organization' => Yii::t('app', 'Organization'),
			'mototelecommains' => null,
			'nkhpExts' => null,
			'objectStatuses' => null,
		);
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('pid', $this->pid);
		$criteria->compare('status_m', $this->status_m);
		$criteria->compare('status_person', $this->status_person);
		$criteria->compare('status_auto', $this->status_auto);
		$criteria->compare('date_from', $this->date_from, true);
		$criteria->compare('time_from', $this->time_from, true);
		$criteria->compare('date_to', $this->date_to, true);
		$criteria->compare('time_to', $this->time_to, true);
		$criteria->compare('num_doc', $this->num_doc, true);
		$criteria->compare('num_auto', $this->num_auto, true);
		$criteria->compare('fio', $this->fio, true);
		$criteria->compare('firm', $this->firm);
		$criteria->compare('culture', $this->culture);
		$criteria->compare('station', $this->station);
		$criteria->compare('mas', $this->mas);
		$criteria->compare('date_to_podskok', $this->date_to_podskok, true);
		$criteria->compare('time_to_podskok', $this->time_to_podskok, true);
		$criteria->compare('date_from_podskok', $this->date_from_podskok, true);
		$criteria->compare('time_from_podskok', $this->time_from_podskok, true);
		$criteria->compare('date_to_nzt', $this->date_to_nzt, true);
		$criteria->compare('time_to_nzt', $this->time_to_nzt, true);
		$criteria->compare('date_from_nzt', $this->date_from_nzt, true);
		$criteria->compare('time_from_nzt', $this->time_from_nzt, true);
		$criteria->compare('primech', $this->primech, true);
		$criteria->compare('date_cre', $this->date_cre, true);
		$criteria->compare('contract', $this->contract);
		$criteria->compare('contract_status', $this->contract_status);
		$criteria->compare('otkat', $this->otkat);
		$criteria->compare('black_list', $this->black_list);
		$criteria->compare('notice', $this->notice);
		$criteria->compare('tel', $this->tel, true);
		$criteria->compare('pinokb', $this->pinokb);
		$criteria->compare('pinokp', $this->pinokp);
		$criteria->compare('timepinokb', $this->timepinokb, true);
		$criteria->compare('timepinokp', $this->timepinokp, true);
		$criteria->compare('datepinokb', $this->datepinokb, true);
		$criteria->compare('datepinokp', $this->datepinokp, true);
		$criteria->compare('pinok', $this->pinok);
		$criteria->compare('time_appro_to', $this->time_appro_to, true);
		$criteria->compare('date_appro_to', $this->date_appro_to, true);
		$criteria->compare('date_from_punkt', $this->date_from_punkt, true);
		$criteria->compare('time_from_punkt', $this->time_from_punkt, true);
		$criteria->compare('remont', $this->remont, true);
		$criteria->compare('remontstatus', $this->remontstatus);
		$criteria->compare('remonttime', $this->remonttime);
		$criteria->compare('del', $this->del);
		$criteria->compare('del_time', $this->del_time, true);
		$criteria->compare('del_date', $this->del_date, true);
		$criteria->compare('nat', $this->nat);
		$criteria->compare('glonass', $this->glonass);
		$criteria->compare('nocash', $this->nocash);
		$criteria->compare('is_nkhp', $this->is_nkhp);
		$criteria->compare('galina', $this->galina);
		$criteria->compare('isReturn', $this->isReturn);
		$criteria->compare('dateReturn', $this->dateReturn, true);
		$criteria->compare('date_from_nat', $this->date_from_nat, true);
		$criteria->compare('orderFromStividor', $this->orderFromStividor, true);
		$criteria->compare('date_weighing_start', $this->date_weighing_start, true);
		$criteria->compare('date_weighing_end', $this->date_weighing_end, true);
		$criteria->compare('provider', $this->provider);
		$criteria->compare('allow_stevedore_change', $this->allow_stevedore_change);
		$criteria->compare('organization', $this->organization);

		return new CActiveDataProvider($this, array(
			'criteria' => $criteria,
		));
	}
}