<? $this->widget('application.widgets.GridAdmin',
    array(
         "columns" => array(
             Array("name"=>"Название", "width"=>false, "sorteble"=>true, "searcheble" => true),
             Array("name"=>"Дата создания", "width"=>'250px', "sorteble"=>true, "searcheble" => true),
             Array("name"=>"Действия", "width"=>'100px', "sorteble"=>true, "searcheble" => true),
         ),
         "routeDelOne" =>   "/news/AjaxDelOne/",
         "routeDelAll" =>   "/news/AjaxDelAll/",
         "routeSource" =>   "/news/AjaxGet/",
         "routeEditForm"=>  "/news/edit/",
         "routeRenewal"=>   "/news/subscription/",
         "tableName" =>    "Новости"
    )
); ?>

<?=$this->renderPartial("_form");?>