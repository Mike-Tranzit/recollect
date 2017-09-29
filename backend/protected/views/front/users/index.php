<? $this->widget('application.widgets.GridAdmin',
    array(
         "columns" => array(
             Array("name"=>"Имя", "width"=>false, "sorteble"=>true, "searcheble" => true),
             Array("name"=>"E-mail", "width"=>false, "sorteble"=>true, "searcheble" => true),
             Array("name"=>"Дата создания", "width"=>false, "sorteble"=>true, "searcheble" => true),
             Array("name"=>"Действия", "width"=>'50px', "sorteble"=>true, "searcheble" => true),
         ),
         "routeDelOne" =>   "/users/AjaxDelOne/",
         "routeDelAll" =>   "/users/AjaxDelAll/",
         "routeSource" =>   "/users/AjaxGet/",
         "routeEditForm"=>  "/users/edit/",
         "tableName" =>   "Пользователи"
    )
); ?>

<?=$this->renderPartial("_form");?>