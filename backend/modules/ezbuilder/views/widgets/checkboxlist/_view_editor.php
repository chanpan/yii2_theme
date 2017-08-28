<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use appxq\sdii\helpers\SDNoty;

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

//set input
$id_gen = appxq\sdii\utils\SDUtility::getMillisecTime();
$data_row = isset($model['ezf_field_options']['options']['data-row'])?$model['ezf_field_options']['options']['data-row']:1;

$builder = isset($data['builder'])?$data['builder']:[
    $id_gen => ['fields'=>[
                    '1_1' => [
                        'attribute'=>$model->ezf_field_name.'_1',
                        'id'=>$id_gen,
                        'label'=>Yii::t('ezform', 'Option').' 1',
                        'action'=>'create',
                    ],
                ]],
];

?>
<div class="row">
    <div class="col-md-2">
	<?=  Html::label(Yii::t('ezform', 'Variable'))?>
    </div>
    <div class="col-md-3 sdbox-col">
	<?=  Html::label(Yii::t('ezform', 'Option'))?>
    </div>
    <div class="col-md-3 sdbox-col">
	<?=  Html::label(Yii::t('ezform', 'Variable (Specify)'))?>
    </div>
    <div class="col-md-2 sdbox-col">
	<?=  Html::label(Yii::t('ezform', 'Suffix'))?>
    </div>
    <div class="col-md-2 sdbox-col">
        
    </div>
</div>

<div id="items-editor" class="condition-editor">
    <?php 
    foreach ($builder as $id => $value) {
        if(is_array($value['fields'])){
            foreach ($value['fields'] as $xy => $obj) {
            //appxq\sdii\utils\VarDumper::dump($xy,0);
                $row = explode('_', $xy);
            ?>
            <div class="row item" data-id="<?=$id?>" data-row="<?=$row[0]?>" data-type="checkbox">
                <div class="col-md-2"><?=  Html::textInput("data[builder][$id][fields][$xy][attribute]", $obj['attribute'], ['class'=>'form-control conditions-value check_varname', 'id'=>"value_$id"])?></div>
                <div class="col-md-3 sdbox-col"><?=  Html::textInput("data[builder][$id][fields][$xy][label]", $obj['label'], ['class'=>'form-control conditions-label', 'id'=>"label_$id"])?></div>
                
                <div class="other_box">
                    <?php if (isset($value['other']['attribute'])):?>
                        <div class="col-md-3 sdbox-col" style="position:relative;">
                            <?= Html::textInput("data[builder][$id][other][attribute]", $value['other']['attribute'], ['class'=>'form-control check_varname', 'id'=>"other_attribute_$id"])?>
                            <?= Html::hiddenInput("data[builder][$id][other][id]", $value['other']['id'])?>
                            <?= Html::hiddenInput("data[builder][$id][other][action]", $value['other']['action'])?>
                            <i class="fa fa-close close-other" data-id="<?=$id?>" data-var="<?=$model->ezf_field_name.'_other_'?>" data-row="<?=$row[0]?>" style="position:absolute;right: 20px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
                        </div>
                        <div class="col-md-2 sdbox-col"><?=  Html::textInput("data[builder][$id][other][suffix]", $value['other']['suffix'], ['class'=>'form-control', 'id'=>"other_suffix_$id"])?></div>
                    <?php else:?>
                        <div class="col-md-5 sdbox-col"><a class="btn btn-default btn-block other-items-editor" data-id="<?=$id?>" data-var="<?=$model->ezf_field_name.'_other_'?>" data-row="<?=$row[0]?>"><i class="glyphicon glyphicon-plus"></i> <?= Yii::t('ezform', 'More text')?></a></div>
                    <?php endif;?>
                </div>
                <div class="col-md-2 sdbox-col">
                    <?= Html::button('<i class="glyphicon glyphicon-remove" style="color: #ff0000; font-size: 20px;"></i>', ['class'=>'btn btn-link del-items-editor'])?>
                    <?= Html::hiddenInput("data[builder][$id][fields][$xy][id]", $obj['id'])?>
                    <?= Html::hiddenInput("data[builder][$id][fields][$xy][action]", $obj['action'])?>
                </div>
            </div>
<?php
            }
        }
    }
    ?>
    
</div>


<div id="items-delete">
    
</div>

<div class="row">
    <div class="col-md-10"><?=  Html::button('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('ezform', 'Add item'), ['class'=>'btn btn-success btn-block add-conditions-ezforms', 'id'=>'add-items-editor', 'data-var'=>$model->ezf_field_name.'_other_', 'data-attr'=>$model->ezf_field_name.'_'])?></div>
</div>

<div class="row">
    <div class="col-md-10"><hr></div>
</div>
    
<div class="row">
    <div class="col-md-4">
	    <?php
	    $inline = isset($model['ezf_field_options']['inline'])?$model['ezf_field_options']['inline']:0;
	    ?>
	    <?=  Html::radioList('options[inline]', $inline, [Yii::t('ezform', 'Vertical'), Yii::t('ezform', 'Horizontal')])?>
    </div>
</div>
    
<?= Html::hiddenInput('options[options][data-row]', $data_row, ['id'=>'data-row']) ?>

<?php
$this->registerJs("
$('#items-editor').on('click', '.del-items-editor', function() {
    var id = $(this).parent().parent().attr('data-id');
    var row = $(this).parent().parent().attr('data-row');
    var selector = $(this).parent().parent().find('input[name=\"data[builder]['+id+'][fields]['+row+'_1][action]\"]').val();
    
    if(selector=='update'){
        var field_attribute = $(this).parent().parent().parent().find('input[name=\"data[builder]['+id+'][fields]['+row+'_1][attribute]\"]').val();
        
	$('#items-delete').append('<input type=\"hidden\" name=\"data[delete_fields]['+id+']\" value=\"'+field_attribute+'\">');
        
        var other_action = $(this).parent().parent().parent().find('input[name=\"data[builder]['+id+'][other][action]\"]').val();
        var other_attribute = $(this).parent().parent().parent().find('input[name=\"data[builder]['+id+'][other][attribute]\"]').val();
        var other_id = $(this).parent().parent().parent().find('input[name=\"data[builder]['+id+'][other][id]\"]').val();

        if(other_action=='update'){
            $('#items-delete').append('<input type=\"hidden\" name=\"data[delete_fields]['+other_id+']\" value=\"'+other_attribute+'\">');
        } 
    } 
    $(this).parent().parent().remove();
    
    renderTap('#items-editor.condition-editor .item');
});


$('#items-editor').on('click', '.close-other', function() {
    var id = $(this).parent().parent().parent().attr('data-id');
    var other_action = $(this).parent().parent().parent().find('input[name=\"data[builder]['+id+'][other][action]\"]').val();
    var other_attribute = $(this).parent().parent().parent().find('input[name=\"data[builder]['+id+'][other][attribute]\"]').val();
    var other_id = $(this).parent().parent().parent().find('input[name=\"data[builder]['+id+'][other][id]\"]').val();
    
    if(other_action=='update'){
	$('#items-delete').append('<input type=\"hidden\" name=\"data[delete_fields]['+other_id+']\" value=\"'+other_attribute+'\">');
    } 
    
    var other = $(this).attr('data-var');
    var row = $(this).attr('data-row');
    var div_box_view = $(this).parent().parent();
    div_box_view.html('<div class=\"col-md-5 sdbox-col\"><a class=\"btn btn-default btn-block other-items-editor\" data-var=\"'+other+'\" data-row=\"'+row+'\"><i class=\"glyphicon glyphicon-plus\"></i> ".Yii::t('ezform', 'More text')."</a></div>');
    
});

$('#items-editor').on('click', '.other-items-editor', function() {
    var div_box_view = $(this).parent().parent();
    var other = $(this).attr('data-var');
    var row = $(this).attr('data-row');
    var id = $(this).attr('data-id');
    
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezforms2/checkbox-list/other'])."',
	data: {row:row, other:other, id:id},
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		div_box_view.html(result.html);
		
		renderTap('#items-editor.condition-editor .item');
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
});

$('#add-items-editor').click(function() {
    var row = parseInt($('#data-row').val())+1;
    var other = $(this).attr('data-var');
    var attr = $(this).attr('data-attr');
    $('#data-row').val(row);
    
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezforms2/checkbox-list/create'])."',
	data: {row:row, other:other, attr:attr},
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