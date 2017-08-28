<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\EzformFields */
/* @var $form yii\bootstrap\ActiveForm */
backend\modules\ezforms2\assets\EzfColorInputAsset::register($this);
?>

<div class="ezform-fields-form">

    <?php $form = ActiveForm::begin([
	'id'=>$model->formName(),
    ]); ?>

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true" >&times;</button>
        <h4 class="modal-title" id="itemModalLabel"><?= Yii::t('ezform', 'Question')?></h4>
    </div>

    <div class="modal-body">
	
	<div class="row">
		<?php
		//init data
		$specific = isset($model['ezf_field_options']['specific'])?$model['ezf_field_options']['specific']:[];
                $icon = isset($specific['icon'])?$specific['icon']:'';
                $color = isset($specific['color'])?$specific['color']:'';
		?>
		<div class="col-md-8 ">
		    <?= $form->field($model, 'ezf_field_label')->textInput(['maxlength' => true, ]) ?>
		</div>
		<div class="col-md-2 sdbox-col">
		    <div class="form-group">
			<?=  Html::label(Yii::t('ezform', 'icon'), 'options[specific][icon]')?>
			<?=  Html::textInput('options[specific][icon]', $icon, ['class'=>'dicon-input form-control'])?>
		    </div>
		</div>
		<div class="col-md-2 sdbox-col">
		    <?=  Html::label(Yii::t('ezform', 'Label color'), 'options[specific][color]')?>
		    <div class="input-group">
			<?=  Html::textInput('options[specific][color]', $color, ['class'=>'form-control', 'id'=>'specific-color'])?>
		    </div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-8 ">
		    <?php
		    $inputList = EzfQuery::getInputv2All();
		    $dataInput = ArrayHelper::map($inputList, 'input_id', 'input_name');
                    echo Html::label(Yii::t('ezform', 'Question type'), 'EzformFields[ezf_field_type]');
                    echo kartik\select2\Select2::widget([
                        'model' => $model,
                        'attribute'=>'ezf_field_type',
                        'options' => ['placeholder' => Yii::t('ezform', 'Question type')],
                        'data' => $dataInput,
                    ]);
		    ?>
		    
		</div>
		<div class="col-md-2 sdbox-col">
		    <?= $form->field($model, 'ezf_field_name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-2 sdbox-col">
		    <?=
		    $form->field($model, 'ezf_field_color', [
			'inputTemplate' => '<div class="input-group">{input}</div>',
		    ])->textInput();
		    ?>
		    
		</div>
	</div>
	
        
	<div class="row">
	    <div class="col-md-8 ">
		<?php 
                    $settings = [
			    'minHeight' => 30,
			    'imageManagerJson' => Url::to(['/ezforms2/text-editor/images-get']),
                            'fileManagerJson' => Url::to(['/ezforms2/text-editor/files-get']),
			    'imageUpload' => Url::to(['/ezforms2/text-editor/image-upload']),
                            'fileUpload' => Url::to(['/ezforms2/text-editor/file-upload']),
			    'plugins' => [
                                'fontcolor',
                                'fontfamily',
                                'fontsize',
				'textdirection',
                                'textexpander',
                                'counter',
                                'table',
                                'definedlinks',
                                'video',
				'imagemanager',
                                'filemanager',
                                'limiter',
                                'fullscreen',
			    ]
                    ];
                    $lang = Yii::$app->language;
                    if($lang!='en-US'){
                        $settings['lang'] = backend\modules\ezforms2\classes\EzfFunc::getLanguage();
                    }
                    
                    echo $form->field($model, 'ezf_field_hint')->widget(vova07\imperavi\Widget::className(), [
		    'settings' => $settings
		]);?>
	    </div>
	    <div class="col-md-2 sdbox-col" style="padding-top: 15px;">
		    <?= $form->field($model, 'ezf_field_required')->checkbox() ?>
                    
	    </div>
	    <div class="col-md-2 sdbox-col">
		<?= $form->field($model, 'ezf_field_lenght')->dropDownList([1=>'8.32% (1)',2=>'16.64% (2)',3=>'25% (3)',4=>'33.33% (4)',5=>'41.67% (5)',6=>'50%  (6)',7=>'58.36%  (7)',8=>'66.66%  (8)',9=>'75%  (9)',10=>'83.34%  (10)',11=>'91.66%  (11)',12=>'100%  (12)']) ?>
	    </div>
            <div class="col-md-2 sdbox-col" style="padding-top: 15px;">
                    <?= $form->field($model, 'table_index')->checkbox() ?>
            </div>
	    <div class="col-md-2 sdbox-col" >
                    <?php //echo $form->field($model, 'table_field_length')->textInput(['maxlength' => true]) ?>
                    <?php echo $form->field($model, 'table_field_length')->hiddenInput()->label(FALSE) ?>
                    <?php //echo $form->field($model, 'ezf_field_order')->textInput(['type' => 'number'])?>
                    <?php echo $form->field($model, 'ezf_field_order')->hiddenInput()->label(FALSE) ?>
	    </div>
            
	</div>
	
	<div id="box-data" class="well">
	    ...
	</div>
	
	<div class="row">
            <div class="col-lg-12">
                <p style="cursor:pointer" id="btn-setting"><i class="fa fa-cog"></i> <?= Yii::t('ezform', 'Advanced settings')?></p>
            </div>
        </div>
	<div id="box-config" style='display:none;'>
	    <div id="box-options">
		
	    </div>
	    
	    <div id="box-validations">
		
	    </div>
	</div>
	
	
	<?= $form->field($model, 'ezf_field_id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_id')->hiddenInput()->label(FALSE) ?>

	<?= $form->field($model, 'ezf_field_group')->hiddenInput()->label(FALSE) ?>


	<?= $form->field($model, 'ezf_field_ref')->hiddenInput()->label(FALSE) ?>

	

        <?= $form->field($model, 'table_field_type')->hiddenInput()->label(FALSE) ?>
        

    </div>
    <div class="modal-footer">
	<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => ($model->isNewRecord ? 'btn btn-success' : 'btn btn-primary').' btn-submit']) ?>
	<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php  $this->registerJs("
var color_options = {
    showInput: true,
    showInitial:true,
    allowEmpty:true,
    showPalette:true,
    showSelectionPalette:true,
    hideAfterPaletteSelect:true,
    showAlpha:false,
    preferredFormat:'hex',
    palette: [
        ['#000','#444','#666','#999','#ccc','#eee','#f3f3f3','#fff'],
        ['#f00','#f90','#ff0','#0f0','#0ff','#00f','#90f','#f0f'],
        ['#f4cccc','#fce5cd','#fff2cc','#d9ead3','#d0e0e3','#cfe2f3','#d9d2e9','#ead1dc'],
        ['#ea9999','#f9cb9c','#ffe599','#b6d7a8','#a2c4c9','#9fc5e8','#b4a7d6','#d5a6bd'],
        ['#e06666','#f6b26b','#ffd966','#93c47d','#76a5af','#6fa8dc','#8e7cc3','#c27ba0'],
        ['#c00','#e69138','#f1c232','#6aa84f','#45818e','#3d85c6','#674ea7','#a64d79'],
        ['#900','#b45f06','#bf9000','#38761d','#134f5c','#0b5394','#351c75','#741b47'],
        ['#600','#783f04','#7f6000','#274e13','#0c343d','#073763','#20124d','#4c1130']
    ]
};

$('#box-data').on('blur', '.check_varname', function() {
    var value = $(this).val();
    
    if(!value.match(/^[a-z0-9_]+$/i)){
        $(this).parent().find('.help-block').remove();
        $(this).parent().append('<div class=\"help-block help-block-error\"><code>".Yii::t('ezform', 'Variables must be in English or numbers only and do not contain spaces.')."</code></div>');
        $(this).focus();
    } else {
        $(this).parent().find('.help-block').remove();
    }

});

$('#specific-color').spectrum(color_options);
$('#ezformfields-ezf_field_color').spectrum(color_options);

$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    $('.btn-submit').attr('disabled', true);
    
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    ". SDNoty::show('result.message', 'result.status') ."
	    if(result.action == 'create') {
		$(document).find('#modal-ezform').modal('hide');
		$('#ezf-box').append(result.html);
		$('#ezf-box').removeClass('dads-children');
	    } else if(result.action == 'update') {
		$(document).find('#modal-ezform').modal('hide');
                var prev_ele = $('.dads-children[data-dad-id=\"'+result.data.ezf_field_id+'\"]').prev();
                
		$('.dads-children[data-dad-id=\"'+result.data.ezf_field_id+'\"]').remove();
		var order = result.data.ezf_field_order-1;
                
                if(prev_ele.length>0){
                    prev_ele.after(result.html);
                } else if(order>0){
		    $('.dads-children[data-dad-position=\"'+order+'\"]').after(result.html);
		} else {
		    $('#ezf-box').prepend(result.html);
		}
		
		$('#ezf-box').removeClass('dads-children');
	    }
	    if(result.alterTable == false) {
		". SDNoty::show('"'.Yii::t('ezform', 'Column creation failed.').'"', '"error"') ."
                    $('.btn-submit').attr('disabled', false);
	    }
	    
	} else {
	    ". SDNoty::show('result.message', 'result.status') ."
                $('.btn-submit').attr('disabled', false);
	} 
    }).fail(function() {
        $('.btn-submit').attr('disabled', false);
	". SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') ."
	console.log('server error');
    });
    return false;
});

$('.dicon-input').iconpicker({
    title: 'Using Font Awesome',
    icons: ['bed', 'bug', 'bolt', 'ban', 'book', 'bell', 'birthday-cake', 'bookmark', 'building', 'calculator', 'calendar',
    'bus', 'camera', 'car', 'check', 'times', 'check-circle', 'check-circle-o', 'circle', 'circle-o', 'clock-o', 'child', 'cloud',
    'coffee', 'cube', 'cubes', 'cutlery', 'envelope-o', 'diamond', 'exclamation-circle', 'exchange', 'female', 'male', 'flask',
    'folder-open-o', 'folder-o', 'users', 'user', 'heartbeat', 'heart-o', 'heart', 'gift', 'globe', 'picture-o', 'minus-circle',
    'phone', 'question-circle', 'plus-circle', 'shield', 'share-alt', 'star', 'star-o', 'thumbs-o-up', 'thumbs-o-down', 'times-circle',
    'unlock-alt', 'unlock', 'wrench', 'trophy', 'trash', 'tree', 'life-ring', 
    'shopping-cart', 'paper-plane', 'search', 'retweet', 'random', 'wheelchair', 'user-md', 'stethoscope', 'hospital-o', 'medkit',
    'h-square', 'ambulance', 'link', 'chain-broken'
    ],
    iconBaseClass: 'fa',
    iconComponentBaseClass: 'fa',
    iconClassPrefix: 'fa-'
});

var field_type_tmp = $('#ezformfields-ezf_field_type').val();
$('#ezformfields-ezf_field_type').change(function() {
    var type_tmp = 1;
    if(field_type_tmp==$(this).val()){
        type_tmp = 0;
    }
    getViewEditor($(this).val(), type_tmp);
});

$('#btn-setting').click(function(){
    $('#box-config').toggle();
});

getViewEditor($('#ezformfields-ezf_field_type').val(), 0);

function getViewEditor(id, newitem){
    $.ajax({
		method: 'POST',
		url:'".yii\helpers\Url::to(['/ezbuilder/ezform-fields/view-input'])."',
		data: {id:id, newitem:newitem, ezf_field_id:'".$model->ezf_field_id."', ezf_id:'".$model->ezf_id."', label:$('#ezformfields-ezf_field_label').val(), name:$('#ezformfields-ezf_field_name').val()},
		dataType: 'JSON',
		success: function(result, textStatus) {
		    if(result.status == 'success') {
			$('#box-data').html(result.html);
			$('#box-options').html(result.options);
			$('#box-validations').html(result.validations);
                        $('#ezformfields-ezf_field_lenght').val(result.size);
		    } else {
			$('#box-data').html('');
			$('#box-options').html('');
			$('#box-validations').html('');
                        $('#ezformfields-ezf_field_lenght').val(3);
			". SDNoty::show('result.message', 'result.status') ."
		    }
		}
    })
}

");
?>