<?php
/**
 * Created by JetBrains PhpStorm.
 * User: PC
 * Date: 27.11.12
 * Time: 3:57
 * To change this template use File | Settings | File Templates.
 */
class MYChtml extends  CHtml
{



    public static  function errorSummary($model) {

        $content='';
        if(!is_array($model))
            $model=array($model);

            $firstError=true;
        foreach($model as $m)
        {
            foreach($m->getErrors() as $errors)
            {
                foreach($errors as $error)
                {
                    if($error!='')
                        $content.="$error";
                    if($firstError)
                        break;
                }
            }
        }
        if($content!=='')
        {

            if(!isset($htmlOptions['class']))

            return   '<script type="text/javascript">$.jGrowl("'.$content.'");</script>';

        }
        else
            return '';

    }

    public static  function showNotice($content) {

        return   '<script type="text/javascript">$.jGrowl("'.$content.'");</script>';

    }


    public static  function showMessage($content) {

        if (strlen($content)>0) return '<div class="nNote nInformation hideit">
            <p><strong>»Õ‘Œ–Ã¿÷»ﬂ: </strong>'.$content.'</p>
        </div>';

    }

    public static  function showError($content) {

        if (strlen($content)>0) return '<div class="nNote nFailure hideit">
            <p><strong>»Õ‘Œ–Ã¿÷»ﬂ: </strong>'.$content.'</p>
        </div>';

    }

    public static function filterJSON ($val) {
        return  trim(str_replace(array("\""),"'",$val));
    }

    public static function getImage($folder,$name){
        $a = @scandir( Yii::getPathOfAlias('webroot')."/uploads/".$folder );
        foreach($a as $item){
            if($name == substr($item,0,strpos($item,"."))) return $folder."/".$item;
        }
    }

    public static function view_num($arg){

        $arg=str_replace ('A','‡',$arg);
        $arg=str_replace ('B','‚',$arg);
        $arg=str_replace ('E','Â',$arg);
        $arg=str_replace ('K','Í',$arg);
        $arg=str_replace ('M','Ï',$arg);
        $arg=str_replace ('H','Ì',$arg);
        $arg=str_replace ('O','Ó',$arg);
        $arg=str_replace ('P','',$arg);
        $arg=str_replace ('C','Ò',$arg);
        $arg=str_replace ('T','Ú',$arg);
        $arg=str_replace ('Y','Û',$arg);
        $arg=str_replace ('X','ı',$arg);
        return $arg;
    }

    public static function check_num($arg){

        $arg=strtoupper($arg);
        $arg=str_replace (' ','',$arg);
        $arg=str_replace ('¿','A',$arg);
        $arg=str_replace ('¬','B',$arg);
        $arg=str_replace ('≈','E',$arg);
        $arg=str_replace (' ','K',$arg);
        $arg=str_replace ('Ã','M',$arg);
        $arg=str_replace ('Õ','H',$arg);
        $arg=str_replace ('Œ','O',$arg);
        $arg=str_replace ('–','P',$arg);
        $arg=str_replace ('—','C',$arg);
        $arg=str_replace ('“','T',$arg);
        $arg=str_replace ('”','Y',$arg);
        $arg=str_replace ('’','X',$arg);

        $arg=str_replace ('‡','A',$arg);
        $arg=str_replace ('‚','B',$arg);
        $arg=str_replace ('Â','E',$arg);
        $arg=str_replace ('Í','K',$arg);
        $arg=str_replace ('Ï','M',$arg);
        $arg=str_replace ('Ì','H',$arg);
        $arg=str_replace ('Ó','O',$arg);
        $arg=str_replace ('','P',$arg);
        $arg=str_replace ('Ò','C',$arg);
        $arg=str_replace ('Ú','T',$arg);
        $arg=str_replace ('Û','Y',$arg);
        $arg=str_replace ('ı','X',$arg);

        return $arg;
    }
}
