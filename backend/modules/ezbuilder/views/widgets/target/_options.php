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

$allowClear = isset($options['pluginOptions']['allowClear'])?$options['pluginOptions']['allowClear']:true;
$size = isset($options['modal_size'])?$options['modal_size']:'modal-xxl';

?>

<div class="well" style="padding: 15px; background-color: #dff0d8; border-color: #d6e9c6; ">
    <h4 style="margin-top: 0px;"><?= Yii::t('ezform', 'Options')?></h4>

    
    <div class="form-group">
	<div class="row">
	    <div class="col-md-3">
		<?= Html::label(Yii::t('ezform', 'Modal Size'), 'options[modal_size]', ['class' => 'control-label']) ?>
                <?= Html::dropDownList('options[modal_size]', $size, ['modal-xxl'=> Yii::t('ezform', 'Very Large'), 'modal-lg'=>Yii::t('ezform', 'Large'), ''=>Yii::t('ezform', 'Normal'), 'modal-sm'=>Yii::t('ezform', 'Small')], ['class' => 'form-control']) ?>
	    </div>
	    <div class="col-md-3" style="padding-top: 30px;">
		<?= Html::checkbox('options[pluginOptions][allowClear]', $allowClear, ['label' => Yii::t('ezform', 'Allow uncheck data.')]) ?>
	    </div>
            
            <?= Html::hiddenInput('EzformFields[ezf_target]', 1) ?>
            <?= Html::hiddenInput('options[ezf_field_id]', $model['ezf_field_id']) ?>
            <?= Html::hiddenInput('EzformFields[ezf_field_required]', 1) ?>
            <?= Html::hiddenInput('EzformFields[table_index]', 1) ?>
	    <?= Html::hiddenInput('options[options][data-type]', 'target') ?>
            <?= Html::hiddenInput('options[options][data-name-set]', 'initValueText') ?>
            <?= Html::hiddenInput('options[options][data-ezfid]', $model['ezf_id']) ?>
            <?= Html::hiddenInput('options[pluginOptions][ajax][data]', "function(params) { return {q:params.term, ezf_field_id:'{$model['ezf_field_id']}', ezf_id:'{$model['ezf_id']}'}; }") ?>
	</div>
    </div>

</div>