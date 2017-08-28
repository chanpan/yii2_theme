<?php
use yii\helpers\Html;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$id = appxq\sdii\utils\SDUtility::getMillisecTime();
?>
<tr data-id="<?=$id?>">
    <td style="position:relative;" class="form-inline">
        <div class="form-group">
            <?=  Html::textInput("data[builder][$id][fields][{$row}_1][label]", Yii::t('ezform', 'Question').' '.$row, ['class'=>'form-control ', 'id'=>"label_$id", 'style'=>'width: 240px', 'placeholder'=>Yii::t('ezform', 'Question')])?>
        </div>
        <div class="form-group">
            <?=  Html::textInput("data[builder][$id][fields][{$row}_1][attribute]", $attr.'_'.$row, ['class'=>'form-control check_varname', 'id'=>"value_$id" , 'style'=>'width: 90px', 'placeholder'=>Yii::t('ezform', 'Variable')])?>
        </div>
        
        <i class="fa fa-close del-items-field" data-id="<?=$id?>" data-var="<?=$attr?>" data-row="<?=$row?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
        
        <?= Html::hiddenInput("data[builder][$id][fields][{$row}_1][id]", $id)?>
        <?= Html::hiddenInput("data[builder][$id][fields][{$row}_1][action]", 'create')?>
        <?= Html::hiddenInput("data[builder][$id][fields][{$row}_1][type]", 'id')?>
    </td>

    <?php for($i=1;$i<=$col;$i++):?>
    <td style="text-align: center"><?= Html::radio('builder_'.$id)?></td>
    <?php endfor;?>
</tr>
