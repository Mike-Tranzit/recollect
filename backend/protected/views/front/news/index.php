<? $this->widget('application.widgets.GridAdmin',
    array(
         "columns" => array(
             Array("name"=>"��������", "width"=>false, "sorteble"=>true, "searcheble" => true),
             Array("name"=>"���� ��������", "width"=>'250px', "sorteble"=>true, "searcheble" => true),
             Array("name"=>"��������", "width"=>'100px', "sorteble"=>true, "searcheble" => true),
         ),
         "routeDelOne" =>   "/news/AjaxDelOne/",
         "routeDelAll" =>   "/news/AjaxDelAll/",
         "routeSource" =>   "/news/AjaxGet/",
         "routeEditForm"=>  "/news/edit/",
         "routeRenewal"=>   "/news/subscription/",
         "tableName" =>    "�������"
    )
); ?>

<?=$this->renderPartial("_form");?>