<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Администратор
 * Date: 10.12.12
 * Time: 18:15
 * To change this template use File | Settings | File Templates.
 */
class extendAythManager
{
    /**
     * Проверка и если надо добавление/удаление операций в RBAC
     * Ставим в соответствии с меню
     */

    public $menu;
    public $operation;
    public $roles;
    public $rolesStruct;

    public function __construct() {

        $this->menu = CPMenu::model()->findAll("level=2");
        $rolse = Yii::app()->authManager->getRoles();
        $this->rolse = $this->createArrayNameRoles($rolse);
    }

    public function  getRoleDataByName($arg) {

       return Yii::app()->authManager->getAuthItem($arg);

    }

    public function delAllChild($arg) {

        foreach ($arg->children as $val) {
            Yii::app()->authManager->removeItemChild($arg->name, $val->name);
        }

        return true;

    }

    public function checkAllOperationAsMenu()
    {
         $opers =  Yii::app()->authManager->getOperations();
         $array_oper = Array();
        foreach ($opers as $val) {
            array_push($array_oper,$val->name);
        }
        $this->operations  = $array_oper;
        foreach ($this->menu  as $val) {

            if (!in_array($val->route, $array_oper)) {

              Yii::app()->authManager->createOperation($val->route,$val->name );
            }

        }
    }

    public function getArrayOperationForRole($arg) {

        $role = Yii::app()->authManager->getAuthItem($arg);
        $arr = Array();

        if (is_array($role->children))
        foreach ($role->children as $val) {
            array_push($arr,$val->name);
        }
        return $arr;
    }

    public function  getArrayRoles() {

        return $this->rolesStruct;

    }

    private function  createArrayNameOperation($arg) {

        $arr = Array();

        foreach ($this->menu as $val) {
            array_push($arr,$val->route);

        }
        return $arr;
    }



    private function  createArrayNameRoles($arg) {

        $arr = Array();
        $this->rolesStruct = Array();
        foreach ($arg as $val) {

            array_push($arr,$val->name);
            array_push($this->rolesStruct, array($val->name, str_replace('"',"&quote;",$val->description)));
        }
        return $arr;

    }


    public function  checkOperationName($arg){


        return in_array($arg, $this->operations);

    }

    public function delAllRole($arg) {
        $err = false;
        foreach($arg as $val) {
        $err = $this->delOneRole($val);
        }
        return $err;

    }




    public function delOneRole($arg) {

        if (!$this->checkRolesName($arg)) return false;
        return Yii::app()->authManager->removeAuthItem($arg);

    }

    public function  checkRolesName($arg){
      return in_array($arg, $this->rolse);

    }






}
