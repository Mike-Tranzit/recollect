<?php

class BalanceController extends Controller
{
    public function actiongetPayments(){
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        $result = Objects::getPayments($_GET['plate']);
        $psyments = array();
        foreach ($result as $item){
            array_push($psyments,$item);
        }
        echo CJSON::encode($psyments);
    }

}