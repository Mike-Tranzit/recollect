<?php

/**
 * Created by JetBrains PhpStorm.
 * User: PC
 * Date: 27.11.12
 * Time: 3:57
 * To change this template use File | Settings | File Templates.
 */
class SendSms
{

    public static function smsGo($message, $phone)
    {
        if (substr($phone, 0, 1) != '+') $phone = "+".$phone;
        try {
             /*file_get_contents('http://192.168.2.200:84/sendSms/?format=json', false, stream_context_create(array(
                 'http' => array(
                     'method'  => 'POST',
                     'header'  => 'Content-type: application/x-www-form-urlencoded',
                     'content' => http_build_query(array(
                         'Phone' => str_replace(" ","",$phone),
                         'Message' => str_replace("\n","\r\n",$message)
                     ))
                 )
             )));*/
            $message = "NTC_CONNECT 89.208.152.55 8100 ".$message;
            $message = str_replace("\n"," ",$message);
            Yii::app()->db->createCommand()->insert('cms.for_send_sms', array(
                'phone' => str_replace(" ","",$phone),
                'message' => $message,
                'modem_id' => 2
            ));
            return 'true';
        } catch (Exception $e) {
            // echo $e->getMessage();
            return 'false';
        }
    }
}