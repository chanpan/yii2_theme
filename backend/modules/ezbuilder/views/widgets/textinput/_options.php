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

$maxlength = isset($options['maxlength'])?$options['maxlength']:'';
$min = isset($options['min'])?$options['min']:0;
$max = isset($options['max'])?$options['max']:'';

?>

<div class="well" style="padding: 15px; background-color: #dff0d8; border-color: #d6e9c6; ">
    <h4 style="margin-top: 0px;"><?= Yii::t('ezform', 'Options')?></h4>

    
    <div class="form-group">
        <?= Html::label(Yii::t('ezform', 'Calculator'))?>
        <?=Html::activeTextInput($model, 'ezf_field_cal', ['class'=>'form-control', 'placeholder'=>Yii::t('ezform', 'Example: {Variable} + {Variable} - {Variable} * {Variable} / {Variable}')])?>
    </div>
    
    <div class="form-group">
	<div class="row">
	    <?php if(isset($options['type']) && $options['type']=='number'): ?>
	    <div class="col-md-3 ">
		<?= Html::label(Yii::t('ezform', 'Min'), 'options[min]', ['class' => 'control-label']) ?>
		<?= Html::textInput('options[min]', $min, ['class' => 'form-control', 'type' => 'number']) ?>
	    </div>
	    <div class="col-md-3">
		<?= Html::label(Yii::t('ezform', 'Max'), 'options[max]', ['class' => 'control-label']) ?>
		<?= Html::textInput('options[max]', $max, ['class' => 'form-control', 'type' => 'number']) ?>
	    </div>
	    <?php else: ?>
	    <div class="col-md-3">
		<?= Html::label(Yii::t('ezform', 'Max Length'), 'options[maxlength]', ['class' => 'control-label']) ?>
		<?= Html::textInput('options[maxlength]', $maxlength, ['class' => 'form-control', 'type' => 'number']) ?>
	    </div>
	    <?php endif;?>
	</div>
    </div>

</div>