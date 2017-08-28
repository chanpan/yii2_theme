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
$template = '{input}';
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
$ezf_field_options = is_array($model['ezf_field_options'])?$model['ezf_field_options']:[];
$options = ArrayHelper::merge($options, $ezf_field_options);

if(!isset($options['label'])){
    $options['label']=$model['ezf_field_label'];
}

$pathInput = [
    '{input}'=> \backend\modules\ezforms2\classes\EzformWidget::activeCheckbox($model, 'ezf_field_default', $options),
];
$input = strtr($inputTemplate, $pathInput);

$path = [
    //'{label}'=>Html::label('คำถาม', 'EzformFields["ezf_field_default"]', $labelOptions),
    '{input}'=>$input,
    '{hint}'=>'',
    '{error}'=>'',
];

$content = strtr($template, $path);
?>
<div class="form-group row">
    <div class="col-md-9">
	<?=$content?>
    </div>
</div>

<div id="items-editor" class="condition-editor">
    <div class="row item" data-type="checkbox">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Label'))?>
            <?= Html::textInput("options[label]", $options['label'], ['class'=>'form-control conditions-label', 'id'=>'label_'.$model['ezf_field_name']])?>
        </div>
        <?=  Html::hiddenInput("hideValue", $model['ezf_field_name'], ['class'=>'conditions-value'])?>
    </div>
</div>

<?php
$this->registerJs("
$('#box-data').on('change', '.label_".$model['ezf_field_name']."', function() {
    renderTap('#items-editor.condition-editor .item');
});
");
?>

