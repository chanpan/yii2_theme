<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * _textinput_view_editor file UTF-8
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
$template = '{label}{input}{hint}';
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
//appxq\sdii\utils\VarDumper::dump($options);
//set input
$enable_tumbon = isset($options['enable_tumbon'])?$options['enable_tumbon']:0;

$data_fields;
$label_addon = '';
if(Yii::$app->session['show_varname']===1){
    if(isset($data['fields']) && !empty($data['fields'])){
        foreach ($data['fields'] as $key => $value) {
            $data_fields[$value['label']] = $value['attribute'];
        }
        $label_addon = " <code>{$data_fields['province']}</code>, <code>{$data_fields['amphur']}</code>, <code>{$data_fields['tumbon']}</code>";
    }
}

$tumbon = $enable_tumbon?'<div class="col-md-4 sdbox-col">'.Html::dropDownList('name3', null, [], ['class'=>'form-control', 'prompt'=>'ตำบล']).'</div>':'';

$input = '<div class="row">
            <div class="col-md-4 ">
                '.Html::dropDownList('name', null, [], ['class'=>'form-control', 'prompt'=>'จังหวัด']).'
            </div>
            <div class="col-md-4 sdbox-col">
                '.Html::dropDownList('name2', null, [], ['class'=>'form-control', 'prompt'=>'อำเภอ']).'
            </div>
            '.$tumbon.'
          </div>    
        ';

$path = [
    '{label}'=>Html::label($field['ezf_field_label'].$label_addon, $field['ezf_field_name'], $labelOptions),
    '{input}'=>$input,
    '{hint}'=>'<p class="help-block">'.$field['ezf_field_hint'].'</p>',
    '{error}'=>'',
];

$content = strtr($template, $path);

?>

<div class="form-group">
    <?=$content?>
</div>