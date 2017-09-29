<?php

/**
 * This is the model class for table "glonass".
 *
 * The followings are the available columns in table 'glonass':
 * @property integer $id
 * @property string $num_auto
 * @property string $dates
 * @property string $description
 * @property integer $flag
 * @property string $date_flag
 * @property string $fio
 * @property string $tel1
 * @property string $tel
 * @property integer $alarm1
 * @property string $alarm1_date
 * @property integer $alarm2
 * @property string $alarm2_date
 * @property integer $alarm3
 * @property string $alarm3_date
 * @property integer $black
 * @property string $black_date
 * @property integer $who_black
 * @property string $why_black
 * @property integer $rub
 * @property string $rubdates
 * @property string $device_id
 * @property string $device_pass
 * @property integer $deleted
 * @property string $subject
 * @property integer $provider
 * @property string $balance
 * @property string $delete_date
 * @property string $only_owner_can_confirm
 * @property integer $userId
 * @property integer $dispatcherId
 * @property string $lat
 * @property string $lon
 * @property string $date_last_coordinate
 * @property integer $zoneId
 */
class Glonass extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Glonass the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'glonass';
	}

    public function defaultScope()
    {
        return array(
            'condition'=>"deleted = 0",
            'order'=>'id DESC'
        );
    }

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('num_auto, dates', 'required'),
			array('flag, alarm1, alarm2, alarm3, black, who_black, 700rub, deleted, provider, userId, dispatcherId, zoneId', 'numerical', 'integerOnly'=>true),
			array('num_auto', 'length', 'max'=>25),
			array('fio', 'length', 'max'=>100),
			array('tel1, tel', 'length', 'max'=>12),
			array('device_id', 'length', 'max'=>20),
			array('device_pass', 'length', 'max'=>8),
			array('balance', 'length', 'max'=>10),
			array('only_owner_can_confirm', 'length', 'max'=>1),
			array('lat, lon', 'length', 'max'=>9),
			array('description, date_flag, alarm1_date, alarm2_date, alarm3_date, black_date, why_black, 700rubdates, subject, delete_date, date_last_coordinate', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, num_auto, dates, description, flag, date_flag, fio, tel1, tel, alarm1, alarm1_date, alarm2, alarm2_date, alarm3, alarm3_date, black, black_date, who_black, why_black, 700rub, 700rubdates, device_id, device_pass, deleted, subject, provider, balance, delete_date, only_owner_can_confirm, userId, dispatcherId, lat, lon, date_last_coordinate, zoneId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'num_auto' => 'Num Auto',
			'dates' => 'Dates',
			'description' => 'Description',
			'flag' => 'Flag',
			'date_flag' => 'Date Flag',
			'fio' => 'Fio',
			'tel1' => 'Tel1',
			'tel' => 'Tel',
			'alarm1' => 'Alarm1',
			'alarm1_date' => 'Alarm1 Date',
			'alarm2' => 'Alarm2',
			'alarm2_date' => 'Alarm2 Date',
			'alarm3' => 'Alarm3',
			'alarm3_date' => 'Alarm3 Date',
			'black' => 'Black',
			'black_date' => 'Black Date',
			'who_black' => 'Who Black',
			'why_black' => 'Why Black',
			'700rub' => '700Rub',
			'700rubdates' => '700Rubdates',
			'device_id' => 'Device',
			'device_pass' => 'Device Pass',
			'deleted' => 'Deleted',
			'subject' => 'Subject',
			'provider' => 'Provider',
			'balance' => 'Balance',
			'delete_date' => 'Delete Date',
			'only_owner_can_confirm' => 'Only Owner Can Confirm',
			'userId' => 'User',
			'dispatcherId' => 'Dispatcher',
			'lat' => 'Lat',
			'lon' => 'Lon',
			'date_last_coordinate' => 'Date Last Coordinate',
			'zoneId' => 'Zone',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('num_auto',$this->num_auto,true);
		$criteria->compare('dates',$this->dates,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('flag',$this->flag);
		$criteria->compare('date_flag',$this->date_flag,true);
		$criteria->compare('fio',$this->fio,true);
		$criteria->compare('tel1',$this->tel1,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('alarm1',$this->alarm1);
		$criteria->compare('alarm1_date',$this->alarm1_date,true);
		$criteria->compare('alarm2',$this->alarm2);
		$criteria->compare('alarm2_date',$this->alarm2_date,true);
		$criteria->compare('alarm3',$this->alarm3);
		$criteria->compare('alarm3_date',$this->alarm3_date,true);
		$criteria->compare('black',$this->black);
		$criteria->compare('black_date',$this->black_date,true);
		$criteria->compare('who_black',$this->who_black);
		$criteria->compare('why_black',$this->why_black,true);
		$criteria->compare('700rub',$this->rub);
		$criteria->compare('700rubdates',$this->rubdates,true);
		$criteria->compare('device_id',$this->device_id,true);
		$criteria->compare('device_pass',$this->device_pass,true);
		$criteria->compare('deleted',$this->deleted);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('provider',$this->provider);
		$criteria->compare('balance',$this->balance,true);
		$criteria->compare('delete_date',$this->delete_date,true);
		$criteria->compare('only_owner_can_confirm',$this->only_owner_can_confirm,true);
		$criteria->compare('userId',$this->userId);
		$criteria->compare('dispatcherId',$this->dispatcherId);
		$criteria->compare('lat',$this->lat,true);
		$criteria->compare('lon',$this->lon,true);
		$criteria->compare('date_last_coordinate',$this->date_last_coordinate,true);
		$criteria->compare('zoneId',$this->zoneId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}