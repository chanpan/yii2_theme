<?php
use yii\helpers\Html;

$key_item = appxq\sdii\utils\SDUtility::getMillisecTime();

?>
<th style="position:relative;">
    <?=  Html::textInput("data[builder][$id][fields][1_1][data][$key_item][value]", $col, ['class'=>'form-control', 'id'=>"value_item_$key_item" , 'placeholder'=>Yii::t('ezform', 'Value')])?>
    <?=  Html::textInput("data[builder][$id][fields][1_1][data][$key_item][label]", Yii::t('ezform', 'levels').' '.$col, ['class'=>'form-control ', 'id'=>"label_item_$key_item", 'placeholder'=> Yii::t('ezform', 'Levels')])?>
    <i class="fa fa-close del-items-lvl" data-id="<?=$id?>" data-item-id="<?=$key_item?>" data-col="<?=$col?>" data-var="<?=$attr?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
    <?= Html::hiddenInput("data[builder][$id][fields][1_1][data][$key_item][action]", 'create')?>
</th>