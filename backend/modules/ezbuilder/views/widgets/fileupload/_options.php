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

$previewFileType = isset($options['pluginOptions']['previewFileType'])?$options['pluginOptions']['previewFileType']:'image';
$multiple = isset($options['options']['multiple'])?$options['options']['multiple']:FALSE;
$allowedFile = isset($options['pluginOptions']['allowedFileExtensions'])?$options['pluginOptions']['allowedFileExtensions']:['pdf','png','jpg','jpeg', 'doc', 'docx', 'xls', 'xlsx'];

?>

<div class="well" style="padding: 15px; background-color: #dff0d8; border-color: #d6e9c6; ">
    <h4 style="margin-top: 0px;"><?= Yii::t('ezform', 'Options')?></h4>

    <div class="form-group">
	<div class="row">
	    <div class="col-md-6">
		<?= Html::label(Yii::t('ezform', 'Display'), 'options[pluginOptions][previewFileType]', ['class' => 'control-label']) ?>
		<?= Html::dropDownList('options[pluginOptions][previewFileType]', $previewFileType, [
                    'image'=>'image',
                    'html'=>'html',
                    'text'=>'text',
                    'video'=>'video',
                    'audio'=>'audio',
                    'flash'=>'flash',
                    'object'=>'object',
                    'other'=>'other',
                ], ['class' => 'form-control']) ?>
	    </div>
	    <div class="col-md-3 sdbox-col" style="padding-top: 30px;">
		<?= Html::checkbox('options[options][multiple]', $multiple, ['label' => Yii::t('ezform', 'Uploading multiple files')]) ?>
	    </div>
            <div class="col-md-3 sdbox-col">
                <?= Html::label(Yii::t('ezform', 'Allowed File Extensions'), 'options[pluginOptions][allowedFileExtensions][]', ['class' => 'control-label']) ?>
		<?php
                    if(isset($allowedFile) && !empty(($allowedFile))){
                        echo '<div id="box-type-file">';
		    foreach ($allowedFile as $key => $value) {
                        ?>
                        <div class="input-group" style="margin-bottom: 5px;">
			    <input type="text" class="form-control" name="options[pluginOptions][allowedFileExtensions][]" value="<?=$value?>">
			    <span class="input-group-addon">
				<a class="btn-type-del-file" style="color: #ff0000;cursor: pointer;"><i class="glyphicon glyphicon-remove"></i></a>
			    </span>
			</div>
                <?php
                    }
                    echo '</div>';
                    }
                ?>
                <?= Html::button("<i class='glyphicon glyphicon-plus'></i>", ['class' => 'btn btn-success btn-type-add-file']) ?>
	    </div>
	    <?= Html::hiddenInput('options[options][data-type]', 'file-upload') ?>
            <?= Html::hiddenInput('options[options][data-name-set]', 'initialPreview') ?>
            <?= Html::hiddenInput('options[options][data-name-in][pluginOptions][initialPreview]', '') ?>
            <?= Html::hiddenInput('options[options][data-data-widget]', '//../modules/ezforms2/views/widgets/_initial') ?>
	</div>
    </div>
    
</div>

<?php  $this->registerJs("
$('.btn-type-add-file').click(function(){
    
    var input = '".Html::textInput("options[pluginOptions][allowedFileExtensions][]", '', ['class' => 'form-control'])."';
    var btnDel = '".Html::a('<i class="glyphicon glyphicon-remove"></i>', '', ['class'=>'btn-type-del-file','style'=>'color: #ff0000;cursor: pointer;'])."';
    var content = '<div class=\"input-group\" style=\"margin-bottom: 5px;\">'+input+'<span class=\"input-group-addon\" >'+btnDel+'</span></div>';
    
    $('#box-type-file').append(content);
});

$('#box-type-file').on('click', '.btn-type-del-file', function() {
    $(this).parent().parent().remove();
    return false;
});

");?>