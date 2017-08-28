<?php
use yii\helpers\Html;

$gen_id = appxq\sdii\utils\SDUtility::getMillisecTime();
$xy = $row.'_'.$col;
?>

<td style="position:relative;">
    <?=  Html::textInput("data[builder][$id][fields][$xy][attribute]", $attr.'_'.$xy, ['class'=>'form-control check_varname row_attr', 'placeholder'=>Yii::t('ezform', 'Variable')])?>
    <?=  Html::textInput("data[builder][$id][fields][$xy][label]", Yii::t('ezform', 'Question').' '.$col, ['class'=>'form-control ', 'placeholder'=>Yii::t('ezform', 'Question')])?>
    <?php if($col==1):?>
    <i class="fa fa-close del-items-row" data-id="<?=$id?>" data-var="<?=$attr?>" data-row="<?=$row?>" data-col="<?=$col?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
    <?php endif;?>
    <?= Html::hiddenInput("data[builder][$id][fields][$xy][id]", $gen_id, ['class'=>'row_id'])?>
    <?= Html::hiddenInput("data[builder][$id][fields][$xy][action]", 'create', ['class'=>'row_action'])?>
</td>