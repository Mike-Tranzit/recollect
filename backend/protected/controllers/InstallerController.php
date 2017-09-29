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

    public function actionupdateDeviceId(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $statusId = BNComplex::saveDeviceId($post);
        $status = ($statusId!=7)?401:200;
        $status_array = array( 2 => '������ � glonass �� ��������', 3 => 'ID ������� �� ���������', 1 => '����� ID ������� ��� ���� � glonass', 4 => '����� ID ������� ��� ���� � BN', 5 => '������ ���������� � BN' , 6 => '������ ���������� � glonass', 7 => '������!');
        echo CJSON::encode(array('status'=>$status,'message'=> $status_array[ $statusId ] ));
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
            ));
        }
        echo CJSON::encode($data_result);
    }

    public function actiongetLastCoordinate(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        echo CJSON::encode(array('last_coordinate'=>BNComplex::getlastcoordinatebydeviceid($post->device_id)));
    }

    public function actiongetObjectForInstallerById(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $info = TasksForInstallers::getTask( $post->id );
        $sanctionData = $this->formSanctionData($info['obj_id']);
        echo CJSON::encode(array(
            "id" => (int)$info['id'],
            "plate" => $info['plate'],
            "date_create" => $info['date_create'],
            "status" => (int)$info['status'],
            "type" => (int)$info['type'],
            'glonass_id'=>(int)$info['glonass_id'],
            "obj_id" => $info['obj_id'],
            'sanction'=>$sanctionData['status_sanction'],
            'date_sanction'=>$sanctionData['date_sanction'],
            'last_coordinate'=>'wait',//BNComplex::getlastcoordinatebydeviceid($info['deviceId']),
            'act'=> $info['act']?$info['act']:'',
            'device_id' => $info['deviceId'],
            'phone' => $info['phone']
        ));
    }
}