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
$id_gen = appxq\sdii\utils\SDUtility::getMillisecTime();
$enable_tumbon = isset($model['ezf_field_options']['enable_tumbon'])?$model['ezf_field_options']['enable_tumbon']:1;

$provinceId = appxq\sdii\utils\SDUtility::getMillisecTime();
$amphurId = appxq\sdii\utils\SDUtility::getMillisecTime();
$tumbonId = appxq\sdii\utils\SDUtility::getMillisecTime();

$builder = isset($data['builder'])?$data['builder']:[
    $id_gen => ['fields'=>[
                    '1_1' => [
                        'attribute'=>$model->ezf_field_name.'_province',
                        'id'=>$provinceId,
                        'label'=> 'province',
                        'action'=>'create',
                        'type'=>'id',
                    ],
                    '1_2' => [
                        'attribute'=>$model->ezf_field_name.'_amphur',
                        'id'=>$amphurId,
                        'label'=>'amphur',
                        'action'=>'create',
                        'type'=>'id',
                    ],
                    '1_3' => [
                        'attribute'=>$model->ezf_field_name.'_tumbon',
                        'id'=>$tumbonId,
                        'label'=>'tumbon',
                        'action'=>'create',
                        'type'=>'id',
                    ],
                ]],
];

?>
<div class="form-group row">
    <div class="col-md-3">
	
    </div>
    <div class="col-md-3 sdbox-col">
	<?=  Html::label(Yii::t('ezform', 'Variable').' '.Yii::t('ezform', 'Province'))?>
    </div>
    <div class="col-md-3 sdbox-col">
	<?=  Html::label(Yii::t('ezform', 'Variable').' '. Yii::t('ezform', 'Amphur'))?>
    </div>
    <div class="col-md-3 sdbox-col tumbon-box">
	<?=  Html::label(Yii::t('ezform', 'Variable').' '.Yii::t('ezform', 'Tumbon'))?>
    </div>
</div>

<div class="form-group row">
    <div class="col-md-3">
	<?= Html::checkbox('options[enable_tumbon]', $enable_tumbon, ['label'=> Yii::t('ezform', 'Show tumbon'), 'id'=>'show-tumbon']);?>
    </div>
<?php 
foreach ($builder as $id => $value) {
    if(is_array($value['fields'])){
        foreach ($value['fields'] as $xy => $obj) {
            //appxq\sdii\utils\VarDumper::dump($xy,0);
            ?>
            <div class="col-md-3 sdbox-col <?=($xy=='1_3'?'tumbon-box':'')?>">
                <?= Html::textInput("data[builder][$id][fields][$xy][attribute]", $obj['attribute'], ['class'=>'form-control check_varname'])?>
                <?= Html::hiddenInput("data[builder][$id][fields][$xy][id]", $obj['id'])?>
                <?= Html::hiddenInput("data[builder][$id][fields][$xy][label]", $obj['label'])?>
                <?= Html::hiddenInput("data[builder][$id][fields][$xy][action]", $obj['action'])?>
                <?= Html::hiddenInput("data[builder][$id][fields][$xy][type]", $obj['type'])?>
            </div>
<?php
        }
    }
}
?>
    <?= Html::hiddenInput('options[options][data-type]', 'fields') ?>
</div>

<?php
$this->registerJs("
    
$('#box-data').on('click', '#show-tumbon', function() {
    
});


");
?>