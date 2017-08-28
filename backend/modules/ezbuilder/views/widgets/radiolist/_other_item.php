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
$other_id = appxq\sdii\utils\SDUtility::getMillisecTime();
?>
<div class="col-md-3 sdbox-col" style="position:relative;">
    <?= Html::textInput("data[builder][$id][other][attribute]", $other.$row, ['class'=>'form-control check_varname', 'id'=>"other_attribute_$id"])?>
    <?= Html::hiddenInput("data[builder][$id][other][id]", $other_id)?>
    <?= Html::hiddenInput("data[builder][$id][other][action]", 'create')?>
    <i class="fa fa-close close-other" data-var="<?=$other?>" data-row="<?=$row?>"  style="position:absolute;right: 20px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
</div>
<div class="col-md-2 sdbox-col"><?= Html::textInput("data[builder][$id][other][suffix]", '', ['class'=>'form-control', 'id'=>"other_suffix_$id"])?></div>
