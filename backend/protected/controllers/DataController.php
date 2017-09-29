<?php

class DataController extends Controller
{
    public function actionupdateComment(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $status = Comment::addComment($post)?200:401;
        echo CJSON::encode(array('status'=>$status));
    }



    public function actionchangeSanction(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $responce = Sanctions::setSanctionStatus($post);
        $date = (int)$responce->status == 0 ? date('Y-m-d H:i:s') : $responce->date_create;
        echo CJSON::encode( array('status'=>200,'status_sanction'=>(int)$responce->status,'date_sanction'=>MYDate::showComments($date)) );
    }

    public function actionaddTask(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $status_code = TasksForInstallers::addTask($post)?200:401;
        echo CJSON::encode(array('status'=>$status_code));
    }

    public function actiongetObjectsForDistechers(){
        header("Access-Control-Allow-Origin: *");
        $data = Objects::getObjectsForDistechers();
        $data_result = array();
        foreach($data as $info){
            $sanction = Sanctions::getSanctionStatus($info['id']);
            if($sanction){
                $status_sanction = 0;
                $date_sanction = MYDate::showComments($sanction->date_create);
            }else{
                $status_sanction = 1;
                $date_sanction = 'false';
            }
            array_push($data_result, array(
                'ids'=>$info['id'],
                'plate'=>$info["num_auto"],
                'balance'=>Objects::getAmount($info["num_auto"]),
                'phone'=>"+7".$info["tel"]."",
                'deviceId'=>$info['device_id'],
                'glonass_id'=>(int)$info['glonass_id'],
                'date_create'=>$info['dates'],
                'date_last_coordinate' => BNComplex::getlastcoordinatebydeviceid($info['device_id']),
                'new_record'=>2,
                'trello'=>0,
                'sanction'=>$status_sanction,
                'date_sanction'=>$date_sanction,
                'comment'=>Objects::getComments($info["num_auto"]),
                'main_comment'=>$info['description']
            ));
        }
        echo CJSON::encode($data_result);
    }



    public function actiongetObjectsForInstaller(){
        header("Access-Control-Allow-Origin: *");
        $data = TasksForInstallers::getTasks();
        $data_result = array();
        foreach($data as $info){
            $sanction = Sanctions::getSanctionStatus($info['id']);

            if($sanction){
                $status_sanction = 0;
                $date_sanction = MYDate::showComments($sanction->date_create);
            }else{
                $status_sanction = 1;
                $date_sanction = 'false';
            }
            array_push($data_result, array(
                "id" => $info['id'],
                "plate" => $info['plate'],
                "date_create" => $info['date_create'],
                "status" => $info['status'],
                "type" => $info['type'],
                'glonass_id'=>(int)$info['glonass_id'],
                "obj_id" => $info['obj_id'],
                'sanction'=>$status_sanction,
                'date_sanction'=>$date_sanction,
                'last_coordinate'=>0,//BNComplex::getlastcoordinatebydeviceid($info['deviceId']),
                'act'=> $info['act']?$info['act']:'',
                'device_id' => $info['deviceId']
            ));
        }
        echo CJSON::encode($data_result);
    }

}