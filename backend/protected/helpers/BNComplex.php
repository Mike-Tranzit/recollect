<?php

/**
 * Created by PhpStorm.
 * User: Mihail
 * Date: 24.09.2017
 * Time: 14:38
 */
class BNComplex
{
    public static function GlonassInBnComplex($device_id,$num_auto,$tel){
        try{
            Yii::app()->BNComplex->createCommand('EXEC [BNComplex].[dbo].[prcCreateDeviceAndObjectByDevice]\''.self::addZeroTodeviceId($device_id).'\',13,1,1,5,\''.$num_auto.'\',\''.$num_auto.'\',\''.$tel.'\',1')->execute();
            return true;
        }catch (Exception $e){
            echo $e->getMessage();
            return false;
        }
    }

    private static function GetDeviceIdByNum($deviceId){
        return Yii::app()->BNComplex->createCommand("SELECT [BNComplex].[dbo].[fnGetDeviceIdByNum]('".self::addZeroTodeviceId($deviceId)."')")->queryScalar();
    }

    public static function checkDeviceIdInBn($deviceId){       //Проверка на существование DevNum в Devices
        return (Yii::app()->BNComplex->createCommand("select count(DevNum) from [BNComplex].[dbo].[Devices] where DevNum='".self::addZeroTodeviceId($deviceId)."'")->queryScalar() > 0) ? false : true ;
    }

    public static function inTruck($plate, $id){
        if( $model = TrucksGlonass::model()->find("plate='".$plate."'") ){
            $model->nat_id = $id;
        }else{
            $model = new TrucksGlonass;
            $model->nat_id = $id;
            $model->plate = $plate;
        }
        $model->save();
    }

    public static function reventGLonassData($glonass, $old_device_id){
        $glonass->device_id = $old_device_id;
        $glonass->save(false);
    }

    public static function saveDeviceId($post){

        try {
            if (!$glonass = Glonass::model()->findByPk($post->glonass_id)) return 2;
            $old_device_id = $glonass->device_id;
            //  if( $glonass->device_id == $post->device_id ) return 3;
            if ($glonass->device_id != $post->device_id){
                if (!self::checkDeviceIdinGlonass($post->glonass_id, $post->device_id)) return 1;
              //  if (!self::checkDeviceIdInBn($post->device_id)) return 4;
            }

            $glonass->device_id = $post->device_id;
            $phone_in_bn = $glonass->tel;
            if ($glonass->save(false)) {
                if ($model = Trucks::model()->find('`plate`=:plate', array(":plate" => $glonass->num_auto))){
                    if (($post->old_sim_number != $post->sim) && trim(strlen($post->sim))>5){
                        if (!$s = Sims::model()->find("SUBSTR(sim_num, -6) = :sim_num", array(':sim_num' => $post->sim))) {
                            self::reventGLonassData($glonass, $old_device_id);
                            return 8;
                        }
                        $model->number_sim = $s->sim_num;
                        $model->phone_sim = $s->phone_num;
                        $model->save(false);
                    }
                    if( $model->phone_sim && strlen($model->phone_sim) >2 ) $phone_in_bn = $model->phone_sim;
                }

                /*     $sql = 'SET CONCAT_NULL_YIELDS_NULL ON
                         SET ANSI_WARNINGS ON
                         SET ANSI_PADDING ON
                         SET ARITHABORT ON';
                     Yii::app()->BNComplex->createCommand($sql)->queryAll();*/

                if (!self::checkFailDeviceId($post->device_id)) self::inTruck($glonass->num_auto, $glonass->id);

                $check = false;
                if(self::checkDeviceIdInBn($post->device_id)){
                    $check = true;
                    if(self::checkFailDeviceId($old_device_id)){ //если нет нового прибора и старый по-умолчанию
                        if (!self::GlonassInBnComplex($glonass->device_id, $glonass->num_auto, $phone_in_bn)) {
                            self::reventGLonassData($glonass, $old_device_id);
                            return 5;
                        }
                    }else{ //если нет нового прибора и старый не по-умолчанию
                        if (!self::updateGlonassInBnComplex($phone_in_bn, $old_device_id, $glonass->device_id, $glonass->num_auto)){
                            self::reventGLonassData($glonass, $old_device_id);
                            return 5;
                        }
                    }
                }

                if(!$check && !self::checkDeviceIdInBn($post->device_id)){ //если такой есть уже
                    $pre_old_device_id = $old_device_id;
                    if (self::checkFailDeviceId($old_device_id)) $old_device_id = $glonass->device_id; //если старый по-умолчанию
                    if (!self::updateGlonassInBnComplex($phone_in_bn, $old_device_id, $glonass->device_id, $glonass->num_auto, true)) {
                        self::reventGLonassData($glonass, $pre_old_device_id);
                        return 5;
                    }
                }

                return 7; //Все ок!
            } else { return 6; }

        }catch (Exception $e){
            return array('status'=>401,'message'=> $e->getMessage() );
        }
    }

    public static function checkFailDeviceId($deviceId){
        return (in_array($deviceId, array(1, 0, 11111111, 'testtest')));
    }

    public static function checkDeviceIdinGlonass($id, $device_id){
        return Glonass::model()->count('(device_id=:device_id) AND id!=:id',array(":device_id"=>$device_id,":id"=>$id))>0?false:true;
    }

    public static function addZeroTodeviceId( $deviceId ){
        $deviceId = trim($deviceId);
        if(strlen($deviceId)<8){
            do{
                $deviceId = 0..$deviceId;
            } while(strlen($deviceId) < 8);
        }
        return $deviceId;
    }

	
	public static function getBn($deviceId){
			if($model = Yii::app()->BNComplex->createCommand('exec [BNComplex].[dbo].[GetLastCoordByDeviceId]'.self::addZeroTodeviceId($deviceId).'')->queryRow()){
				if(strpos($model['NavTime'],'PM')>0 OR strpos($model['NavTime'],'AM')>0){
					return date('Y-m-d H:i:s',strtotime( substr($model['NavTime'],0,20)." ".substr($model['NavTime'],24) ) + 3*60*60 );
				}else return date('Y-m-d H:i:s', strtotime(substr($model['NavTime'],0,20)) + 3*60*60 );
			}else return 'null';
	}
	
    public static function getlastcoordinatebydeviceid($deviceId){ //дата последней координаты
		if (strlen($deviceId) == 0 OR self::checkFailDeviceId($deviceId)) return 'null';
        $url = 'http://glonassapi.azurewebsites.net/api/Coord?serial='.str_pad($deviceId, 8, 0, STR_PAD_LEFT);
        $result = @file_get_contents($url,true, stream_context_create(array(
            'http' => array(
                'method'  => 'GET',
                'timeout' => 2,
                'header'  => 'Content-type: application/x-www-form-urlencoded',
            )
        )));
        $j = json_decode($result,true);
        $a = json_decode($j);
        if($a && $a->Longitude>0){
			
			if((int)substr($a->Time,6,10) < (time() - 60*60*24*2)) return self::getBn($deviceId);
			else return date("Y-m-d H:i:s",substr($a->Time,6,10));
			
        }else{
			return self::getBn($deviceId);
			/*if($model = Yii::app()->BNComplex->createCommand('exec [BNComplex].[dbo].[GetLastCoordByDeviceId]'.self::addZeroTodeviceId($deviceId).'')->queryRow()){
				if(strpos($model['NavTime'],'PM')>0 OR strpos($model['NavTime'],'AM')>0){
					return date('Y-m-d H:i:s',strtotime( substr($model['NavTime'],0,20)." ".substr($model['NavTime'],24) ) + 3*60*60 );
				}else return date('Y-m-d H:i:s', strtotime(substr($model['NavTime'],0,20)) + 3*60*60 );
			}else return 'null';*/
		}
       /* if(strlen($deviceId) == 0 OR self::checkFailDeviceId($deviceId)) return 'null';
        if($model = Yii::app()->BNComplex->createCommand('exec [BNComplex].[dbo].[GetLastCoordByDeviceId]'.self::addZeroTodeviceId($deviceId).'')->queryRow()){
            if(strpos($model['NavTime'],'PM')>0 OR strpos($model['NavTime'],'AM')>0){
                return date('Y-m-d H:i:s',strtotime( substr($model['NavTime'],0,20)." ".substr($model['NavTime'],24) ) + 3*60*60 );

            }else return date('Y-m-d H:i:s', strtotime(substr($model['NavTime'],0,20)) + 3*60*60 );
        }else return 'null';*/
    }

    public static function DeleteDeviceAndObject($device_id){
        $device_num = self::GetDeviceIdByNum( self::addZeroTodeviceId($device_id) );
        Yii::app()->BNComplex->createCommand('EXEC [BNComplex].[dbo].[prcDeleteDevice]\''.$device_num.'\'')->execute();
    }

    public static function updateGlonassInBnComplex($tel, $old_device_id, $new_device_id, $num_auto, $delete = false){
        try{
            $tel = (strlen($tel)>0)? $tel : 0 ;
            $currentDevId = ( $old_device_id != $new_device_id )? $new_device_id : $old_device_id;

            $device = self::GetDeviceIdByNum( self::addZeroTodeviceId($currentDevId) );
            if(Yii::app()->BNComplex->createCommand('EXEC [BNComplex].[dbo].[prcUpdateDeviceAndObjectByDevice]'.$device.',1,\''.self::addZeroTodeviceId($new_device_id).'\',13,1,1,5,\''.$num_auto.'\',\''.$num_auto.'\',\''.$tel.'\',1')->execute()){
                if( $old_device_id != $new_device_id && $delete) {
                  self::DeleteDeviceAndObject($old_device_id);
                }
            }
            return true;
        }catch (Exception $e){
          //  echo $e->getMessage();
            return false;
        }
    }
}