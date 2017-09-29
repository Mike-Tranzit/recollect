<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Администратор
 * Date: 11.12.12
 * Time: 19:37
 * To change this template use File | Settings | File Templates.
 */
class GridAdmin extends CWidget {

    public $columns = Array();
    public $routeDelOne ="";
    public $routeDelAll ="";
    public $routeSource = "";
    public $tableName = "";
    public $routeEditForm = "";
	public $routeRenewal = "";
	public $routeViewForm = "";


    public function run() {
        $base_route = Yii::app()->request->baseUrl."/";

        $this->render('gridAdmin', array("columns"=>$this->columns, "routeDelOne"=>$this->routeDelOne, "routeDelAll"=>$this->routeDelAll,  "routeSource"=>$this->routeSource, "tableName"=>$this->tableName,"routeEditForm"=>$this->routeEditForm,  "routeViewForm"=>$this->routeViewForm, "routeRenewal"=>$this->routeRenewal, "base_route"=>$base_route));

    }

}