<?php
use yii\helpers\Html;
/**
 * _filetype file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 22 ส.ค. 2559 12:33:08
 * @link http://www.appxq.com/
 */
?>
<div class="form-group">
    <button type="button" class="close btn-del-validations" aria-hidden="true">&times;</button>
    <div class="row">
	<div class="col-md-3 ">
	    <?= Html::label('Type', "validate[$row][0]", ['class' => 'control-label']) ?>
	    <?= Html::textInput("validate[$row][0]", 'file', ['class' => 'form-control', 'readonly'=>'readonly']) ?>
	</div>
	<div class="col-md-3 sdbox-col">
	    <?= Html::label('Max Size', "validate[$row][maxSize]", ['class' => 'control-label']) ?>
	    <?= Html::textInput("validate[$row][maxSize]", isset($data['maxSize'])?$data['maxSize']:'', ['class' => 'form-control', 'type' => 'number']) ?>
	</div>
	<div class="col-md-3 sdbox-col">
	    <?= Html::label('Types', "validate[$row][types][]", ['class' => 'control-label']) ?>
	    <div id="box-type-<?=$row?>">
		<?php
		//init data array
		if(isset($data['types']) && is_array($data['types'])){
		    foreach ($data['types'] as $key => $value) {
		?>
			<div class="input-group" style="margin-bottom: 5px;">
			    <input type="text" class="form-control" name="validate[<?=$row?>][types][]" value="<?=$value?>">
			    <span class="input-group-addon">
				<a class="btn-type-del" href="#" style="color: #ff0000;"><i class="glyphicon glyphicon-remove"></i></a>
			    </span>
			</div>
		<?php
		    }
		}
		?>
	    </div>
	    
	    <?= Html::button("<i class='glyphicon glyphicon-plus'></i>", ['class' => 'btn btn-success btn-add-type']) ?>
	</div>
    </div>

</div>

<?php  $this->registerJs("
$('.btn-add-type').click(function(){
    var input = '".Html::textInput("validate[$row][types][]", '', ['class' => 'form-control'])."';
    var btnDel = '".Html::a('<i class="glyphicon glyphicon-remove"></i>', '#', ['class'=>'btn-type-del','style'=>'color: #ff0000;'])."';
    var content = '<div class=\"input-group\" style=\"margin-bottom: 5px;\">'+input+'<span class=\"input-group-addon\" >'+btnDel+'</span></div>';
    
    $('#box-type-$row').append(content);
});

$('#box-type-$row').on('click', '.btn-type-del', function() {
    $(this).parent().parent().remove();
});

");?>