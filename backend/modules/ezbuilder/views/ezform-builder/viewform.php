<?php

use backend\modules\ezforms2\classes\EzActiveForm;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfFunc;

/**
 * viewform file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 1 ก.ย. 2559 10:41:10
 * @link http://www.appxq.com/
 */

$this->title = $modelEzf->ezf_name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('ezform', 'Ezforms'), 'url' => ['/ezforms2/ezform/index']];
$this->params['breadcrumbs'][] = Yii::t('ezform', 'Preview form');
?>
    
<?= $this->render('_menu', [
	'model' => $model,
	'ezf_id' => $ezf_id,
	'modelFields' => $modelFields,
]);?>

<div class="ezform-box">
    <?php $form = EzActiveForm::begin([
	'options' => [
	    'enctype' => 'multipart/form-data'
	]
    ]); ?>
    <div class="modal-header">
	<h3 class="modal-title" id="itemModalLabel"><?=$modelEzf->ezf_name?> <small><?=$modelEzf->ezf_detail?></small></h3>
    </div>
    <div class="modal-body">
	<div id="formPanel" class="row">
	    <?php
	    foreach ($modelFields as $field) {
                if($field['ezf_field_type']>0){
                    $disabled = 0;
                    if(isset($field['ezf_field_ref']) && $field['ezf_field_ref']>0){
                        $cloneRefField = EzfFunc::cloneRefField($field);
                        $field = $cloneRefField['field'];
                        $disabled = $cloneRefField['disabled'];
                    }

                    $dataInput;
                    if (isset(Yii::$app->session['ezf_input'])) {
                        $dataInput = EzfFunc::getInputByArray($field['ezf_field_type'], Yii::$app->session['ezf_input']);
                    }
                
		
                    echo EzfFunc::generateInput($form, $model, $field, $dataInput, $disabled);
                    if($field['ezf_condition']==1){
                        EzfFunc::generateCondition($model, $field, $modelEzf, $this, $dataInput);
                    }

                    if(isset($field['ezf_field_cal']) && $field['ezf_field_cal']!=''){
                        $cut = preg_match_all("%{(.*?)}%is", $field['ezf_field_cal'], $matches);
                        if($cut){
                            $varArry = $matches[1];
                            $createEvent = EzfFunc::genJs($varArry, $model, $field);
                            $this->registerJs($createEvent);
                        }
                    }
                }
	    }
	    ?>
	</div>
    </div>
    
    <?php EzActiveForm::end(); ?>
</div>
    
    <?php backend\modules\ezforms2\classes\EzfStarterWidget::begin();?>
    
    <?php backend\modules\ezforms2\classes\EzfStarterWidget::end();?>
<?php
$this->registerJs("
            
");

?>

