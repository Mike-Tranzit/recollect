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
    static function _date_range_limit($start, $end, $adj, $a, $b, $result)
    {
        if ($result[$a] < $start) {
            $result[$b] -= intval(($start - $result[$a] - 1) / $adj) + 1;
            $result[$a] += $adj * intval(($start - $result[$a] - 1) / $adj + 1);
        }

        if ($result[$a] >= $end) {
            $result[$b] += intval($result[$a] / $adj);
            $result[$a] -= $adj * intval($result[$a] / $adj);
        }

        return $result;
    }

    static function _date_range_limit_days($base, $result)
    {
        $days_in_month_leap = array(31, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        $days_in_month = array(31, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

        self::_date_range_limit(1, 13, 12, "m", "y", &$base);

        $year = $base["y"];
        $month = $base["m"];

        if (!$result["invert"]) {
            while ($result["d"] < 0) {
                $month--;
                if ($month < 1) {
                    $month += 12;
                    $year--;
                }

                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;
            }
        } else {
            while ($result["d"] < 0) {
                $leapyear = $year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0);
                $days = $leapyear ? $days_in_month_leap[$month] : $days_in_month[$month];

                $result["d"] += $days;
                $result["m"]--;

                $month++;
                if ($month > 12) {
                    $month -= 12;
                    $year++;
                }
            }
        }

        return $result;
    }

    static function _date_normalize($base, $result)
    {
        $result = self::_date_range_limit(0, 60, 60, "s", "i", $result);
        $result = self::_date_range_limit(0, 60, 60, "i", "h", $result);
        $result = self::_date_range_limit(0, 24, 24, "h", "d", $result);
        $result = self::_date_range_limit(0, 12, 12, "m", "y", $result);

        $result = self::_date_range_limit_days(&$base, &$result);

        $result = self::_date_range_limit(0, 12, 12, "m", "y", $result);

        return $result;
    }


    static function _date_diff($one, $two)
    {
        $invert = false;
        if ($one > $two) {
            list($one, $two) = array($two, $one);
            $invert = true;
        }

        $key = array("y", "m", "d", "h", "i", "s");
        $a = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $one))));
        $b = array_combine($key, array_map("intval", explode(" ", date("Y m d H i s", $two))));

        $result = array();
        $result["y"] = $b["y"] - $a["y"];
        $result["m"] = $b["m"] - $a["m"];
        $result["d"] = $b["d"] - $a["d"];
        $result["h"] = $b["h"] - $a["h"];
        $result["i"] = $b["i"] - $a["i"];
        $result["s"] = $b["s"] - $a["s"];
        $result["invert"] = $invert ? 1 : 0;
        $result["days"] = intval(abs(($one - $two)/86400));

        if ($invert) {
            self::_date_normalize(&$a, &$result);
        } else {
            self::_date_normalize(&$b, &$result);
        }

        return $result;
    }

    public static function DateAdd($interval, $number, $date) {

        @$date_time_array = getdate($date);
        $hours = $date_time_array['hours'];
        $minutes = $date_time_array['minutes'];
        $seconds = $date_time_array['seconds'];
        $month = $date_time_array['mon'];
        $day = $date_time_array['mday'];
        $year = $date_time_array['year'];

        switch ($interval) {

            case 'yyyy':
                $year+=$number;
                break;
            case 'q':
                $year+=($number*3);
                break;
            case 'm':
                $month+=$number;
                break;
            case 'y':
            case 'd':
            case 'w':
                $day+=$number;
                break;
            case 'ww':
                $day+=($number*7);
                break;
            case 'h':
                $hours+=$number;
                break;
            case 'n':
                $minutes+=$number;
                break;
            case 's':
                $seconds+=$number;
                break;
        }
        @$timestamp= mktime($hours,$minutes,$seconds,$month,$day,$year);
        return $timestamp;
    }

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
