<?php
use kartik\select2\Select2;
if($multiple){
    echo Select2::widget([
        'id'=>$id,
        'name' => $name,
        'value'=>$value,
        'data' => $data,
        'options' => ['placeholder' => Yii::t('ezform', 'Select field ...'), 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
            'tags' => true,
            'tokenSeparators' => [',', ' '],
        ]
    ]);
} else {
    echo Select2::widget([
        'id'=>$id,
        'name' => $name,
        'value'=>$value,
        'data' => $data,
        'options' => ['placeholder' => Yii::t('ezform', 'Select field ...')],
        'pluginOptions' => [
            'allowClear' => true,
        ]
    ]);
}

