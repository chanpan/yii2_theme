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
$builder = isset($data['builder'])?$data['builder']:[
    $id => ['value'=>1, 'label'=> Yii::t('ezform', 'Option').' 1', 'action'=>'create']
];

//set input
$pathInput = [
    '{input}'=> Html::activeTextInput($model, 'ezf_field_default', $options),
];
$input = strtr($inputTemplate, $pathInput);

$path = [
    '{label}'=>Html::label(Yii::t('ezform', 'Default'), 'EzformFields["ezf_field_default"]', $labelOptions),
    '{input}'=>$input,
    '{hint}'=>'',
    '{error}'=>'',
];

$content = strtr($template, $path);
 
?>
<div class="row">
    <div class="col-md-3"><h4><?=Yii::t('ezform', 'Value')?></h4></div>
    <div class="col-md-4 sdbox-col"><h4><?=Yii::t('ezform', 'Option')?></h4></div>
    <div class="col-md-2 sdbox-col"></div>
</div>

<div id="items-editor" class="condition-editor">
    <?php foreach ($builder as $id => $value):?>
    <div class="row item" data-id="<?=$id?>" data-type="radio">
	<div class="col-md-3"><?=  Html::textInput("data[builder][$id][value]", $value['value'], ['class'=>'form-control conditions-value', 'id'=>"value_$id"])?></div>
	<div class="col-md-4 sdbox-col"><?=  Html::textInput("data[builder][$id][label]", $value['label'], ['class'=>'form-control conditions-label', 'id'=>"label_$id"])?></div>
	<div class="col-md-2 sdbox-col">
	    <?=  Html::button('<i class="glyphicon glyphicon-remove" style="color: #ff0000; font-size: 20px;"></i>', ['class'=>'btn btn-link del-items-editor'])?>
	    <?= Html::hiddenInput("data[builder][$id][action]", $value['action'])?>
	</div>
    </div>
    <?php endforeach;?>
</div>

<div id="items-delete">
    
</div>

<div class="row">
    <div class="col-md-7"><?=  Html::button('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('ezform', 'Add item'), ['class'=>'btn btn-success btn-block add-conditions-ezforms', 'id'=>'add-items-editor'])?></div>
</div>

<div class="row">
    <div class="col-md-7"><hr></div>
</div>

<div class="row">
    <div class="col-md-3">
	<?=$content?>
    </div>
    <div class="col-md-4 sdbox-col">
	<?=  Html::label(Yii::t('ezform', 'Suffix'), 'options[specific][suffix]')?>
	    <?php
	    $suffix = isset($model['ezf_field_options']['specific']['suffix'])?$model['ezf_field_options']['specific']['suffix']:'';
	    ?>
	    <?=  Html::textInput('options[specific][suffix]', $suffix, ['class'=>'form-control'])?>
    </div>
</div>

<?php
$this->registerJs("
$('#items-editor').on('click', '.del-items-editor', function() {
    var id = $(this).parent().parent().attr('data-id');
    var selector = $(this).parent().parent().find('input[name=\"data[builder]['+id+'][action]\"]').val();
    
    if(selector=='update'){
	$('#items-delete').append('<input type=\"hidden\" name=\"data[delete][]\" value=\"'+id+'\">');
    } 
    $(this).parent().parent().remove();
    
    renderTap('#items-editor.condition-editor .item');
});

$('#add-items-editor').click(function() {
    var row = $('#items-editor .item').length+1;
    
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezforms2/select2/create'])."',
	data: {row:row},
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		$('#items-editor').append(result.html);	
		
		renderTap('#items-editor.condition-editor .item');
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
    
});

");
?>

