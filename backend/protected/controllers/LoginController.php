<?php

class LoginController extends Controller
{
    public function actionAuthentication()
	{
     //   echo "23423";


      //  Yii::$app->request->post();


        header('Access-Control-Allow-Origin: *');
        // print_r($_POST);die();
        $post = json_decode($_POST['data']);
        $user = Users::getCurrentModel($post->login,$post->password);
        if(!$user) {
            echo CJSON::encode(array('status'=>401));
        }else {
            echo CJSON::encode(array('status'=>200,'currUser'=>array('role'=>(int)$user->role,'id'=>$user->id,'name'=>$user->name,'token'=> md5(decbin(rand(0,1024))))));
        }
    }
}
