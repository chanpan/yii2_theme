<?php

use yii\helpers\Html;

/**
 * _textinput_options file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 19:02:45
 * @link http://www.appxq.com/
 * @example  
 */
$optionsTmp = isset($input['input_option'])?\appxq\sdii\utils\SDUtility::string2Array($input['input_option']):[];
$options = isset($model['ezf_field_options'])?$model['ezf_field_options']:$optionsTmp;

$placeholder = isset($options['options']['placeholder'])?$options['options']['placeholder']:Yii::t('ezform', 'Unknown');
$allowClear = isset($options['pluginOptions']['allowClear'])?$options['pluginOptions']['allowClear']:true;
?>

<div class="well" style="padding: 15px; background-color: #dff0d8; border-color: #d6e9c6; ">
    <h4 style="margin-top: 0px;"><?= Yii::t('ezform', 'Options')?></h4>

    <div class="form-group">
	<div class="row">
	    <div class="col-md-6">
		<?= Html::label(Yii::t('ezform', 'Explanation when not specified'), 'options[options][placeholder]', ['class' => 'control-label']) ?>
		<?= Html::textInput('options[options][placeholder]', $placeholder, ['class' => 'form-control']) ?>
	    </div>
	    <div class="col-md-3 sdbox-col" style="padding-top: 30px;">
		<?= Html::checkbox('options[pluginOptions][allowClear]', $allowClear, ['label' => Yii::t('ezform', 'Allow uncheck data.')]) ?>
	    </div>
	    <?= Html::hiddenInput('options[options][data-type]', 'select') ?>
	</div>
    </div>
    <div class="row">
	<div class="col-sm-12">
	    <div id="conditionBox">
		
	    </div>
	    <?= Html::hiddenInput('data[condition]', '', ['id'=>'conditionFields'])?>
            <?=  Html::hiddenInput('EzformFields[ezf_condition]', 1)?>
	</div>
    </div>
</div>

<?php 
// set var top
$this->registerJs(" 
    newLoad = true;
    renderTap('#items-editor.condition-editor .item');
");
?>