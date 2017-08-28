<?php
use yii\helpers\Html;
/**
 * _number file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 22 ส.ค. 2559 12:32:41
 * @link http://www.appxq.com/
 */
?>
<div class="form-group">
    <button type="button" class="close btn-del-validations" aria-hidden="true">&times;</button>
    <div class="row">
	<div class="col-md-3 ">
	    <?= Html::label('Type', "validate[$row][0]", ['class' => 'control-label']) ?>
	    <?= Html::textInput("validate[$row][0]", 'number', ['class' => 'form-control', 'readonly'=>'readonly']) ?>
	</div>
	<div class="col-md-3 sdbox-col">
	    <?= Html::label('Min', "validate[$row][min]", ['class' => 'control-label']) ?>
	    <?= Html::textInput("validate[$row][min]", isset($data['min'])?$data['min']:'', ['class' => 'form-control', 'type' => 'number']) ?>
	</div>
	<div class="col-md-3 sdbox-col">
	    <?= Html::label('Max', "validate[$row][max]", ['class' => 'control-label']) ?>
	    <?= Html::textInput("validate[$row][max]", isset($data['max'])?$data['max']:'', ['class' => 'form-control', 'type' => 'number']) ?>
	</div>
    </div>

</div>