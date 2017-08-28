<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\helpers\Html;
use yii\helpers\Url;

$moduleID = '';
$controllerID = '';
$actionID = '';

if (isset(Yii::$app->controller->module->id)) {
	    $moduleID = Yii::$app->controller->module->id;
}
if (isset(Yii::$app->controller->id)) {
	    $controllerID = Yii::$app->controller->id;
}
if (isset(Yii::$app->controller->action->id)) {
	    $actionID = Yii::$app->controller->action->id;
}

?>

<div class="row">
    <div class="col-md-12">
        <?= Html::a('<i class="fa fa-edit"></i> '. Yii::t('ezform', 'Edit form'), ['/ezbuilder/ezform-builder/update', 'id' => $ezf_id], ['class' => 'btn btn-flat '.($controllerID=='ezform-builder' && $actionID=='update'?'btn-info':'btn-primary')]) ?>
        <?= Html::a('<i class="glyphicon glyphicon-eye-open"></i> '. Yii::t('ezform', 'Preview form'), ['/ezbuilder/ezform-builder/viewform', 'id' => $ezf_id], ['class' => 'btn btn-flat '.($controllerID=='ezform-builder' && $actionID=='viewform'?'btn-info':'btn-primary')]) ?>
        <?php //echo Html::a('<i class="fa fa-file-excel-o"></i> ' . Yii::t('ezform', 'Builder tool'), '#', ['class' => 'btn btn-flat btn-primary']) ?>
        <?= Html::a('<i class="glyphicon glyphicon-export"></i> ' . Yii::t('ezform', 'Export Form'), ['/ezbuilder/ezform-builder/export', 'ezf_id' => $ezf_id], ['class' => 'btn btn-flat btn-primary', 'target'=>'_blank']) ?>
        <div class="pull-right">
            <?= Html::a('<i class="fa fa-mail-reply"></i> ' . Yii::t('ezform', 'Back to form page'), ['/ezforms2/ezform/index'], ['class' => 'btn btn-warning btn-flat']) ?>
        </div>
    </div>
</div>
