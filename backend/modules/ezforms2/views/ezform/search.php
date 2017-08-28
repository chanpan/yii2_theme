<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Select2;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?> 
<div class="user-search">
    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName(),
                'action' => ['index', 'tab' => $tab],
                'method' => 'get',
                'options' => ['data-pjax' => true, 'class'=>'form-inline'],
    ]);
    ?>
    
        <?= $form->field($model, 'ezf_name')->textInput(['class' => 'form-control', 'placeholder' => Yii::t('ezform', 'Find the form name.')])->label(false); ?>

        <?php
        echo $form->field($model, 'created_by')->widget(Select2::className(), [
            'data' => $userlist,
            'options' => ['placeholder' => Yii::t('ezform', 'Find the creator name.'), 'multiple' => FALSE],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ])->label(false);
        ?>
    <div class="form-group" style="margin-bottom: 10px;">
        <?= Html::button(SDHtml::getBtnSearch(), ['class' => 'btn btn-default', 'type' => 'submit']); ?>
        <?php
        if ($tab == '1') {
            echo Html::button(SDHtml::getBtnAdd(), ['data-url' => Url::to(['ezform/create']), 'class' => 'btn btn-success', 'id' => 'modal-addbtn-ezform']);
        }
        ?>
    </div>
        
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs(" 
$('form#{$model->formName()}').on('change', function(e) {
    $(this).submit();   
});
");
?>
