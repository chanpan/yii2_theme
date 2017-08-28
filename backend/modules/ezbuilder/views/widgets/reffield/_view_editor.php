<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use appxq\sdii\utils\SDUtility;

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

$pathInput = [
    '{input}'=>Html::activeTextInput($model, 'ezf_field_default', $options),
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
    <div class="col-md-6 ">
        <?= Html::label(Yii::t('ezform', 'Form'), 'EzformFields[ref_ezf_id]', ['class' => 'control-label']) ?>
        <?php 
        $itemsEzform = \backend\modules\ezforms2\classes\EzfQuery::getEzformCoDev($model['ezf_id']);

        echo kartik\select2\Select2::widget([
            'model' => $model,
            'attribute'=>'ref_ezf_id',
            'options' => ['placeholder' => Yii::t('ezform', 'Form'), 'id'=>'ref_ezf_id_'.$model['ezf_field_name']],
            'data' => ArrayHelper::map($itemsEzform,'ezf_id','ezf_name'),
            'pluginOptions' => [
                'allowClear' => true
            ],
//            'pluginEvents' => [
//		"select2:select" => "function(e) {  console.log('select') }",
//                "select2:selecting" => "function(e) {  console.log('selecting') }",
//                
//	    ]
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Relation variable'), 'EzformFields[ref_field_id]', ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
        
    </div>
    
</div>

<?php
$this->registerJS("
    fields($('#ref_ezf_id_".$model['ezf_field_name']."').val());
    
    $('#ref_ezf_id_".$model['ezf_field_name']."').on('change',function(){
      var ezf_id = $(this).val();
      fields(ezf_id);
      
    });
    
    function fields(ezf_id){
        var value = '{$model->ref_field_id}';
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:0, name: 'EzformFields[ref_field_id]', value: value ,id:'ref_field_id'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>