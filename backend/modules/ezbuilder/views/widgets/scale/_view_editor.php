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
$id_item1 = appxq\sdii\utils\SDUtility::getMillisecTime();
$id_item2 = appxq\sdii\utils\SDUtility::getMillisecTime();
$id_item3 = appxq\sdii\utils\SDUtility::getMillisecTime();
$id_item4 = appxq\sdii\utils\SDUtility::getMillisecTime();
$id_item5 = appxq\sdii\utils\SDUtility::getMillisecTime();

$data_row = isset($model['ezf_field_options']['options']['data-row'])?$model['ezf_field_options']['options']['data-row']:1;
$data_col = isset($model['ezf_field_options']['options']['data-col'])?$model['ezf_field_options']['options']['data-col']:5;

$builder = isset($data['builder'])?$data['builder']:[
    $id_gen => ['fields'=>[
                    '1_1' => [
                        'attribute'=>$model->ezf_field_name.'_1',
                        'id'=>$id_gen,
                        'label'=>Yii::t('ezform', 'Question').' 1',
                        'action'=>'create',
                        'type'=>'id',
                        'data'=>[
                            $id_item1 => [
                                'value'=>'5',
                                'label'=>Yii::t('ezform', 'Very good'),
                                'action'=>'create'
                            ],
                            $id_item2 => [
                                'value'=>'4',
                                'label'=>Yii::t('ezform', 'Good'),
                                'action'=>'create'
                            ],
                            $id_item3 => [
                                'value'=>'3',
                                'label'=>Yii::t('ezform', 'Fair'),
                                'action'=>'create'
                            ],
                            $id_item4 => [
                                'value'=>'2',
                                'label'=>Yii::t('ezform', 'Badly'),
                                'action'=>'create'
                            ],
                            $id_item5 => [
                                'value'=>'1',
                                'label'=>Yii::t('ezform', 'Very bad'),
                                'action'=>'create'
                            ],
                        ],
                    ],
                ]],
];
$idcol = 0;
//\appxq\sdii\utils\VarDumper::dump($data,0);
?>
<div class="row " style="margin-bottom: 5px;">
    <div class="col-md-3 col-md-offset-9">
        <?=  Html::button('<i class="glyphicon glyphicon-plus"></i> '. Yii::t('ezform', 'Add levels'), ['class'=>'btn btn-success btn-block', 'id'=>'add-items-lvl', 'data-attr'=>$model->ezf_field_name])?>
    </div>
</div>

<div id="items-editor">
    <table class="table" style="margin-bottom: 5px;background-color: #fff;">
        <?php 
        $thead = true;
        $del_lvl = false;
        $del_field = false;
        $col = 1;
        foreach ($builder as $id => $value) {
            if(is_array($value['fields'])){
                
                ?>
                <?php if(!$thead){
                    echo '<tr>';
                }
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
                        <th style="width: 350px"><?=Yii::t('ezform', 'Question')?></th>
                        <?php if(isset($obj['data']) && is_array($obj['data'])):?>
                            <?php foreach ($obj['data'] as $key_item => $value_item):?>
                            <th style="position:relative;">
                                <?=  Html::textInput("data[builder][$id][fields][$xy][data][$key_item][value]", $value_item['value'], ['class'=>'form-control', 'id'=>"value_item_$key_item" , 'placeholder'=>Yii::t('ezform', 'Value')])?>
                                <?=  Html::textInput("data[builder][$id][fields][$xy][data][$key_item][label]", $value_item['label'], ['class'=>'form-control ', 'id'=>"label_item_$key_item", 'placeholder'=>Yii::t('ezform', 'Levels')])?>
                                <?php if($del_lvl):?>
                                    <i class="fa fa-close del-items-lvl" data-id="<?=$id?>" data-row="<?=$row[0]?>" data-item-id="<?=$key_item?>" data-col="<?=$col?>" data-var="<?=$model->ezf_field_name?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
                                <?php endif;?>
                                <?= Html::hiddenInput("data[builder][$id][fields][$xy][data][$key_item][action]", $value_item['action'], ['id'=>"action_item_$key_item"])?>
                            </th>
                            <?php $del_lvl=true; $col++?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </tr>
                </thead>
                <tbody>
                    <tr data-id="<?=$id?>">
                        <?php $thead = false;?>
                <?php endif;?>
                    
                        <td style="position:relative;" class="form-inline">
                            <div class="form-group">
                                <?=  Html::textInput("data[builder][$id][fields][$xy][label]", $obj['label'], ['class'=>'form-control ', 'id'=>"label_$id", 'style'=>'width: 240px', 'placeholder'=>Yii::t('ezform', 'Question')])?>
                            </div>
                            <div class="form-group">
                                <?=  Html::textInput("data[builder][$id][fields][$xy][attribute]", $obj['attribute'], ['class'=>'form-control check_varname', 'id'=>"value_$id" , 'style'=>'width: 90px', 'placeholder'=>Yii::t('ezform', 'Variable')])?>
                            </div>
                            <?php if($del_field):?>
                                <i class="fa fa-close del-items-field" data-id="<?=$id?>" data-var="<?=$model->ezf_field_name?>" data-row="<?=$row[0]?>" style="position:absolute;right: 10px;top:10px;cursor:pointer;color:#9F9F9F;"></i>
                            <?php endif;?>
                            <?= Html::hiddenInput("data[builder][$id][fields][$xy][id]", $obj['id'])?>
                            <?= Html::hiddenInput("data[builder][$id][fields][$xy][action]", $obj['action'])?>
                            <?= Html::hiddenInput("data[builder][$id][fields][$xy][type]", $obj['type'])?>
                        </td>
                        
                        <?php for($i=1;$i<=$data_col;$i++):?>
                        <td style="text-align: center"><?= Html::radio('builder_'.$id)?></td>
                        <?php endfor;?>
                <?php
                    $del_field=true;
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
        <?=  Html::button('<i class="glyphicon glyphicon-plus"></i> '. Yii::t('ezform', 'Add question'), ['class'=>'btn btn-success btn-block', 'id'=>'add-items-field', 'data-attr'=>$model->ezf_field_name])?>
    </div>
</div>
<div id="items-delete">
    
</div>

<?= Html::hiddenInput('options[options][data-row]', $data_row, ['id'=>'data-row']) ?>
<?= Html::hiddenInput('options[options][data-col]', $data_col, ['id'=>'data-col']) ?>
<?= Html::hiddenInput('options[options][data-type]', 'fields') ?>

<?php
$this->registerJs("
$('#items-editor').on('click', '.del-items-field', function() {
    var id = $(this).attr('data-id');
    var row = $(this).attr('data-row');
    var selector = $(this).parent().find('input[name=\"data[builder]['+id+'][fields]['+row+'_1][action]\"]').val();

    if(selector=='update'){
        var field_attribute = $(this).parent().find('input[name=\"data[builder]['+id+'][fields]['+row+'_1][attribute]\"]').val();
        
	$('#items-delete').append('<input type=\"hidden\" name=\"data[delete_fields]['+id+']\" value=\"'+field_attribute+'\">');
        
    } 
    $(this).parent().parent().remove();
    
});

$('#items-editor').on('click', '.del-items-lvl', function() {
    var id = $(this).attr('data-id');
    var row = $(this).attr('data-row');
    var item_id = $(this).attr('data-item-id');
    var selector = $(this).parent().find('#action_item_'+item_id).val();
    
    if(selector=='update'){
	$('#items-delete').append('<input type=\"hidden\" name=\"data[delete][]\" value=\"'+item_id+'\">');
    } 
    $(this).parent().remove();
    var col = parseInt($('#data-col').val())-1;
    $('#data-col').val(col);
    
    $( '#items-editor table tbody tr' ).each(function( index ) {
        var name_id = 'builder_'+$(this).attr('data-id');
        $(this).find('td').last().remove();
    });
});


$('#add-items-lvl').click(function() {
    var col = parseInt($('#data-col').val())+1;
    var row = parseInt($('#data-row').val());
    var attr = $(this).attr('data-attr');
    var id = '$idcol';
    
    $('#data-col').val(col);
    
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezforms2/scale/lvl'])."',
	data: {row:row, col:col, attr:attr, id:id},
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		$('#items-editor table thead tr').append(result.html);	
                $('#items-editor table tbody tr').each(function( index ) {
                    var name_id = 'builder_'+$(this).attr('data-id');
                    $(this).append('<td style=\"text-align: center\"><input type=\"radio\" name=\"'+name_id+'\" value=\"1\"></td>');
                });
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
});

$('#add-items-field').click(function() {
    var row = parseInt($('#data-row').val())+1;
    var col = parseInt($('#data-col').val());
    var attr = $(this).attr('data-attr');
    $('#data-row').val(row);
    
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezforms2/scale/create'])."',
	data: {row:row, col:col, attr:attr},
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