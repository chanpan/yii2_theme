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
$id_gen2 = appxq\sdii\utils\SDUtility::getMillisecTime();
$id_gen3 = appxq\sdii\utils\SDUtility::getMillisecTime();

$id_item1 = appxq\sdii\utils\SDUtility::getMillisecTime();
$id_item2 = appxq\sdii\utils\SDUtility::getMillisecTime();
$id_item3 = appxq\sdii\utils\SDUtility::getMillisecTime();

$data_row = isset($model['ezf_field_options']['options']['data-row'])?$model['ezf_field_options']['options']['data-row']:1;
$data_col = isset($model['ezf_field_options']['options']['data-col'])?$model['ezf_field_options']['options']['data-col']:3;

$builder = isset($data['builder'])?$data['builder']:[
    $id_gen => ['fields'=>[
                    '1_1' => [
                        'attribute'=>$model->ezf_field_name.'_1_1',
                        'id'=>$id_gen,
                        'label'=>Yii::t('ezform', 'Question').' 1',
                        'action'=>'create',
                        'header'=>[
                            $id_item1 => [
                                'label'=> Yii::t('ezform', 'Title').' 1',
                                'type'=>'textinput',
                                'col'=>'1',
                            ],
                            $id_item2 => [
                                'label'=>Yii::t('ezform', 'Title').' 2',
                                'type'=>'textinput',
                                'col'=>'2',
                            ],
                            $id_item3 => [
                                'label'=>Yii::t('ezform', 'Title').' 3',
                                'type'=>'textinput',
                                'col'=>'3',
                            ],
                        ]
                    ],
                    '1_2' => [
                        'attribute'=>$model->ezf_field_name.'_1_2',
                        'id'=>$id_gen2,
                        'label'=>Yii::t('ezform', 'Question').' 2',
                        'action'=>'create',
                    ],
                    '1_3' => [
                        'attribute'=>$model->ezf_field_name.'_1_3',
                        'id'=>$id_gen3,
                        'label'=>Yii::t('ezform', 'Question').' 3',
                        'action'=>'create',
                    ],
                    
                ]],
    
    ];
$idcol = 0;
//\appxq\sdii\utils\VarDumper::dump($data,0);
?>
<div class="row " style="margin-bottom: 5px;">
    <div class="col-md-3 col-md-offset-9">
        <?=  Html::button('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('ezform', 'Add column'), ['class'=>'btn btn-success btn-block', 'id'=>'add-items-col', 'data-attr'=>$model->ezf_field_name])?>
    </div>
</div>

<div id="items-editor">
    <table class="table" style="margin-bottom: 5px;background-color: #fff;">
        <?php 
        $thead = true;
        $del_col = false;
        $del_row = false;
        $row_now = 1;
        foreach ($builder as $id => $value) {
            if(is_array($value['fields'])){
                
                ?>

                <?php
                
                foreach ($value['fields'] as $xy => $obj) {
                //appxq\sdii\utils\VarDumper::dump($xy,0);
                    $row = explode('_', $xy);
                ?>
                <?php if($thead):?>
                <?php $idcol = $id; ?>
                <thead>
                    <tr>
                        <?php if(isset($obj['header']) && is_array($obj['header'])):?>
                            <?php foreach ($obj['header'] as $key_item => $value_item):?>
                            <th style="position:relative;">
                                <?= Html::textInput("data[builder][$id][fields][$xy][header][$key_item][label]", $value_item['label'], ['class'=>'form-control', 'id'=>"label_item_$key_item" , 'placeholder'=>Yii::t('ezform', 'Title')])?>
                                <?= Html::dropDownList("data[builder][$id][fields][$xy][header][$key_item][type]", $value_item['type'], ['textinput'=>'Text Input', 'textarea'=>'Textarea', 'datetime'=>'Date Time', 'checkbox'=>'Checkbox'],['class'=>'form-control ', 'id'=>"type_item_$key_item"])?>
                                <?= Html::hiddenInput("data[builder][$id][fields][$xy][header][$key_item][col]", $value_item['col'], ['class'=>'header_col'])?>
                                <?php if($del_col):?>
                                    <i class="fa fa-close del-items-col" data-id="<?=$id?>" data-row="<?=$row[0]?>" data-item-id="<?=$key_item?>" data-col="<?=$value_item['col']?>" data-var="<?=$model->ezf_field_name?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
                                <?php endif;?>
                            </th>
                            <?php $del_col=true;?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </tr>
                </thead>
                <tbody>
                    
                    <tr data-id="<?=$id?>" data-attr="<?=$model->ezf_field_name?>" data-row="<?=$row[0]?>" >
                        <?php $thead = false;?>
                <?php endif;?>
                
                <?php

                if($row[0]>$row_now){
                    echo "<tr data-id=\"$id\" data-attr=\"{$model->ezf_field_name}\" data-row=\"{$row[0]}\">";
                    $row_now=$row[0];
                }
                ?>   
                        
                        <td style="position:relative;" >
                                <?=  Html::textInput("data[builder][$id][fields][$xy][attribute]", $obj['attribute'], ['class'=>'form-control check_varname row_attr', 'placeholder'=>Yii::t('ezform', 'Variable')])?>
                                <?=  Html::textInput("data[builder][$id][fields][$xy][label]", $obj['label'], ['class'=>'form-control ', 'placeholder'=>Yii::t('ezform', 'Question')])?>
                            <?php if($row[0]>1 && $row[1]==1):?>
                                <i class="fa fa-close del-items-row" data-id="<?=$id?>" data-var="<?=$model->ezf_field_name?>" data-row="<?=$row[0]?>" data-col="<?=$row[1]?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
                            <?php endif;?>
                            <?= Html::hiddenInput("data[builder][$id][fields][$xy][id]", $obj['id'], ['class'=>'row_id'])?>
                            <?= Html::hiddenInput("data[builder][$id][fields][$xy][action]", $obj['action'], ['class'=>'row_action'])?>
                        </td>
                
                <?php
                $del_row=true;
                if($row[0]!=$row_now){
                    echo "</tr>";
                    
                }
                ?>  
                        
                <?php
                }
                ?>
                    </tr>
        <?php
            }
        }
        ?>
        </tbody> 
    </table>
</div>

<div class="row">
    <div class="col-md-3">
        <?=  Html::button('<i class="glyphicon glyphicon-plus"></i> '.Yii::t('ezform', 'Add row'), ['class'=>'btn btn-success btn-block', 'id'=>'add-items-row', 'data-attr'=>$model->ezf_field_name])?>
    </div>
</div>
<div id="items-delete">
    
</div>

<?= Html::hiddenInput('options[options][data-row]', $data_row, ['id'=>'data-row']) ?>
<?= Html::hiddenInput('options[options][data-col]', $data_col, ['id'=>'data-col']) ?>
<?= Html::hiddenInput('options[options][data-type]', 'fields') ?>

<?php
$this->registerJs("
$('#items-editor').on('click', '.del-items-row', function() {
    $(this).parent().parent().find('.row_action').each(function( index ) {
        var selector = $(this).val();
        if(selector=='update'){
            var field_attribute = $(this).parent().find('.row_attr').val();
            var field_id = $(this).parent().find('.row_id').val();
            
            $('#items-delete').append('<input type=\"hidden\" name=\"data[delete_fields]['+field_id+']\" value=\"'+field_attribute+'\">');
        } 
    });
    
    $(this).parent().parent().remove();
    
});

$('#items-editor').on('click', '.del-items-col', function() {
    
    var col = $(this).parent().find('.header_col').val();
    
    $( '#items-editor table tbody tr ').each(function( index ) {
        var row = $(this).attr('data-row');
        var id = $(this).attr('data-id');
        var selector = $(this).find('input[name=\"data[builder]['+id+'][fields]['+row+'_'+col+'][action]\"]');
        
        var field_attribute = $(selector).parent().find('.row_attr').val();
        var field_id = $(selector).parent().find('.row_id').val();
            
        if($(selector).val()=='update'){
            var field_attribute = $(selector).parent().find('.row_attr').val();
            var field_id = $(selector).parent().find('.row_id').val();
            
            $('#items-delete').append('<input type=\"hidden\" name=\"data[delete_fields]['+field_id+']\" value=\"'+field_attribute+'\">');
        } 
        
        $(selector).parent().remove();
        
    });
    
    $(this).parent().remove();
    
});


$('#add-items-col').click(function() {
    var col = parseInt($('#data-col').val())+1;
    var row = parseInt($('#data-row').val());
    var attr = $(this).attr('data-attr');
    var id = '$idcol';
    
    $('#data-col').val(col);
    
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezforms2/grid/col'])."',
	data: {row:row, col:col, attr:attr, id:id},
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		$('#items-editor table thead tr').append(result.html);	
                $('#items-editor table tbody tr').each(function( index ) {
                    var f_attr = $(this).attr('data-attr');
                    var f_id = $(this).attr('data-id');
                    var f_row = $(this).attr('data-row');
                    
                    var ele_f = this;
                    $.ajax({
                        method: 'POST',
                        url:'".Url::to(['/ezforms2/grid/fields'])."',
                        data: {'row':f_row, 'col':col, 'attr':f_attr, 'id':f_id},
                        dataType: 'JSON',
                        success: function(result2, textStatus2) {
                            if(result2.status == 'success') {
                                $(ele_f).append(result2.html);	
                            } else {
                                ". SDNoty::show('result2.message', 'result2.status') ."
                            }
                        }
                    });
                });
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
});

$('#add-items-row').click(function() {
    var row = parseInt($('#data-row').val())+1;
    var col = parseInt($('#data-col').val());
    var attr = $(this).attr('data-attr');
    $('#data-row').val(row);
    
    var header = new Array();
    $('#items-editor table thead tr th .header_col').each(function( index ) {
        header.push($(this).val());
    }); 
    
    $.ajax({
        method: 'POST',
        url:'".Url::to(['/ezforms2/grid/row'])."',
        data: {row:row, col:col, attr:attr, header:header},
        dataType: 'JSON',
        success: function(result, textStatus) {
            if(result.status == 'success') {
                $('#items-editor table tbody').append(result.html);	
            } else {
                ". SDNoty::show('result.message', 'result.status') ."
            }
        }
    });
    
});

");
?>