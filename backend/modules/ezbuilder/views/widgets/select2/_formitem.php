<?php
use yii\helpers\Html;

/**
 * _select2_formitem file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 8 ก.ย. 2559 15:50:07
 * @link http://www.appxq.com/
 */
$id = appxq\sdii\utils\SDUtility::getMillisecTime();
?>
<div class="row item" data-id="<?=$id?>" data-type="radio">
    <div class="col-md-3"><?=  Html::textInput("data[builder][$id][value]", $row, ['class'=>'form-control conditions-value', 'id'=>"value_$id"])?></div>
    <div class="col-md-4 sdbox-col"><?=  Html::textInput("data[builder][$id][label]", Yii::t('ezform', 'Option')." $row", ['class'=>'form-control conditions-label', 'id'=>"label_$id"])?></div>
    <div class="col-md-2 sdbox-col">
	<?=  Html::button('<i class="glyphicon glyphicon-remove" style="color: #ff0000; font-size: 20px;"></i>', ['class'=>'btn btn-link del-items-editor'])?>
	<?= Html::hiddenInput("data[builder][$id][action]", 'create')?>
    </div>
</div>