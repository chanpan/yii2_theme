<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use appxq\sdii\utils\SDUtility;

/**
 * _space_view_item file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 19:02:45
 * @link http://www.appxq.com/
 * @example  
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

$size = isset($model['ezf_field_options']['modal_size'])?$model['ezf_field_options']['modal_size']:'modal-xxl';
$theme = isset($model['ezf_field_options']['theme'])?$model['ezf_field_options']['theme']:'default';
$column = isset($model['ezf_field_options']['column'])?SDUtility::array2String($model['ezf_field_options']['column']):"''";

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
        <?= Html::label(Yii::t('ezform', 'Relation variable'), 'options[column]', ['class' => 'control-label']) ?>
        <div id="ref_field_box">
            
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <div class="col-md-6">
            <?= Html::label(Yii::t('ezform', 'Themes'), 'options[theme]', ['class' => 'control-label']) ?>
            <?= Html::dropDownList('options[theme]', $theme, ['default'=>'Default', 'primary'=>'Primary', 'success'=>'Success', 'info'=>'Info', 'warning'=>'Warning', 'danger'=>'Danger'], ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-6 sdbox-col">
            <?= Html::label(Yii::t('ezform', 'Modal Size'), 'options[modal_size]', ['class' => 'control-label']) ?>
            <?= Html::dropDownList('options[modal_size]', $size, ['modal-xxl'=> Yii::t('ezform', 'Very Large'), 'modal-lg'=>Yii::t('ezform', 'Large'), ''=>Yii::t('ezform', 'Normal'), 'modal-sm'=>Yii::t('ezform', 'Small')], ['class' => 'form-control']) ?>
        </div>
        <?= Html::hiddenInput('EzformFields[ref_field_id]', 'id') ?>
        <?= Html::hiddenInput('options[options][data-type]', 'viewer') ?>
        <?= Html::hiddenInput('options[ezf_field_id]', $model['ezf_field_id']) ?>
        <?= Html::hiddenInput('options[options][data-ezfid]', $model['ezf_id']) ?>
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
        var value = {$column};
        $.post('".Url::to(['/ezforms2/target/get-fields'])."',{ ezf_id: ezf_id, multiple:1, name: 'options[column]', value: value ,id:'set-column'}
          ).done(function(result){
             $('#ref_field_box').html(result);
          }).fail(function(){
              ". \appxq\sdii\helpers\SDNoty::show('"server error"', '"error"') . "
              console.log('server error');
          });
    }
    
");
?>