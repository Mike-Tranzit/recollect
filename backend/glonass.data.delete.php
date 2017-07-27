<?php header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');


include 'db.php';
$result = array('status'=>'true','errorMsg'=>'Content-type is not allowed');
try{
    mysqli_select_db($link,"glonass");
   // mysqli_query("delete from glonass.`reminder_glonass_porttranzit` where id=".(int)$_GET['id']);
}catch (Exception $e){
    $result['status'] = 'false';
    $result['errorMsg'] = 'Content-type is not allowed';
}
echo json_encode($result);