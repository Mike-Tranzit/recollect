<?php

class InstallerController extends Controller
{

    public function actionupdateAct(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $statusId = Trucks::addAct($post);
        $status_array = array( 0 => '������ �� �������', 1 => '������!', 2 => '������ ����������.' );
        $status = ($statusId!=1) ? 401 : 200;
        echo CJSON::encode(array( 'status'=>$status,'message'=>$status_array[$statusId] ));
    }

    public function actionupdateCommentInstaller(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $statusId = (TasksForInstallers::updateTaskComment($post))?401:200;
        $status_array = array( 200 => '������!', 401=> '������ ����������');
        echo CJSON::encode(array('status'=>$statusId,'message'=> $status_array[ $statusId ] ));
    }

    public function actioncheckMassCoordinates(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $return_array = array();
        foreach ($post->arrDeviceId as $item){
            $plate = '';
            $device = $item->deviceId;
            $last_coordinate = '';

            if( strlen($item->deviceId) > 5 ){
                $field = (ctype_digit($item->deviceId))? 'device_id' : 'num_auto';
                $item->deviceId = iconv("UTF-8","windows-1251",$item->deviceId);
                $item->deviceId = MYChtml::check_num($item->deviceId);
                if($glonass = Glonass::model()->find(array('condition'=>'`'.$field.'`="'.$item->deviceId.'"'))){
                    if($field == 'device_id'){
                        $last_coordinate = BNComplex::getlastcoordinatebydeviceid((string)$item->deviceId);
                    }else{
                        $last_coordinate = ($glonass)? BNComplex::getlastcoordinatebydeviceid((string)$glonass->device_id):'null';
                    }
                    $device = iconv("windows-1251","UTF-8",MYChtml::view_num($item->deviceId));
                    $plate = ($glonass && $field == 'device_id')?$glonass->num_auto:'';
                }
                if(!$glonass && $field == 'device_id'){ //����� ������
                    if (BNComplex::checkDeviceIdInBn($device)){
                        $last_coordinate = (!BNComplex::GlonassInBnComplex($device, '', '')) ? 'fail_new' : 'success_new' ;
                    }else{
                        $last_coordinate = BNComplex::getlastcoordinatebydeviceid((string)$item->deviceId);
                    }
                }
                if(!$glonass && $field == 'num_auto'){
                    $last_coordinate = 'not_exist';
                }
                }
            $return_array = $this->addToResult($return_array, $plate, $device, $last_coordinate);
        }
        echo json_encode($return_array);
    }

    public function addToResult($return_array, $plate, $device, $last_coordinate){
        array_push($return_array,array(
            'plate'=> $plate,
            'deviceId'=> $device,
            'last_coordinate'=>$last_coordinate
        ));
        return $return_array;
    }

    public function actionupdateStatus(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $statusId = TasksForInstallers::updateTask($post);
        $status = ($statusId!=2)?401:200;

        $status_array = array( 0 => '������ �� ��������', 1=> '������ ����������' , 2 => '�� ����� ������ � ������!');
        if( $post->status == 0 ) $status_array[2] = '�� ���������� �� ������';
        echo CJSON::encode(array('status'=>$status,'message'=> $status_array[ $statusId ] ));
    }

    public function actionupdateDeviceId(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $statusId = BNComplex::saveDeviceId($post);
        if(is_array($statusId)) echo CJSON::encode($statusId);
        else{
            $status = ($statusId!=7)?401:200;
            $status_array = array( 2 => '������ � glonass �� ��������', 3 => 'ID ������� �� ���������', 1 => '����� ID ������� ��� ���� � glonass', 4 => '����� ID ������� ��� ���� � BN', 5 => '������ ���������� � BN' , 6 => '������ ���������� � glonass', 7 => '������!', 8 =>'������ ����� SIM �� ������ � �������' );
            echo CJSON::encode(array('status'=>$status,'message'=> $status_array[ $statusId ] ));
        }

    }

    public function actioncompleteTask(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $statusId = TasksForInstallers::completeTask($post);
        $status = ($statusId!=2)?401:200;
        $status_array = array( 0 => '������ �� ��������', 1=> '������ ����������' , 2 => '������ ������� �������!');
        echo CJSON::encode(array('status'=>$status,'message'=> $status_array[ $statusId ] ));
    }

    public function actiongetTasksList(){
        header('Access-Control-Allow-Origin: *');
        $data = TasksForInstallers::getTasksForInstaller();
        $data_result = array();
        foreach($data as $info) {
            $sanctionData = $this->formSanctionData($info['obj_id']);
            array_push($data_result, array(
                "id" => (int)$info['id'],
                "plate" => $info['plate'],
                "date_create" => $info['date_create'],
                "type" => (int)$info['type'],
                'sanction'=>$sanctionData['status_sanction'],
                'date_sanction'=>$sanctionData['date_sanction'],
                'stat'=>(int)$info['stat'],
                'date_from'=>$info['date_fr'],
                'date_take'=>$info['date_take'],
                'user_take'=>(int)$info['user_id_take'],
                'user_name'=>$info['name'],
                'status'=>$info['status'],
                'dispatcher_comment' => $info['dispatcher_comment']?$info['dispatcher_comment']:''
            ));
        }
        echo CJSON::encode($data_result);
    }

    public function actiongetLastCoordinate(){
        header('Access-Control-Allow-Origin: *');
        $model = new TasksForInstallers();
        $post = json_decode($_POST['data']);
        echo CJSON::encode(array('last_coordinate'=>BNComplex::getlastcoordinatebydeviceid($post->device_id)));
    }

    public function actiongetObjectForInstallerById(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $info = TasksForInstallers::getTask( $post->id );
        $sanctionData = $this->formSanctionData($info['obj_id']);
        if( (int)$info['obj_id'] == 0 ){
            $LastPhone = TasksForInstallers::getLastPhone($info['plate']);
            if($LastPhone) {
                $phone = $LastPhone['tel'];
                if((int)$LastPhone['status_m'] == 1){
                    TasksForInstallers::updateTaskByPk((int)$info['id'],$LastPhone['id']);
                    $info['obj_id'] = $LastPhone['id'];
                }
            }else{
               $gl = TasksForInstallers::getGlonassPhone((int)$info['glonass_id']);
               $phone = (substr($gl['tel'],0,2) == '+7')?substr($gl['tel'],2):$gl['tel'];
            }
        }else $phone = $info['phone'];

        echo CJSON::encode(array(
            "id" => (int)$info['id'],
            "plate" => $info['plate'],
            "date_create" => $info['date_create'],
            "status" => (int)$info['status'],
            "userGet" => (int)$info['user_id_take'],
            "type" => (int)$info['type'],
            'glonass_id'=>(int)$info['glonass_id'],
            "obj_id" => (int)$info['obj_id'],
            'sanction'=>$sanctionData['status_sanction'],
            'date_sanction'=>$sanctionData['date_sanction'],
            'last_coordinate'=>'wait',//BNComplex::getlastcoordinatebydeviceid($info['deviceId']),
            'act'=> $info['act']?$info['act']:'',
            'device_id' => $info['deviceId'],
            'phone' =>$phone,
            'sim'=>$info['sim']?$info['sim']:'',
            'installer_comment' => $info['installer_comment']?$info['installer_comment']:'',
            'dispatcher_comment' => $info['dispatcher_comment']?$info['dispatcher_comment']:'',
            'tarifts' => Tarifs::getTarifts((int)$info['type'])
        ));
    }

    /*                      */

    public function actionupdateSim(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $statusId = Trucks::updateSim( $post );
        $status = ($statusId==2)?200:401;
        $status_array = array( 0 => 'SIM �� ������� � ��', 2 => '�������');
        echo CJSON::encode(array('status'=>$status,'message'=> $status_array[ $statusId ] ));
    }

    public function actiongetTruckForInstallerById()
    {
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $info = Trucks::model()->findByPk( $post->id );
        $objects = Objects::getPhoneAutoByPlate($info['plate']);

        echo CJSON::encode(array(
            "id" => (int)$info['id'],
            "plate" => $info['plate'],
            'act'=> $info['act_number']?$info['act_number'] .' ���.: '. $objects['tel']:'���.: ' . $objects['tel'],
            'sim'=>$info['number_sim']?substr($info['number_sim'],-6):''
        ));
    }

    public function actiongetTrucks()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        $result = Trucks::getTrucksSim();
        $to_browser = array();
        foreach ($result as $item) {
            array_push($to_browser, array(
                'id' => $item['id'],
                'plate' => $item['plate']
            ));
        }
        echo CJSON::encode($to_browser);
    }

    /*                      */
}