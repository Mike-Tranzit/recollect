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
        $statusId = TasksForInstallers::addTask($post);
        $status = ($statusId!=2)?401:200;
        $status_array = array( 3 => '×òî-òî ïîøëî íå òàê, îáğàòèòåñü ê àäìèíèñòğàòîğó', 1=> 'Ìàøèíà íå íàéäåíà â ÃËÎÍÀÑÑ' , 2 => 'Çàäà÷à äîáàâëåíà óñïåøíî!');
        echo CJSON::encode(array('status'=>$status,'message'=> $status_array[ $statusId ] ));
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
            $stat = ($info['stat']!= NULL)? $info['stat']+1: (int)$info['stat'];

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
                'main_comment'=>$info['description'],
                'status'=>$stat,
                'date_take'=>$info['date_take'],
                'user_take'=>(int)$info['user_id_take'],
                'user_name'=>$info['name'],
                'installer_comment' => $info['installer_comment'],
                'dispatcher_comment' => $info['dispatcher_comment']
            ));
        }
        echo CJSON::encode($data_result);
    }

    public function actiondeleteFromReminder(){
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $status = Objects::deleteFromReminder($post->id)?200:401;
        echo CJSON::encode(array('status'=>$status));
    }
}