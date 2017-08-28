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
unset($options['specific']);
$config = ArrayHelper::merge($options, ['model'=>$model,'attribute'=>'ezf_field_default']);

$pathInput = [
    '{input}'=> Html::activeHiddenInput($model, 'ezf_field_default', $options).Html::img('@storageUrl/ezform/img/drawing.png', ['height'=>250]),
];
$input = strtr($inputTemplate, $pathInput);

$path = [
    '{label}'=>Html::label(Yii::t('ezform', 'Question'), 'EzformFields["ezf_field_default"]', $labelOptions),
    '{input}'=>' '.$input,
    '{hint}'=>'',
    '{error}'=>'',
];

$content = strtr($template, $path);
 
?>

<div class="form-group row">
    <div class="col-md-6 pull-right">
        <a class="fileUpload btn btn-success"><?= Yii::t('ezform', 'Upload a background image')?>
                    <input type="file" id="option-bg-id" class="upload" name="option-bg" accept="image/*" data-url="<?= yii\helpers\Url::to(['/ezforms2/drawing/option-image', 'name'=>$model->ezf_field_name])?>">                    
                </a>
                <?= \backend\modules\ezforms2\classes\EzformWidget::checkbox('options[allow_bg]', isset($model['ezf_field_options']['allow_bg'])?$model['ezf_field_options']['allow_bg']:0, ['label'=> Yii::t('ezform', 'Allows changing background image.')]); ?>
                <div id="showImg"></div>
                
    </div>
    <div class="col-md-6" >
	<?=$content?>
    </div>
</div>

<?php
if($model->ezf_field_default!=''){
    list($width, $height, $type, $attr) = getimagesize(Yii::getAlias('@storage/ezform/drawing/bg/').$model->ezf_field_default);
    
    if($width>0 && $height>0){
        $this->registerJs("
                var bgsize = 'auto auto';
                if({$width} > {$height}){
                bgsize = '300px auto';
                } else {
                bgsize = 'auto 200px';
                }
                $('#showImg').css('background-image', 'url(". yii\helpers\Url::to(Yii::getAlias('@storageUrl').'/ezform/drawing/bg/'.$model->ezf_field_default).")');
                $('#showImg').css('background-size', bgsize);
                $('#showImg').css('background-position', 'center center');
                $('#showImg').css('background-repeat', 'no-repeat');
        ");
    }
}

dosamigos\fileupload\FileUploadPlusAsset::register($this);
$clientOptions = [
        //'data-url'=>yii\helpers\Url::to(['/ezforms/drawing/option-image', 'name'=>$model->ezf_field_name]),
        'maxFileSize' => 3000000
];

$opJs = \yii\helpers\Json::encode($clientOptions);

$id = 'option-bg-id';
$js[] = ";jQuery('#$id').fileupload($opJs);";

$clientEvents = [
'fileuploaddone' => "function(e, data) {
                        var bgsize = 'auto auto';
                        
                        $('input[name=\"option-bg\"]').attr('data-url', data.result.files.newurl);
                        $('input[name=\"option-bg\"]').fileupload({'maxFileSize':3000000,'url':$('input[name=\"option-bg\"]').attr('data-url')});

                        $('#ezformfields-ezf_field_default').val(data.result.files.name);
                        console.log($('#ezformfields-ezf_field_default').val());
                        $('#showImg').css('background-image', 'url('+data.result.files.url+')');
                        $('#showImg').css('background-size', bgsize);
                        $('#showImg').css('background-position', 'center center');
                        $('#showImg').css('background-repeat', 'no-repeat');
                        }",
'fileuploadfail' => "function(e, data) {
                        console.log(e);
                        console.log(data);
                        }",
];
if (!empty($clientEvents)) {
    foreach ($clientEvents as $event => $handler) {
        $js[] = "jQuery('#box-data').on('$event', '#$id',$handler);";
    }
}
$this->registerJs(implode("\n", $js));

?>