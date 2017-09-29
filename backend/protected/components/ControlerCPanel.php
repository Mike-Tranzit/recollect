<?php
/**
 * Created by JetBrains PhpStorm.
 * User: PC
 * Date: 27.11.12
 * Time: 1:30
 * To change this template use File | Settings | File Templates.
 */
class ControlerCPanel extends CController
{

    public $nameControlerForView = "Основные категории";
    public $descriptionActionControlerForView = "";
    public $name;
    public $edit;
    public $model;
    public $save = 'ok';
    public $return = "//ajaxOK";
    public $returnArray = array();
    public $layout='//layouts/column1';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs=array();

    public function filters()
    {
        return array(
            'checkAccess'
        );
    }

    public function uploadFile($name,$object){		//Загрузка файлов

        $info_dir = getcwd().'/uploads/'.$name.'/';

        if(!is_dir($info_dir)) mkdir($info_dir,0770);

        if(!$object->saveAs($info_dir.strtolower($object))) $this->save = 'error';
    }
	
	public function message(){

        if(isset($_GET['save'])) {
            switch ($_GET['save']){
                case 'ok': return MYChtml::showMessage('Сохранено успешно'); break;
                case 'error': return MYChtml::showError('Ошибка сохранения'); break;
            }
            unset($_GET['save']);
        }
    }

    public  function  filterCheckAccess($filterChain) {


     /*   if (Yii::app()->user->isGuest) {
            $this->redirect($this->createUrl("/login"));
        }*/

        $filterChain->run();
        return true;
    }

    public function MailerTo($item,$model){
        $__smtp=Yii::app()->params['smtp'];

        Yii::app()->mailer->Host = $__smtp['host'];
        Yii::app()->mailer->Port = $__smtp['port'];
        Yii::app()->mailer->IsSMTP();
        Yii::app()->mailer->SMTPAuth = $__smtp['auth'];
        Yii::app()->mailer->Username = $__smtp['username'];
        Yii::app()->mailer->Password = $__smtp['password'];
        Yii::app()->mailer->SMTPDebug = $__smtp['debug'];
        Yii::app()->mailer->From = $__smtp['from'];
        Yii::app()->mailer->FromName = iconv("windows-1251","UTF-8",$__smtp['fromname']);

        Yii::app()->mailer->AddAddress($item->email); //bablgum@mail.ru
        Yii::app()->mailer->Subject = iconv("windows-1251","UTF-8",$model->title);
        Yii::app()->mailer->MsgHTML(iconv("windows-1251","UTF-8",$this->renderPartial('//clearRender', array('model'=>$model,'item'=>$item),true)));
        Yii::app()->mailer->CharSet = "UTF-8";
        if(Yii::app()->mailer->send())	return true;
        else return false;
    }



}
