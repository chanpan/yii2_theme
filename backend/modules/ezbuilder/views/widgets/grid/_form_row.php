<?php
use yii\helpers\Html;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<tr data-id="<?=$id?>" data-attr="<?=$attr?>" data-row="<?=$row?>">
    <?php
    if(!empty($header)){
        foreach ($header as $key => $col) {
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
            <?php
        }
    }
    ?>
</tr>
