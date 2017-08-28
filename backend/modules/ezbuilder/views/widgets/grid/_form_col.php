<?php
use yii\helpers\Html;

$key_item = appxq\sdii\utils\SDUtility::getMillisecTime();

?>
<th style="position:relative;">
    <?=  Html::textInput("data[builder][$id][fields][1_1][header][$key_item][label]", Yii::t('ezform', 'Title').' '.$col, ['class'=>'form-control', 'id'=>"label_item_$key_item" , 'placeholder'=>Yii::t('ezform', 'Title')])?>
    <?=  Html::dropDownList("data[builder][$id][fields][1_1][header][$key_item][type]", 'textinput', ['textinput'=>'Text Input', 'textarea'=>'Textarea', 'datetime'=>'Date Time', 'checkbox'=>'Checkbox'],['class'=>'form-control ', 'id'=>"type_item_$key_item"])?>
    <?= Html::hiddenInput("data[builder][$id][fields][1_1][header][$key_item][col]", $col, ['class'=>'header_col'])?>
    <i class="fa fa-close del-items-col" data-id="<?=$id?>" data-item-id="<?=$key_item?>" data-col="<?=$col?>" data-var="<?=$attr?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
</th>