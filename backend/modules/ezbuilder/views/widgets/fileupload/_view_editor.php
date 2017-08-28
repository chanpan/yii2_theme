<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use appxq\sdii\helpers\SDNoty;
use yii\helpers\Url;

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
$template = '{label}{input}';
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

$id = appxq\sdii\utils\SDUtility::getMillisecTime();

//set input
unset($options['specific']);

$config = ArrayHelper::merge($options, ['model'=>$model,'attribute'=>'ezf_field_default']);

$pathInput = [
    '{input}'=> '<div class="input-group">
  <input type="text" class="form-control">
  <span class="input-group-btn"><button class="btn btn-primary" type="button"><i class="glyphicon glyphicon-folder-open"></i> Browse ...</button></span>
</div>',
];
$input = strtr($inputTemplate, $pathInput);

$path = [
    '{label}'=>Html::label(Yii::t('ezform', 'Question'), 'EzformFields["ezf_field_default"]', $labelOptions),
    '{input}'=>$input,
    '{hint}'=>'',
    '{error}'=>'',
];

$content = strtr($template, $path);
 
?>


<div class="form-group row">
    <div class="col-md-12">
	<?=$content?>
    </div>
</div>

