<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/front';
    public $save = 'ok';
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

    public function formSanctionData($id) {
        $sanction = Sanctions::getSanctionStatus($id);
        if ($sanction) {
            $status_sanction = 0;
            $date_sanction = MYDate::showComments($sanction->date_create);
        } else {
            $status_sanction = 1;
            $date_sanction = 'null';
        }
        return array('status_sanction'=>$status_sanction,'date_sanction'=>$date_sanction);
    }

}