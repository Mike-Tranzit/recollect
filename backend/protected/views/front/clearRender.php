
<div style="margin-left: 20px; width: 600px;">
    <br>
    <div style="padding: 10px; border-radius: 2px; margin-bottom: 5px;">
        <p style="font-size: 16px; font-weight: 600;">Новое сообщение от <?=$model->name;?></p>
        <p style="line-height: 2em;"><?=$model->text;?></p>
        <br>
        <p>Контакты адресата: <?=$model->contacts;?></p>
        <p><b><?=MYDate::showDate(date('Y-m-d H:i'));?></b></p>
    </div>
</div>
