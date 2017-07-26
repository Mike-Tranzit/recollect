<?php
header("Access-Control-Allow-Origin: *");

$user="all";
$pas="1111";
$host='192.168.2.104';
$link=mysql_connect($host,$user,$pas);
mysql_query("SET NAMES 'cp1251'");
mysql_query("SET CHARACTER SET 'cp1251'");

mysql_select_db("glonass",$link);
$res = mysql_query("SELECT *,`reminder_glonass_porttranzit`.id as ids FROM `reminder_glonass_porttranzit` LEFT JOIN nztmodule3.objects on status_m in (1,2,3) and del=0 and nztmodule3.objects.num_auto = reminder_glonass_porttranzit.num_auto where pid is not null order by nztmodule3.objects.status_m desc",$link);

$data = array();
while ($info = mysql_fetch_array($res)){
    $inf = mysql_fetch_array(mysql_query("select * from nztmodule3.glonass where num_auto='" . $info["num_auto"] . "' and deleted=0 order by id desc", $link));
    $result = mysql_query("SELECT * from glonass.comment where num_auto='" . $info["num_auto"] . "'", $link);
    $comment = "";
    while ($dd = mysql_fetch_array($result)) {
        $comment .= "" . $dd["dates"] . " " . $dd["comment"] . "";
    }
    array_push($data,array(
        'status'=>$info["status_m"],
        'plate'=>$info["num_auto"],
        'phone'=>$inf["tel"] . " " . $inf["tel1"],
        'date'=>$info["date_cre"],
        'date_create'=>$info["dates"],
        'comment'=>iconv("windows-1251","UTF-8",$comment),
        'main_comment'=>iconv("windows-1251","UTF-8",$info['description'])
    ));
}
echo json_encode($data);