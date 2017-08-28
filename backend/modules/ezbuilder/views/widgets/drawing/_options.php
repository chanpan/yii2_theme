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

$width = isset($options['options']['width'])?$options['options']['width']:600;
$height = isset($options['options']['height'])?$options['options']['height']:480;
?>

<div class="well" style="padding: 15px; background-color: #dff0d8; border-color: #d6e9c6; ">
    <h4 style="margin-top: 0px;"><?= Yii::t('ezform', 'Options')?></h4>
    <div class="row">
        <div class="col-md-3">
            <?= Html::label(Yii::t('ezform', 'Width'))?>
            <?= Html::textInput('options[options][width]', $width, ['class'=>'form-control', 'type'=>'number']) ?>
        </div>
        <div class="col-md-3 sdbox-col">
            <?= Html::label(Yii::t('ezform', 'Height'))?>
            <?= Html::textInput('options[options][height]', $height, ['class'=>'form-control', 'type'=>'number']) ?>
        </div>
    </div>

    <?= Html::hiddenInput('options[options][data-type]', 'file') ?>
    <?= Html::hiddenInput('options[options][data-name]', 'default_bg') ?>
    <?= Html::hiddenInput('options[options][data-from]', '@storage/ezform/drawing/') ?>
    <?= Html::hiddenInput('options[options][data-to]', '@storage/ezform/drawing/bg/') ?>
</div>