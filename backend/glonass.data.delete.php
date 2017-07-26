<?php
header("Access-Control-Allow-Origin: *");

include 'db.php';
$result = array('status'=>'true');
try{
    mysqli_select_db($link,"glonass");
   // mysqli_query("delete from glonass.`reminder_glonass_porttranzit` where id=".(int)$_GET['id']);
}catch (Exception $e){
    $result['status'] = false;
}
echo json_encode($data);