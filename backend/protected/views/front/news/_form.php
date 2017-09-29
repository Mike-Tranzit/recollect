<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/tinymce/tinymce.min.js"></script>
<?$action = ($this->edit)? 'EditRecord'  : 'add' ;?>
<div class="widget">
    <a name='formadd'></a>
    <form action="<?=$this->createUrl($action,array("edit"=>$this->edit));?>" class="form" method='post' id='addForm' >
        <fieldset>
            <div class="title"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/dark/record.png" alt="" class="titleIcon" /><h6><?=$this->name;?></h6></div>

            <div class="formRow">
                <label>Название:</label>
                <div class="formRight">
                    <input type="text" value="<?=$this->model->title;?>" name='title' style="width:260px;"/><span class="formNote"></span>
                </div>
                <div class="clear"></div>
            </div>

            <div class="formRow">
                <label>Текст:</label>
                <div class="formRight">
                    <textarea name='text'/><?=$this->model->text;?></textarea><span class="formNote"></span>
                </div>
                <div class="clear"></div>
            </div>

            <div class="formRow">
                <label></label>
                <div class="formRight">
                    <a  onClick='$("#addForm").submit()' title="" class="button greyishB"  style="margin: 5px;"><img src="<?php echo Yii::app()->request->baseUrl; ?>/images/icons/light/create.png" alt="" class="icon" /><span id='buttonName'><?=(!$this->edit?"Сохранить":"Сохранить изменения")?></span></a>
                </div>

                <div class="clear"></div>
            </div>

        </fieldset>
    </form>
</div>

<script>

    $(document).ready(function(){
    var validator =  $("#addForm").validate({
        rules: {
            'title': {
                required : true
            },
            'text': {
                required : true
            }
        }
    });

        tinymce.init({selector:'textarea',menubar: false,language:'ru',plugins: ["textcolor colorpicker"],toolbar: "insertfile undo redo | bold italic underline| forecolor backcolor",forced_root_block : false,
            force_br_newlines : true,
            force_p_newlines : false});

    });
</script>