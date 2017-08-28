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

$maskTemp = isset($optionsTmp['mask'])?$optionsTmp['mask']:'aaa-999-***';
$rmosTemp = isset($optionsTmp['clientOptions']['removeMaskOnSubmit'])?$optionsTmp['clientOptions']['removeMaskOnSubmit']:0;

$mask = isset($options['mask'])?$options['mask']:$maskTemp;
$rmos = isset($options['clientOptions']['removeMaskOnSubmit'])?$options['clientOptions']['removeMaskOnSubmit']:$rmosTemp;
?>

<div class="well" style="padding: 15px; background-color: #dff0d8; border-color: #d6e9c6; ">
    <h4 style="margin-top: 0px;"><?= Yii::t('ezform', 'Options')?></h4>
    <div class="form-group">
	<div class="row">
	    <div class="col-md-3 ">
		<?= Html::label(Yii::t('ezform', 'Mask'), 'options[mask]', ['class' => 'control-label']) ?>
		<?= Html::textInput('options[mask]', $mask, ['class' => 'form-control']) ?>
	    </div>
	    <div class="col-md-3 sdbox-col">
		<?= Html::label('', 'options[clientOptions][removeMaskOnSubmit]', ['class' => 'control-label']) ?>
		<?= Html::checkbox('options[clientOptions][removeMaskOnSubmit]', $rmos, ['label'=>Yii::t('ezform', 'Remove Mask On Submit')]) ?>
	    </div>
	    <div class="col-md-3 sdbox-col">

	    </div>
	    <div class="col-md-3 sdbox-col">

	    </div>
	</div>
    </div>
    <?= Html::hiddenInput('options[options][data-type]', 'special') ?>
    <?= Html::hiddenInput('EzformFields[table_index]', 1) ?>
    <?= Html::hiddenInput('EzformFields[ezf_special]', 1) ?>
    <?= Html::hiddenInput('EzformFields[ezf_field_required]', 1) ?>
    <?= Html::hiddenInput('options[options][data-ezfid]', $model['ezf_id']) ?>
    <?= Html::hiddenInput('options[options][data-ezf_field_id]', $model['ezf_field_id']) ?>
    <?= Html::hiddenInput('options[options][class]', 'form-control') ?>
</div>