<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

/**
 * _select2_view_item file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 19:02:45
 * @link http://www.appxq.com/
 * @example  http://www.yiiframework.com/doc-2.0/yii-bootstrap-activefield.html
 * $checkboxTemplate		    the template for checkboxes in default layout
    $radioTemplate		    the template for radio buttons in default layout
    $horizontalCheckboxTemplate	    the template for checkboxes in horizontal layout
    $horizontalRadioTemplate	    the template for radio buttons in horizontal layout
    $inlineCheckboxListTemplate	    the template for inline checkboxLists
    $inlineRadioListTemplate	    the template for inline radioLists
 * 
 * $template = '{label}<div class="input-group"><span class="input-group-addon">@</span>{input}</div>';
 * 
 * [
    'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
]
 * [
    'template' => '{label} <div class="row"><div class="col-sm-4">{input}{error}{hint}</div></div>'
]
 */

//set options
$template = '{input}{hint}';
if(isset($specific['template']) && !empty($specific['template'])){
    $template = $specific['template'];
}
$inputTemplate = '{input}';
if(isset($specific['inputTemplate']) && !empty($specific['inputTemplate'])){
    $inputTemplate = $specific['inputTemplate'];
}
$labelOptions = ['class'=>'control-label'];
if(isset($specific['labelOptions']) && !empty($specific['labelOptions'])){
    $labelOptions = ArrayHelper::merge($labelOptions, $specific['labelOptions']);
}

//set input
//." <code>{$field['ezf_field_name']}</code>"

unset($options['specific']);
if(Yii::$app->session['show_varname']===1){
    if(isset($options['label'])){
        $options['label'] .= " <code>{$field['ezf_field_name']}</code>";
    } else {
        $options['label'] = " <code>{$field['ezf_field_name']}</code>";
    }
}

$pathInput = [
    '{input}'=> \backend\modules\ezforms2\classes\EzformWidget::checkbox($field['ezf_field_name'], $field['ezf_field_default'], $options),
];
$input = strtr($inputTemplate, $pathInput);

$path = [
    //'{label}'=>Html::label($field['ezf_field_label'], $field['ezf_field_name'], $labelOptions),
    '{input}'=>$input,
    '{hint}'=>'<p class="help-block">'.$field['ezf_field_hint'].'</p>',
    '{error}'=>'',
];

$content = strtr($template, $path);
 
?>

<div class="form-group">
    <?=$content?>
</div>