<?php header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');

include 'db.php';
$result = array('status'=>'true');
$formData = json_decode(file_get_contents('php://input'));
try{
    mysqli_select_db($link,"glonass");
    mysqli_query($link,"delete from glonass.`reminder_glonass_porttranzit` where id=".(int)$formData->id);
}catch (Exception $e){
    $result['status'] = 'false';
    $result['errorMsg'] = $e->getMessage();
}
mysqli_close($link);
echo json_encode($result);