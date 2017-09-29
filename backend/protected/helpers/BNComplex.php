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

    public static function saveDeviceId($post){

        if(!$glonass = Glonass::model()->findByPk($post->glonass_id)) return 2;
        $old_device_id = $glonass->device_id;
        if( $glonass->device_id == $post->device_id ) return 3;
        if(!self::checkDeviceIdinGlonass($post->glonass_id,$post->device_id)) return 1;
        if(!self::checkDeviceIdInBn($post->device_id)) return 4;
        $glonass->device_id = $post->device_id;

        if($glonass->save(false) ){

       /*     $sql = 'SET CONCAT_NULL_YIELDS_NULL ON
			    SET ANSI_WARNINGS ON
			    SET ANSI_PADDING ON
			    SET ARITHABORT ON';
            Yii::app()->BNComplex->createCommand($sql)->queryAll();*/

            if(!in_array($post->device_id,array(1,0,11111111,'testtest'))) self::inTruck($glonass->num_auto, $glonass->id);

            if(in_array($old_device_id,array(1,0,11111111,'testtest'))){
                if(!self::GlonassInBnComplex($glonass->device_id,$glonass->num_auto,$glonass->tel)) return 5;
            }else{
                if(!self::updateGlonassInBnComplex($glonass->tel,$old_device_id,$glonass->device_id,$glonass->num_auto)) return 5;
            }
            return 7;
        }else return 6;
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

    public static function getlastcoordinatebydeviceid($deviceId){ //дата последней координаты
        return '2017-09-29 16:37:00';
        if (strlen($deviceId) == 0) return 'null';
        if($model = Yii::app()->BNComplex->createCommand('exec [BNComplex].[dbo].[GetLastCoordByDeviceId]'.self::addZeroTodeviceId($deviceId).'')->queryRow()){
            return substr($model['NavTime'],0,19);
        }else return 'null';
    }


    public static function updateGlonassInBnComplex($tel, $old_device_id, $new_device_id, $num_auto){
         try{
             $tel = (strlen($tel)>0)? $tel : 0 ;
             $device = self::GetDeviceIdByNum( self::addZeroTodeviceId($old_device_id) );
             Yii::app()->BNComplex->createCommand('EXEC [BNComplex].[dbo].[prcUpdateDeviceAndObjectByDevice]'.$device.',1,\''.self::addZeroTodeviceId($new_device_id).'\',13,1,1,5,\''.$num_auto.'\',\''.$num_auto.'\',\''.$tel.'\',1')->execute();
             return true;
         }catch (Exception $e){
             echo $e->getMessage();
             return false;
         }
    }
}