<?php
header("Access-Control-Allow-Origin: *");

include 'db.php';

mysqli_select_db($link,"glonass");
$res = mysqli_query($link,"SELECT *,`reminder_glonass_porttranzit`.id as ids FROM glonass.`reminder_glonass_porttranzit` LEFT JOIN cms.autos on cms.autos.num_auto = reminder_glonass_porttranzit.num_auto where date(windows) = curdate() and prov is not null order by windows");

$data = array();
while ($info = mysqli_fetch_array($res)){
    $inf = mysqli_fetch_array(mysqli_query($link,"select * from nztmodule3.glonass where num_auto='" . $info["num_auto"] . "' and deleted=0 order by id desc"));
    $result = mysqli_query($link,"SELECT * from glonass.comment where num_auto='" . $info["num_auto"] . "'");
    $comment = "";
    while ($dd = mysqli_fetch_array($result)) {
        $comment .= "" . $dd["dates"] . " " . $dd["comment"] . " ";
    }

    $rr = 0;
 /*   $r = mysqli_query($link,"SELECT SUM(`amount_installation`) as amount_inst from glonass_crm.payments where plate='".$info["num_auto"]."'");

    while ($row = mysqli_fetch_assoc($r)){
        $rr = $row['amount_inst'];
    }
    if( trim(strlen($rr)) == 0) $rr = 0;*/

    $relation = ($inf["device_id"]!=0 && $inf['date_last_coordinate'] && strtotime($inf['date_last_coordinate']) > (time() - 60*60))?1:2;

    array_push($data,array(
        'ids'=>$info['ids'],
        'plate'=>$info["num_auto"],
      //  'balance'=>$rr,
        'relation'=>$relation,
        'phone'=>$inf["tel"] . " " . $inf["tel1"],
        'deviceId'=>$inf["device_id"],
        'date_create'=>$info["dates"],
        'date_window'=>$info["windows"],
        'comment'=>iconv("windows-1251","UTF-8",$comment),
        'main_comment'=>iconv("windows-1251","UTF-8",$info['description'])
    ));
}
echo json_encode($data);