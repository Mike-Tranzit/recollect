<?php header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

include 'db.php';
$result = array( );
try{
   if(isset($_GET['plate'])) {
       mysqli_select_db($link, "glonass_crm");
       $res = mysqli_query($link, "SELECT payments.*,trucks.balance_license_fee as balance_license_fee from glonass_crm.payments LEFT join trucks on trucks.plate = payments.plate where payments.`plate`='".$_GET['plate']."' order by `date` desc");

       while ($info = mysqli_fetch_array($res)){
           array_push($result,$info);
       }
    }
}catch (Exception $e){
   /* $result['status'] = 'false';
    $result['errorMsg'] = $e->getMessage();*/
}
mysqli_close($link);
echo json_encode($result);