<?php header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');


include 'db.php';
$result = array( 'status'=>'true');
$formData = json_decode(file_get_contents('php://input'));
try{
    mysqli_select_db($link,"glonass");
    mysqli_query($link,"INSERT into glonass.comment values(0,'".mysqli_real_escape_string($link,iconv("UTF-8","windows-1251",$formData->body->text))."',now(),'".mysqli_real_escape_string($link,$formData->body->plate)."')");
}catch (Exception $e){
    $result['status'] = 'false';
    $result['errorMsg'] = $e->getMessage();
}
echo json_encode($result);