<?php

class AjaxController extends ControlerCPanel
{

	public function actionIndex()
	{
		$this->render('index');
	}

    /*                          */
     public function actiongetRating()
     {
        if( $model = Users::model()->findByPk($_GET['id'])) {
            $rating = UsersRating::model()->find("`user_id`=:user_id",array(":user_id"=>$model->id));
            $f = (isset($_GET['forum']) && $_GET['forum'] == 1)?true:false;
            $this->renderPartial("rating",array("rating"=>$rating,'user'=>$model, "f"=>$f));
        }
    }



    public function actionGetculture()
    {

        $culture_nvrks = array("5"=>"3-класс","4"=>"4-класс","2"=>"5-класс","3"=>"Ячмень","6"=>"Рис","7"=>"Кукуруза");
        $culture = array();

     if ($_POST["stevedore_id"] < 4 ) { $culture = $culture_nvrks; }
     if ($_POST["stevedore_id"] >= 4 ) {

         $culture_rostov = Yii::app()->db->createCommand("SELECT * from rostov.cultures ORDER  by name")->queryAll();
        if ($culture_rostov)
            foreach ($culture_rostov as $cultures) {
            $culture[$cultures["id"]] = $cultures["name"];
            }

         }
        echo CJSON::encode($culture);
    }

    public function actionsaveoccupation()
    {
        $user = Users::model()->findByPk($_POST['id']);
        $user->occupation = $_POST['occupation'];
        $user->save(false);
    }
	
    public function actionLocality($code,$name)
    {
        if(Yii::app()->request->isAjaxRequest) {
            header('Content-Type: application/json; charset=utf-8');
			$freecarrier = (isset($_GET['freecarrier']) && $_GET['freecarrier'] == 1)?true:false;
            $m = Kladr::getLocalityInRegionArrayForSelect2($code,$name,$freecarrier);
            if(in_array(Yii::app()->user->getId(),array(2047,7449))) {
                AccessoryFunctions::writeLog("User: ".Yii::app()->user->getId()." code:".$code.", name:  ".iconv("UTF-8","windows-1251",$name)." ".print_r($m,true)." ".date('Y-m-d H:i:s') );
            }
            echo  json_encode($m);
        }
    }
	
     public function actionshowWithFilterRequest()
     {
        $post_value = array('regions','stevedore', 'stevedorename', 'company','trader', 'culture','locality','priceTopic');
        foreach($post_value as $k) {
            Yii::app()->session[$k] = (strlen($_POST[$k]))?$_POST[$k]:0;
        }
    }

    public function checkUserCompany($phone)
    {
        if(!$model = Users::model()->find("`login`=:login AND occupation in(2,4,99)",array(":login"=>AccessoryFunctions::clearTel($phone)))) $this->getError('Пользователь не найден');
        return $model;
    }

    public function actionsearchUserCompany()
    {
        header('Content-Type: text/html; charset=windows-1251');
        $model = $this->checkUserCompany($_POST['phone']);
        echo  '<span style="color:green;">'.$model->name."</span>";
    }

    public function searchUserCompanyCount($company)
    {
        return Users::model()->count("company_id=:company_id",array(":company_id"=>$company)) == 5?false:true;
    }

    /**
     * Получение экспортера
     */
	public function actiongetTraders()
    {
      header('Content-Type: text/html; charset=windows-1251');
      if(in_array($_POST['id'], array(848,849))) {
          $traders = $_POST['id'] == 848?'`is_nzt`':'`is_nkhp`';
          $model = Firm::getAll($traders);
      } elseif(in_array($_POST['id'], array(843))) {
          $model = Trader::getAll(false,1);
      } else $model = Trader::getAll();
          echo FormHTML::select($model,"trader","trader","Выберите экспортера",$_POST['r'],false,"");
    }

    public function actionsaveUserCompany()
    {
        header('Content-Type: text/html; charset=windows-1251');
        if(!$this->searchUserCompanyCount($_POST['company'])) $this->getError('Максимум 5 пользователей');
        $model = $this->checkUserCompany($_POST['phone']);
        if( $model->company_id  == $_POST['company'])  $this->getError('Данный пользователь уже привязан к данной компании');
        $model->company_id = $_POST['company'];
        if($model->save( false ))  echo '<span style="color:green;">Пользователь привязан удачно</span>';
        else $this->getError('Ошибка добавления');
    }

    public  function  actionRegions()
    {
        if(Yii::app()->request->isAjaxRequest) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(Kladr::getRegions(true));
        }
    }

    public function actiongetRegion(){
        header('Content-Type: text/html; charset=windows-1251');
        $mode = (isset($_POST['cp']) && $_POST['cp']==1)?2:0;
        $all_region = Kladr::getRegions(true,$mode);
        $region = str_pad(substr($_POST['r'],0,strpos($_POST['r'],'0')),13,0);//13
		if($mode == 2) $region = $_POST['r'];
        echo FormHTML::select($all_region,"regions","regions","Выберите регион погрузки",$region);
    }

    public function actiongetStiv()
    {
        header('Content-Type: text/html; charset=windows-1251');
        $all_region = Kladr::getRegions(true);
        echo FormHTML::select($all_region,"add_region","add_region","Выберите регион выгрузки",false,false,' ');
    }

    public function actiongetStivExist()
    {
        header('Content-Type: text/html; charset=windows-1251');
        echo FormHTML::select(Stevedore::getAll(),"stevedore","stevedore","Выберите место выгрузки",$_POST['r'],true);
    }

    /*              СПИСКИ              */
    public function actionaddList()
    {
        if(Yii::app()->request->isAjaxRequest) {
            echo Lists::addList(iconv("UTF-8","windows-1251",$_POST['name']));
        }
    }

    public function actionshowList()
    {
        $lists = UsersInLists::getMyUserList(substr($_POST['id'],2));
        if(!count($lists)) { $this->renderPartial("application.views.cpanel.extend.clearListUsers",array('id'=>substr($_POST['id'],2))); die(); }
        $this->renderPartial("application.views.cpanel.extend.listUsers",array('model'=>$lists,'id'=>substr($_POST['id'],2)));
    }

    public function actionsearchAndAdd()
    {
        $login  = AccessoryFunctions::clearTel($_POST['login']);
        if(!$model = Users::searchUserForLogin($login)){
            echo 'noUser'; die();  //есть ли такой пользователь
        }
        if(UsersInLists::searchDoubleUser($_POST['pid'],$model->id)) {
            echo 'double'; die();  //поиск уществует ли уже в этом списке
        }
        if(!$new = UsersInLists::addInList($_POST['pid'],$model->id)) echo 'false'; //если не соханился то хер
        else {
            if((int)$_POST['removeRowList']==0) {
                $this->renderPartial("application.views.cpanel.extend.addNewListUsers",array('model'=>$new));
            } else {
                $this->renderPartial("application.views.cpanel.extend.addNewListUsersRow",array('model'=>$new));
            }
        }
    }

    public function actiondeleteListRow()
    {
      echo UsersInLists::deleteRow((int) $_POST['id'] );
    }

    public function actionaddToListBootboxForm()
    {
        $list = Lists::getMyList();
        $this->renderPartial("application.views.cpanel.extend.addToListBootboxForm",array('list'=>$list, 'id'=>$_POST['id']));
    }

    public function actionsearchAndAddModal()
    {
        if(UsersInLists::searchDoubleUser($_POST['pid'],$_POST['id'])) { echo 'double'; die(); }
        if(!$new = UsersInLists::addInList($_POST['pid'],$_POST['id'])) echo 'false';
        else echo 'true';
    }

    /*                              */
      public function actionDeleteonerequestshipping($id,$status)
      {
        $a = RequestShipping::model()->findByPk($id);
        $m = new RequestShippingArchive;
        $m->setAttributes( $a->getAttributes() );
        $m->status_id = $status;
        $m->date_force_closed = new CDbExpression("now()");
        $m->trader_id = ($a->trader_id == 0)?0:$a->trader_id;
        $m->id = $a->id;
        $status = 'false';
        if($m->save(false)) {
            if($a->delete()) {
                $status = 'true';
                HideRequestShipping::model()->deleteAll("request_shipping_id=".(int)$id);
            }
        } else AccessoryFunctions::writeLog("in My ".print_r($m->getErrors(),true),'go_to_archive.txt');
            echo $status;
    }

    public function actiondeleteallrequestshipping()
    {
        foreach($_POST['id'] as $item) {
            RequestShippingArchive::model()->updateByPK($item,array("status_id"=>3,'date_force_closed'=>new CDbExpression("now()")));
        }
    }

    /*                РЕДАКТИРОВАНИЕ ЗАЯВКИ              */
    public function actionEditrequestshipping($id)
    {
        $model = RequestShipping::model()->findByPk($id);
        $this->renderPartial("application.views.cpanel.extend.editRequestShipping",array("model"=>$model));
    }

    public function actionSaveRequestshippingBehindEdit()
    {
        $data = array();
        parse_str($_POST['data'],$data);
        $data['id'] = $_POST['id'];
        echo RequestShipping::editRequest( $data )?'true':'false';
    }

    /*----------------------------------------------------- */
    public function getError($text)
    {
        echo '<span style="color:red;">'.$text."</span>";
        die();
    }

    public function actionansferToRequestShipping()
    {
        $data = array();
        parse_str($_POST['data'],$data);
        header('Content-Type: text/html; charset=windows-1251');
        if(!strlen($data['trucks_count'])) $this->getError('Введите кол-во машин');
        if(!strlen($data['price'])) $this->getError('Введите цену');
        if(!ReplyShipping::checkExistReplyShipping($data['request_id']))  $this->getError('Вы уже ответили на данную заявку');
		if($m = RequestShipping::model()->findByPk($data['request_id'])) {
            $m->answers_count = $m->answers_count+1;
            $m->save(false);
        }

        echo ReplyShipping::saveData($data)?'<span style="color:green;">Ваш ответ сохранен успешно</span>':'<span style="color:red;">Ошибка сохранения, обратитесь к администратору</span>';
    }

    /*-------------Отзывы----------------*/
    public function actiongetReviewModal()
    {
        header('Content-Type: text/html; charset=windows-1251');
        $user = CompanyReviews::getAllReview($_POST['id']);
        $user_exist = CompanyReviews::getExistUser($_POST['id']);
        $this->renderPartial("application.views.cpanel.extend.".$_POST['view'],array("user"=>$user,'id'=>$_POST['request'],'user_exist'=>$user_exist,'id_company'=>$_POST['id']));
    }

    public function actionsavePreview()
    {
      echo CompanyReviews::inBase($_POST);
    }

    public function actionshowNewReview()
    {
        $user = CompanyReviews::getAllReview($_POST['id']);
        $this->renderPartial("application.views.cpanel.extend.rowReview",array("user"=>$user));
    }

    /*--------------------------------- */
    public function actiongetAnsferModalFull()
    {
		if(!MYRules::driverCheckPay()) {
            $this->renderPartial("application.views.cpanel.extend.ansfergetpay");
            die();
        }
		
        $model = RequestShipping::model()->findByPk($_POST['id']);
		 if(!$model) {
            header('Content-Type: text/html; charset=windows-1251');
            echo "Данная заявка уже закрыта"; die();
        }
        RequestShipping::updateView(false,$model);

		RequestsViews::addNewView($_POST['id']);
    }


    public function actiondeleteAnswer()
    {
        echo ReplyShipping::deleteAnswer($_POST['id']);
    }

    /*----------------------------------------------------- */
    //Какова дальнейшая цена размещения предложений по работе и как взымается плата
    private  function confirmreply($requestId,$phone, $ids)
    {
       Forwebservices::Confirmreply($requestId,$phone);
	     $this->changeReplyShippingStatus($ids,1);
    }

    private  function unconfirmreply($requestId,$phone, $ids)
    {

        Forwebservices::Unconfirmreply($requestId,$phone);
		  $this->changeReplyShippingStatus($ids,0);
    }

    private function delconfirmreply($requestId, $phones, $ids)
    {
        Forwebservices::DeleteReply($requestId,$phones);
		  $this->changeReplyShippingStatus($ids,2);
    }

	public function changeReplyShippingStatus($ids,$confirm)
    {
        if (ReplyShipping::model()->updateByPK($ids,array("confirm"=>$confirm)))
            echo json_encode(array("ids"=>$ids,"result"=>"true"));
        else echo json_encode(array("result"=>"error"));
    }
	
    public function actionaddTrader()
    {
        $model = new Trader;
        $model->firm = MYChtml::fromUTF8($_POST['trader']);
        $model->status = 1;
        echo $model->save()?$model->id:0;
    }

    public function actionaddStevedore()
    {
	    echo Stevedore::addStiv( MYChtml::fromUTF8($_POST['stevedore_locality_name']),$_POST['place_code']);
    }

    public function actionChangestatusreply()
    {
        $phones = $this->arrayToString($_GET['phones']);
        $requestId =  $_GET['requestId'];
        $ids = $_GET['ids'];
        if ($_GET['status'] == 1) $this->confirmreply($requestId,$phones,$ids);
        if ($_GET['status'] == 2) $this->delconfirmreply($requestId,$phones,$ids);
        if ($_GET['status'] == 3) $this->unconfirmreply($requestId,$phones,$ids);
    }

    private function arrayToString($array)
    {
        $string = "";
        foreach ($array as $val) {
            $string.=$val.",";
        }
        return substr($string,0,strlen($string)-1);
    }

    public function actionaddlastviewreply($id)
    {
        $lastReply =  ReplyShipping::model()->find(array("condition"=>"request_id=:id","params"=>array(":id"=>$id),"order"=>"id desc"));
        if ($lastReply) {
        }
        echo "true";
    }

    /*              УПРАВЛЕНИЕ ОТЗЫВАМИ (РЕТИНГ)            */
    public function actiondeleteRating()
    {
            $result = Rating::deleteRating($_POST['id'])?'true':'false';
            $this->renderPartial($this->$result);
    }

    public function actionsaveRating()
    {
            $result = Rating::editRating($_POST)?'true':'false';
            $this->renderPartial($this->$result);
    }

    public function actioneditRating()
    {
            if(!$rating = Rating::getRecordForEdit($_POST['id'])) $this->renderFalse();
            $this->renderPartial('/Rating/render/edit',array('rating'=>$rating));
    }

    public function actiondeleteAllRating()
    {
            $result = Rating::deleteAllRating($_POST)?'true':'false';
            $this->renderPartial($this->$result);
    }

    /*              УПРАВЛЕНИЕ FAQ (ВОПРОС/ОТВЕТ)            */
    public function actiondeleteFaq()
    {
        $result = Faq::model()->updateByPk($_POST['id'],array('status'=>2))?'true':'false';
        $this->renderPartial($this->$result);
    }

    public function actionsaveFaq()
    {
        $result = Faq::editFaq($_POST)?'true':'false';
        $this->renderPartial($this->$result);
    }

    public function actioneditFaq()
    {
        if(!$faq = Faq::model()->findByPk($_POST['id'])) $this->renderFalse();
        $this->renderPartial('/faq/render/edit',array('faq'=>$faq));
    }

    public function actiondeleteAllFaq()
    {
        $result = Faq::deleteAllFaq($_POST)?'true':'false';
        $this->renderPartial($this->$result);
    }
	
    /*                     КОММЕНТАРИИ                      */
    public function actionaddToConnemts()
    {
        $result = Comments::model()->find('driver_id=:driver_id and user_id=:user_id',array('user_id'=>Users::getCurrUser(),':driver_id'=>(int)$_POST['id']));
        header('Content-Type: text/html; charset=utf-8');
        $this->renderPartial( $_POST['view'],array('result'=>$result,'id'=>$_POST['id']));
    }

    public function actionsaveConnemts()
    {
        $model = (int)strlen($_POST['id'])>0 ?Comments::model()->findByPk($_POST['id']):new Comments;
        $model->user_id = Users::getCurrUser();
        $model->driver_id = (int)$_POST['driver'];
        $model->text = MYChtml::fromUTF8( $_POST['text'] );
        if(!$model->save()) {
            echo 0; die();
        }
        echo $model->id;
    }

     /*                     EXCEL                            */
     public function actionExcel()
     {
        if(strtotime($_POST['end']) < strtotime($_POST['start'])) {
            echo 'date_false'; die();
        }
        echo MYExcel::getStatisticExcel($_POST['start'],$_POST['end']);
    }

    public function actionlogStatisticError()
    {
        AccessoryFunctions::writeLog( $_POST['text'] );
    }

    /*                     Статистика                            */
    public function filterDataStatistic()
    {
        $start = $_POST['load'][0] !=0?strpos($_POST['load'],'0'):strpos($_POST['load'], '0', 1);
        return substr( $_POST['load'],0,$start);
    }

    public function actionGraph()
    {
        if(!strlen($_POST['load']) OR !strlen($_POST['stevedore'])){ echo 'data_false'; die(); }
        $locality_id = $this->filterDataStatistic();
        if((int)RequestShipping::getGraphModelCount($locality_id,$_POST['stevedore'],$_POST['start'],$_POST['end'])){
            $this->renderPartial( "application.views.cpanel.ajax.graph",array("post"=>$_POST)  );
        }else echo 0;
    }

    public function actiongetJsonGraph()
    {
            $locality_id = $this->filterDataStatistic();
            $model = RequestShipping::getGraphModel($locality_id,$_POST['stevedore'],$_POST['start'],$_POST['end']);
            $result = array();
            foreach($model as $item){
                $result [$item->DateCreate] = $item->requestPrice;
            }
            echo json_encode($result);
    }

    /*                                                      */
	    public function foreachPlaces($carrier, $model)
    {
        $result = array();
        foreach($model as $item) {
            if(in_array(substr($item['id'],0,5),$carrier)){
                array_push($result,$item);
            }
        }
        return $result;
    }

    public function actionLocalityOnlyExist()
    {
        header('Content-Type: text/html; charset=windows-1251');
        if(!isset($_POST['topic'])) $carrier = FreeCarriers::getCarrierForFilter($_POST['code']);
        else $carrier = RequestShipping::getRequestShippingForFilter($_POST['code']);
        $model = Kladr::getLocalityInRegionArrayForSelect2($_POST['code'],'',true);
        $result = array();
        $places = array();
        $placesCode = array();
        foreach($carrier as $k){
            array_push($placesCode,substr($k->place_code,0,5));
        }
        $result = $this->foreachPlaces($placesCode, $model);
        if(!count($result)){
            foreach($carrier as $item){
                $text = (strpos($item->place,",")>0)?substr($item->place,0,strpos($item->place,",")):$item->place;
                if(strpos($text," г")>0) $text = substr($text,0,strpos($text," г"))." город";

                if(!in_array($text,$places)){
                    array_push($result,array('id'=>$item->place_code,'text'=>MYChtml::toUTF8($item->place)));
                    array_push($places,$text);
                }
            }
        }
        $topic = (isset($_POST['topic']))?1:0;
        echo FormHTML::selectFreeCarrier($result,"locality_s","locality_s","Выберите район",$topic);
    }
	
    /*                Группировка стивидоров                */
    public function actiongroupStividor()
    {
        echo Stevedore::groupStividore($_POST);
    }

    /*                                                      */
    public function actioncheckRequestExistDelete()
    {
        RequestShipping::checkRequestExistDeleteAll($_POST);
    }

    public function actioninLog()
    {
        if(Users::getCurrUser() == 7588) {
            $parse_str = array();
            parse_str($_POST['data'],$parse_str);
            AccessoryFunctions::writeErrorLog("BeforeSave date = ".date('Y-m-d')." post - ".print_r($parse_str,true));
        }
    }

    /**
     * Восстановление заявки
     */
    public function actionupOrder()
    {
        echo RequestShipping::upOrder($_POST);
    }

    /**
     * Проверка заявки
     */
    public function actioncheckRequestExist()
    {
        echo RequestShipping::checkRequestExist($_POST);
    }

    /**
     * Получить котировки
     */
    public function actionGetQuatas()
    {
        echo Quotas::GetQuotas();
    }

    /**
     * Получить прогноза
     */
    public function actionGetPrognoz()
    {
        echo Prognoz::GetPrognoz();
    }

    /**
     * Отправка push уведомлений
     */
    public function actionSendPush()
    {
        RequestShipping::SendPush();
    }

    /*                    Повышение понижение                  */
    public function actionupRequest()
    {

        if(RequestShipping::model()->count("user_id=:id and status_id=1 and up=1",array(":id"=>$_POST['us'])) >= $_POST['allow_count'] ){
            echo 'fail';
        } else {
            RequestShipping::model()->updateByPk($_POST['id'],array('up'=>1));
            echo 'yes';
        }
    }

    public function actiondownRequest()
    {
        if(RequestShipping::model()->updateByPk($_POST['id'],array('up'=>0)))
        echo 'yes';
        else echo 'no';
    }
	
    /*                         Форум                         */
    public function actiondeleteForumTopic()
    {
        echo ForumTopics::model()->updateByPk($_POST['id'],array("deleted"=>1));
    }

	/*               Напоминание до 1 августа                */
    public function checkUserId($id)
    {
        if(strlen($id) == 0) {
            echo 0; die();
        }
    }

	public function actionSurvey()
    {
        $myAnsfer = Yii::app()->db->createCommand("SELECT * from `survey_google_docs` where user_id = :user_id")->bindValue(":user_id", Users::getCurrUser())->queryRow();
        if(!$myAnsfer || empty($myAnsfer)) {
            Yii::app()->db->createCommand("insert into `survey_google_docs` (user_id, ansfered) values (:user_id,0)")->bindValue(":user_id", Users::getCurrUser())->execute();
        } else {
            if($myAnsfer['ansfered'] == 0) {
                echo "userAnsfered"; die(); 
            }
        }

        $count = Yii::app()->db->createCommand("SELECT count(id) as cc from `survey_google_docs` where `ansfered` = 0")->queryRow();
        if($count['cc'] < 3000) {
            echo "newUser";
        }
    }
	
    public function actionpaymentWindowChange()
    {
        $this->checkUserId($_POST['id']);
        if($m = PaymentWindow::model()->find("`user_id`=:user_id",array(":user_id"=>$_POST['id']))) {
           if($_POST['status'] == 1){
               $m->status = 1;
               $m->save();
           } else {
               $date = date('Y-m-d');
               $m->date_next_show = date('Y-m-d',strtotime("{$date} +".rand(3,7)." days"));
               $m->save();
           }
        }
    }
	// 
	public function actionansferToRequestShippingCarrierDelete()
    {
        $id = (int)$_POST['row_id'];
        if($id !== 0) {
            if($model = LTrucksByUserReply::model()->find("`status`= 0 and id=:id and user_add_id = :user_add_id", array(":id" => $id, ":user_add_id" => Users::getCurrUser()))) {
                $model->status = 3;
                $model->save();
            }
        }
    }
	
	public function actionansferToRequestShippingCarrier()
    {
        $data = array();
        $id = (int)$_POST['id'];
        parse_str($_POST['data'], $data);
        $success = array();

        $reply = LTrucksByUserReply::model()->findAll("`request_id`=:request_id and `user_add_id`=:user_add_id and status in(0,1) and date_start >= date(now())", array(":request_id" => $id, ":user_add_id" => Users::getCurrUser()));
        $days = LTrucksDaysCount::getDaysByRequsetId($id);

        $reply_ids = array();
        $to_delete = array();
        foreach ($reply as $item) {
            $explodeDateStart = explode("-", $item->date_start);
            $revert = $explodeDateStart[2]. "-" . $explodeDateStart[1] . "-" . $explodeDateStart[0];
            $reply_ids[ $revert ][ $item->l_trucks_by_user_id ] = array('status' => $item->status, 'price_confirm' => $item->price_confirm, 'price' => $item->price);
        }

		foreach ($reply as $item) {
            if(LTrucksDaysCount::model()->find('is_closed = 0 and request_id=:request_id and date_action=:date_action', array(":request_id" => $id, ":date_action"=>$item->date_start))) {
                $item->delete();
            }
        }
		
        if (count($data['id']) > 0) {
            for ($i = 0; count($data['id']) >= $i; $i++) {
                
                if ((int)$data['date_start'][$i] != 0) {
                    $m = new LTrucksByUserReply();
                    $m->plate_trailer = MYChtml::check_num(str_replace(' ', '', iconv("UTF-8", "windows-1251", $data['trailers'][$i])));
                    $m->fio_driver = trim(iconv("UTF-8", "windows-1251", $data['drivers'][$i]));
                    $m->request_id = $id;

                    $m->price = (float)$data['price'][$i];

                    $replyIds = $reply_ids[$data['date_start'][$i]][$data['id'][$i]]; // элемент в массиве существующих машин в БД

                    $isConfirmStatus = ( isset($replyIds) && $replyIds['status'] == 1 ); // автомобиль есть в списке и он подтвержден
                    

                    if ($isConfirmStatus) {
                        $m->status = 1;
                        $m->price_confirm = $replyIds['price_confirm'];
                        $m->price = $replyIds['price'];
                    } else $m->status = 0;

                    $m->ts_add = new CDbExpression("NOW()");
                    $m->user_add_id = Users::getCurrUser();
                    $ex = explode("-", $data['date_start'][$i]);
                    $m->date_start = $ex[2] . "-" . $ex[1] . "-" . $ex[0];
                    $m->l_trucks_by_user_id = $data['id'][$i];
                    if ($m->save()) {
                        if (!empty($_POST['need_record']) && in_array($m->l_trucks_by_user_id, $_POST['need_record'])) {
                            
                            $Ltruck = LTrucksByUser::model()->findByPk($m->l_trucks_by_user_id);
                            if ($Ltruck) {
                                if (strlen(trim($Ltruck->fio_driver)) == 0) $Ltruck->fio_driver = $m->fio_driver;
                                if (strlen(trim($Ltruck->plate_trailer)) == 0) $Ltruck->plate_trailer = $m->plate_trailer;
                                $Ltruck->save();
                            }
                        }
                        $success[] = $m->l_trucks_by_user_id;
                    }
                }
            }
        }
        echo json_encode($success);
    }

    public function actiongetCarrierRequest(){
        header('Content-Type: text/html; charset=windows-1251');
        $data = LTrucksByUser::getDataByUserId();
        $in_reply = LTrucksByUserReply::getDataByUserId((int)$_POST['id']);

        $reply_all_users = LTrucksByUserReply::getCountsSql((int)$_POST['id']);
        $this->renderPartial("application.views.cpanel.extend.ansfer_carrier",array('reply_all_users'=>$reply_all_users,'price'=>(float)$_POST['price'],'id'=>$_POST['id'],"data"=>$data,'in_reply'=>$in_reply));
    }
	
    public function actionpaymentWindow()
    {
        $this->checkUserId($_POST['id']);
        if($m = PaymentWindow::model()->find("`user_id`=:user_id",array(":user_id"=>$_POST['id']))) {
            if((!$m->date_next_show OR $m->date_next_show == date('Y-m-d')) AND $m->status == 0) echo 1;
            else echo 0;
        } else {
            $m = new PaymentWindow;
            $m->user_id = $_POST['id'];
            $m->date_show = date('Y-m-d');
            $m->status = 0;
            echo $m->save();
        }
    }
	
    /*                      Расстояние  между точками             */
    public function actionGetRoute()
    {
        $unload = RequestShipping::getPlaceCode( (int)$_POST["unloadCode"] );
        $load = $_POST['loadCode'];
        $data = Forwebservices::GetRoute($load,$unload);
		
        $data = json_decode($data,true);
        $result = array('Distance'=>0,'map'=>0);
		
		if(is_string($data)) { echo json_encode($result); die(); }
        if((int)$data['Distance']>0 && trim(strlen($data['Polyline']))>0) {
            $encoded = $data['Polyline'];
            $points = Polyline::decode($encoded);
            $result['map'] = Polyline::pair($points);
        }
        if($data['Distance']) $result['Distance'] = $data['Distance'];
            echo json_encode($result);
    }
		
	public  function  actionshowTraider()
    {
        $m = Stevedore::model()->findByPk($_POST['r']);
        echo (in_array($m->place_code,array("2300000600000","2303300001000")))?1:0;
    }

	/*							Статистика							*/
	public function actionStatistic()
    {
       if(in_array((int)$_POST['id'],array(1,2)))
           $url = 'statistic/nzt';
        elseif((int)$_POST['id'] == 3)  $url = 'statistic/taman';
        else $url = 'statistic/krymsk';
        $this->renderPartial($url,array('stevedore_id'=>$_POST['id']));
    }

    public function actionstatisticView()
    {
        if(in_array((int)$_POST['stevedore_id'],array(1,2)))
            $url = 'statistic/table_nzt';
        elseif((int)$_POST['stevedore_id'] == 3)  $url = 'statistic/table_taman';
        else $url = 'statistic/table_krymsk';
        $data = Forwebservices::GetStatisticsV2($_POST);
        $this->renderPartial($url,array('mode'=>$_POST['id'],'data'=>$data));
    }

    public function actionCounterOrder()
    {
        echo RequestShipping::updateViewCount(intval($_POST['id']));
    }


    public function actionGetrostovstevedore() {
        $stevedore = array();

        $stevedore_rostov = Yii::app()->db->createCommand(
            "SELECT st.* from rostov.stevedores as st LEFT join rostov.exporter_in_stevedore on rostov.exporter_in_stevedore.stevedore_id = st.id where rostov.exporter_in_stevedore.exporter_id = :id and rostov.exporter_in_stevedore.is_active = 1 ORDER by st.full_name"
        )->bindValue(':id', $_POST['id'])->queryAll();

        if ($stevedore_rostov)
            foreach ($stevedore_rostov as $stevedores) {
                $stevedore[$stevedores["id"]] = array(
                    'full_name'=>$stevedores["full_name"],
                    'payment_required' => $stevedores["payment_required"],
                    'mono'=>$stevedores["mono"],
                    'driver_phone_required'=>$stevedores["driver_phone_required"],
                    'is_using_iqueue' => $stevedores["is_using_iqueue"],
                    'is_using_iqueue_description' => $stevedores["is_using_iqueue_description"]
                );
            }
        echo CJSON::encode($stevedore);
    }

	public function actionGetrostovexporter()
    {
        $exporters = array();
        $exporter_rostov = Yii::app()->db->createCommand("SELECT * from rostov.exporters")->queryAll();
        if ($exporter_rostov)
            foreach ($exporter_rostov as $exporter) {
            $exporters[$exporter["id"]] = array(
			'name' => $exporter["name"],
			'id' => $exporter["id"]);
        }
        echo CJSON::encode($exporters);
    }

    public function actionGetrostovtrader()
    {
         $d = "SELECT * from rostov.traders where inn = '".preg_replace('/[^0-9]/', '', $_POST["inn"])."'";

        $r = Yii::app()->db->createCommand($d)->queryRow();

        if (!$r) {echo "false"; return false;}


        echo CJSON::encode($r);
    }

    public function actiongetTrader()
    {
        echo RostovTraders::getInn();
    }

    public function actiongetCultures()
    {

      /*  try {
            $stevedore_rostov = Yii::app()->db->createCommand("SELECT cultures.name, cultures.short_name, cultures.id FROM rostov.cultures LEFT JOIN culture_in_stevedore on culture_in_stevedore.culture_id = cultures.id where stevedore_id = 5")->queryAll();
        }catch (Exception $e){
            echo 'Поймано исключение: ',  $e->getMessage(), "\n";
        }finally {
            echo "Второе finally.\n";
        }*/

        $d = "SELECT rostov.cultures.name, rostov.cultures.short_name, rostov.cultures.id FROM rostov.cultures LEFT JOIN rostov.culture_in_stevedore on rostov.culture_in_stevedore.culture_id = cultures.id where stevedore_id = ".(int)$_POST["stevedore"];
        $r = Yii::app()->db->createCommand($d)->queryAll();
        echo CJSON::encode($r);
    }
}