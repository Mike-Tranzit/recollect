<?php

class LoginController extends Controller
{
    public function actionAuthentication()
	{
        header('Access-Control-Allow-Origin: *');
        $post = json_decode($_POST['data']);
        $user = Users::getCurrentModel($post->login,$post->password);
        if(!$user) {
            echo CJSON::encode(array('status'=>401));
        }else {
            echo CJSON::encode(array('status'=>200,'currUser'=>array('role'=>(int)$user->role,'id'=>$user->id,'name'=>$user->name,'token'=> md5(decbin(rand(0,1024))))));
        }
    }
}