<?php

class TimeslotsController extends Controller
{
    public function actiongetTimeslots(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        $result = Objects::getTimeslots();
        $payments = array();
        foreach ($result as $item){
            if((int)$item["device_id"] == 0){
                $date_last = 'null';
            }else $date_last = BNComplex::getlastcoordinatebydeviceid($item['device_id']);
            $relation = ($date_last != 'null' && strtotime($date_last) > (time() - 60*60))?1:2;
            array_push($payments,array(
                'ids'=>$item['ids'],
                'plate'=>$item["num_auto"],
                'relation'=>$relation,
                'date_last_coordinate' => $date_last,
                'phone'=>$item["tel"] . " " . $item["tel1"],
                'deviceId'=>$item["device_id"],
                'date_create'=>$item["dates"],
                'date_window'=>$item["windows"],
                'comment'=>Objects::getComments($item["num_auto"]),
                'main_comment'=>$item['description']
            ));

           // array_push($payments,$item);
        }
        echo CJSON::encode($payments);
    }
}