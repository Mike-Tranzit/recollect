<?php
/**
 * Created by JetBrains PhpStorm.
 * User: PC
 * Date: 27.11.12
 * Time: 3:57
 * To change this template use File | Settings | File Templates.
 */
class MYDate
{

    public static function showComments($date){
        $ex = explode(" ",$date);
        $tx = explode(":",$ex[1]);
        $ye = explode("-",$ex[0]);
        return $tx[0].":".$tx[1]." ".$ye[2]."-".$ye[1];
    }

    public static function showDate($date) {
        if($date=='0000-00-00'){
            return "Дата не указанна";
        }
        $array=array('01'=>"января",
            '02'=>"февраля",
            '03'=>"марта",
            '04'=>"апреля",
            '05'=>"мая",
            '06'=>"июня",
            '07'=>"июля",
            '08'=>"августа",
            '09'=>"сентября",
            '10'=>"октября",
            '11'=>"ноября",
            '12'=>"декабря");

        $no_zero=array('01'=>"1",
            '02'=>"2",
            '03'=>"3",
            '04'=>"4",
            '05'=>"5",
            '06'=>"6",
            '07'=>"7",
            '08'=>"8",
            '09'=>"9");
        $new_date=explode("-",$date);
        if(strpos($date,":")>0){
            $time=explode(" ",$new_date[2]);
            return(in_array($time[0],$no_zero))?$no_zero[$time[0]]." ".$array[$new_date[1]]." ".$new_date[0]." (".$time[1].")":$time[0]." ".$array[$new_date[1]]." ".$new_date[0]." (".$time[1].")";
        }else{
            return(in_array($new_date[2],$no_zero))?$no_zero[$new_date[2]]." ".$array[$new_date[1]]." ".$new_date[0]:$new_date[2]." ".$array[$new_date[1]]." ".$new_date[0];
        }

    }
}
