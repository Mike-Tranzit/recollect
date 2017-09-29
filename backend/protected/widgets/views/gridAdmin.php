<div class="widget">
    <div class="title"><span class="titleIcon"><input type="checkbox"  name="titleCheck" id="titleCheck"/></span><h6><?=$tableName?></h6></div>
    <table cellpadding="0" cellspacing="0" border="0" class="display dTable withCheck" id="tableMainCheck">
        <thead>
        <tr>
            <th> </th>
            <th> </th>

      <? foreach ($columns as $val) {?>
            <th><?=$val["name"]?></th>
            <?}?>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>





<script>


    $(".forDelConfirm").live("click", function(e){

        var tr = $(this).parent().parent().parent().get(0);
        var name = this.id;

        if (confirm("Вы действительно хотите удалить выбранный объект?")) {
            oTable.fnDeleteRow(tr, function() {
                $.get("<?=$base_route.$routeDelOne?>", {cid:name}, function(data) {  ajaxMessage(data);});
            });
        }
    });

    $(".forStatusConfirm").live("click", function(e){

        var id = this.id.substr(7);

        if (confirm("Разослать новость?")) {

                $.get("<?=$base_route.$routeRenewal?>", {id:id}, function(data) {

                    ajaxMessage(data);

                });

        }
    });

    $(".forStatusInvConfirm").live("click", function(e){

        var name = this.id;

        if (confirm("Изменить статус счета на 'оплачен'?")) {

            $.get("<?=$base_route.$routeDelOne?>", {cid:name}, function(data) {

                setTimeout(location.reload(),'2000');
                // ajaxMessage(data);

            });

        }
    });

    $(".forStatusInvEmailConfirm").live("click", function(e){

        var name = this.id;

        if (confirm("Отправить счет повторно?")) {

            $.get("<?=$base_route.$routeEditForm;?>", {cid:name}, function(data) {
                 ajaxMessage(data);
            });

        }
    });

    $("#delAllList").live("click", function(e){
        var attr = new Array();
        if (confirm("Вы действительно хотите удалить выбранный объект?")) {
        $(".dTable input[type='checkbox']:checked").each(function() {

            tr = $(this).parent().parent().get(0);
            oTable.fnDeleteRow(tr);
            attr.push($(this).attr("value"));
                });

       $.get("<?=$base_route.$routeDelAll?>", { 'cid[]': attr } , function(data) {  ajaxMessage(data);});

        }
        return false;
    });

    $("#titleCheck").live("click", function() {
        var checkedStatus = this.checked;
        $("#tableMainCheck tbody tr td:first-child input:checkbox").each(function() {
            this.checked = checkedStatus;
            if (checkedStatus == this.checked) {
                $(this).closest('.checker > span').removeClass('checked');
            }
            if (this.checked) {
                $(this).closest('.checker > span').addClass('checked');
            }
        });
    });



    $(".forEditConfirm").live("click", function(e) {
        var id = this.id.substr(7);
        location.href="<?=$base_route.$routeEditForm?>?editid="+id+"#formadd";
    });
	
	
	$(".forViewConfirm").live("click", function(e) {
        var id = this.id.substr(7);
        location.href="<?=$base_route.$routeViewForm?>?viewid="+id+"#formadd";
    });
	
	
	 $(".forRenewalConfirm").live("click", function(e) {
        var id = this.id.substr(6);
        location.href="<?=$base_route.$routeRenewal?>?renid="+id+"#formadd";
    });


    var gaiSelected =  [];
    var init = {
        "bJQueryUI": true,
        "bAutoWidth": false,
        "bProcessing": true,
        "bStateSave": true,

        "sAjaxSource": "<?=$base_route.$routeSource?>",
        "sPaginationType": "full_numbers",

        "aoColumns": [
            { "bVisible": false },
            { "sClass":"gradeU", "bSortable": false, "bSearchable": false, "sWidth": "27px" },


            <?
            $i = 0;
            $count = sizeof($columns);
            foreach ($columns as $val) {
            $i++;
            ?>
            { "sClass":"gradeU", "bSortable": <?=($val["sorteble"]) ? "true" : "false";?>, "bSearchable": <?=($val["searcheble"]) ? "true" : "false";?> <? if ($val["width"]) {?> ,"sWidth": "<?=$val["width"]?>" <?}?> }<?
            if ($count != $i) echo ",
            ";
            }?>


        ]

    }
    oTable = $('.dTable').dataTable(init);
    var oSettings = oTable.fnSettings();
    oTable.oApi._fnLoadState(oSettings,init);
</script>