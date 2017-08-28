<?php

use yii\helpers\Html;

/**
 * _textinput_options file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 19:02:45
 * @link http://www.appxq.com/
 * @example  
 */
$optionsTmp = isset($input['input_option'])?\appxq\sdii\utils\SDUtility::string2Array($input['input_option']):[];
$options = isset($model['ezf_field_options'])?$model['ezf_field_options']:$optionsTmp;

$config = isset($options['config'])?$options['config']:1;
?>

<div class="well" style="padding: 15px; background-color: #dff0d8; border-color: #d6e9c6; ">
    <h4 style="margin-top: 0px;"><?= Yii::t('ezform', 'Options')?></h4>
    
    <div class="form-group">
	<div class="row">
            <div class="col-md-12">
                <?= \backend\modules\ezforms2\classes\EzformWidget::radioList('options[config]', $config, ['data'=>[1=> Yii::t('ezform', 'Show only unreachable (Read-only)'), 2=> Yii::t('ezform', 'If the value is modified It will update both source and destination tables.'), 3=> Yii::t('ezform', 'Edit only the destination table.')]])?>
            </div>
	    
	    <?= Html::hiddenInput('options[data-type]', 'ref') ?>
            <?= Html::hiddenInput('options[ezf_field_id]', $model['ezf_field_id']) ?>
            <?= Html::hiddenInput('options[data-ezfid]', $model['ezf_id']) ?>
	</div>
    </div>

</div>