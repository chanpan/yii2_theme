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
$placeholder = isset($options['options']['placeholder'])?$options['options']['placeholder']:'ไม่ระบุ';

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
            'pluginEvents' => [
		"select2:select" => "function(e) {  console.log('select') }",
                //"select2:selecting" => "function(e) {  console.log('selecting') }",
                
	    ]
        ]);
        ?>
    </div>
    <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Explanation when not specified'), 'options[options][placeholder]', ['class' => 'control-label']) ?>
        <?= Html::textInput('options[options][placeholder]', $placeholder, ['class' => 'form-control']) ?>
    </div>
    <?= Html::hiddenInput('EzformFields[ref_field_id]', 'id')?>
    <?= Html::hiddenInput('EzformFields[parent_ezf_id]', $model['parent_ezf_id'], ['id'=>'parent_ezf_id_'.$model['ezf_field_name']])?>
</div>

<div class="form-group row">
    <div class="col-md-6 ">
        <?= Html::label(Yii::t('ezform', 'Display variables'), 'EzformFields[ref_field_desc]', ['class' => 'control-label']) ?>
        <div id="ref_desc_box">
            
        </div>
        
    </div>
    <div class="col-md-6 sdbox-col">
        <?= Html::label(Yii::t('ezform', 'Search variables'), 'EzformFields[ref_field_search]', ['class' => 'control-label']) ?>
        <div id="ref_search_box">
            
        </div>
    </div>
</div>

<?php
$this->registerJS("
    descFields($('#ref_ezf_id_".$model['ezf_field_name']."').val());
    searchFields($('#ref_ezf_id_".$model['ezf_field_name']."').val());

    $('#ref_ezf_id_".$model['ezf_field_name']."').on('change',function(){
      var ezf_id = $(this).val();
      descFields(ezf_id);
      searchFields(ezf_id);
      parentFields(ezf_id);
    });
    
    function parentFields(ezf_id){
        $.post('".Url::to(['/ezforms2/target/parent-fields'])."',{ ezf_id: ezf_id}
          ).done(function(result){
             $('#parent_ezf_id_{$model['ezf_field_name']}').val(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }

    function descFields(ezf_id){
        var value = ".SDUtility::array2String($model->ref_field_desc)."
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: 'EzformFields[ref_field_desc]', value: value ,id:'ref_field_desc'}
          ).done(function(result){
             $('#ref_desc_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
    function searchFields(ezf_id){
        var value = ".SDUtility::array2String($model->ref_field_search)."
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: 'EzformFields[ref_field_search]', value: value  ,id:'ref_field_search'}
          ).done(function(result){
             $('#ref_search_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
");
?>