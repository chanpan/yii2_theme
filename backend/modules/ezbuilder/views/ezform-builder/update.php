<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use kartik\widgets\Select2;
use kartik\tree\TreeViewInput;
use backend\modules\ezforms2\models\EzformTree;

\backend\modules\ezforms2\assets\EzfToolAsset::register($this);

$this->title = $model->ezf_name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezforms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = Yii::t('ezform', 'Edit form');

$op['key'] = 'AIzaSyCq1YL-LUao2xYx3joLEoKfEkLXsEVkeuk';
$op['language'] = 'th';

$q = array_filter($op);

$this->registerJsFile('https://maps.google.com/maps/api/js?'.http_build_query($q), [
    'position'=>\yii\web\View::POS_HEAD,
    'depends'=>'yii\web\YiiAsset',
]);
?>

<?= $this->render('_menu', [
	'model' => $model,
	'ezf_id' => $ezf_id,
	'modelFields' => $modelFields,
]);?>

<div id="ezform-update-main-box">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist" style="margin-top: 20px;">
      <li role="presentation" class="active"><a href="#ezf-update-tab-panel" aria-controls="ezf-update-tab-panel" role="tab" data-toggle="tab"><?=Yii::t('app', 'Form')?> <strong><?=$model->ezf_name?></strong></a></li>
    <li role="presentation"><a href="#ezf-config-tab-panel" aria-controls="ezf-config-tab-panel" role="tab" data-toggle="tab"><?=Yii::t('app', 'Setting')?></a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="ezf-update-tab-panel">
          <?= $this->render('_ezf_editor', [
                'model' => $model,
                'ezf_id' => $ezf_id,
                'modelFields' => $modelFields,
                'userlist' => $userlist,
                'modelCoDevs' => $modelCoDevs,
                'modelDynamic' => $modelDynamic,
                'tccTables' => $tccTables,
                'userprofile'=>$userprofile,
        ]);?>
      </div>
      <div role="tabpanel" class="tab-pane" id="ezf-config-tab-panel">
          <?=$this->render('@app/modules/ezforms2/views/ezform/_form', [
                'model' => $model,
                'modelFields' => $modelFields,
                'userlist' => $userlist
            ]);
            ?> 
          
      </div>
  </div>

</div>




    
</div>


<?= ModalForm::widget([
    'id' => 'modal-ezform',
    'size' => 'modal-lg',
    'tabindexEnable' => false,
]);
?>

<?= ModalForm::widget([
    'id' => 'modal-condition',
    'size'=>'modal-sm',
]);
?>

<?= ModalForm::widget([
    'id' => 'modal-gridtype',
    'size'=>'modal-md',
]);
?>

<?php
// AUTO Save EzForm 
$this->registerJs("

$('#modal-ezform').on('hidden.bs.modal', function (e) {
    $('.sp-container').remove();
    $('.redactor-toolbar-tooltip').remove();
})

function modalEzform(url) {
    $('#modal-ezform .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform').modal('show')
    .find('.modal-content')
    .load(url);
}

function modalCondition(url) {
    $('#modal-condition .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-condition').modal('show')
    .find('.modal-content')
    .load(url);
}

function modalGridtype(url) {
    $('#modal-gridtype .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-gridtype').modal('show')
    .find('.modal-content')
    .load(url);
}

");

?>

<?php 
// set var top
$this->registerJs(" 
	var baseUrlTo = '".Url::to(['/ezbuilder'])."';
", 1);
?>

<?php 
// set var end
$this->registerJs("
	var eid = '".$ezf_id."';
	var baseUrl = '".Url::to(['/ezbuilder/ezform-condition'])."';
	var conditionUrl = '".Url::to(['/ezbuilder/ezform-condition/condition'])."';
	var fieldsUrl = '".Url::to(['/ezbuilder/ezform-condition/fields', 'id'=>$_GET['id']])."';
", 3);


?>
