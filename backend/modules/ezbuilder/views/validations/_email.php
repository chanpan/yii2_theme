<?php
use yii\helpers\Html;
/**
 * _email file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 22 ส.ค. 2559 12:33:42
 * @link http://www.appxq.com/
 */
?>
<div class="form-group">
    <button type="button" class="close btn-del-validations" aria-hidden="true">&times;</button>
    <div class="row">
	<div class="col-md-3 ">
	    <?= Html::label('Type', "validate[$row][0]", ['class' => 'control-label']) ?>
	    <?= Html::textInput("validate[$row][0]", 'email', ['class' => 'form-control', 'readonly'=>'readonly']) ?>
	</div>
	
    </div>

</div>