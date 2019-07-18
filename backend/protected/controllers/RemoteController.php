<?php

class RemoteController extends Controller
{
    public function actionchecPhoneInTrucks()
    {
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $data = Trucks::getData($post);
        $status = (!$data) ? 401 : 200;
        echo CJSON::encode(array('status' => $status, 'phone' => $data['phone'], 'plate' => ($data['pl']) ? ', авто: ' . $data['pl'] : ''));
    }

    public function actionsendCookieMessage()
    {
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $data = SendSms::smsGo($post->param->text, $post->param->phone);
        $status = ($data == 'false') ? 401 : 200;
        echo CJSON::encode(array('status' => $status));
    }
}