<?php

use yii\helpers\Html;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezbuilder\classes\EzBuilderFunc;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/**
 * _ezf_editor file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 18 ส.ค. 2559 12:50:33
 * @link http://www.appxq.com/
 */
?>

<!--<hr style="margin: 5px 0; border: 1px solid #333;">-->

<div class="row" id="ezf-box">
    <?php if(isset($modelFields)):?>
    <?php foreach ($modelFields as $key => $value):?>
    <?php
    $dataInput;
    if(isset(Yii::$app->session['ezf_input'])){
	$dataInput = EzfFunc::getInputByArray($value['ezf_field_type'], Yii::$app->session['ezf_input']);
    }
    if($dataInput){
	$inputWidget = Yii::createObject($dataInput['system_class']);
        try {
            $htmlInput = $inputWidget->generateViewInput($value);
        } catch (yii\base\Exception $e) {
            $htmlInput = '<code>'.$e->getMessage().'</code>';
        }
	echo EzBuilderFunc::createChildrenItem($value, $htmlInput);
    }
    
    ?>
    
    <?php endforeach; ?>
    <?php endif;?>
    <?php
    
    ?>
</div>

<div class="modal-footer">
    <div class="row">
	<div class="col-md-9">
	    <?=Html::button('<i class="glyphicon glyphicon-plus "></i> '. Yii::t('ezform', 'Add question'), ['class'=>'btn btn-success btn-block', 'id'=>'btn-add-question', 'data-url'=>Url::to(['/ezbuilder/ezform-fields/create', 'ezf_id'=>$ezf_id])])?>
	</div>
	<div class="col-md-3">
	    <?=Html::button('<i class="glyphicon glyphicon-resize-horizontal "></i> '.Yii::t('ezform', 'Add space'), ['class'=>'btn btn-default btn-block', 'id'=>'btn-add-space', 'data-url'=>Url::to(['/ezbuilder/ezform-fields/space', 'ezf_id'=>$ezf_id])])?>
	</div>
    </div>
</div>

<?php $this->registerJs(" 

$('#ezf-box').dad({
    draggable:'.draggable',
    callback:function(e){
	var positionArray = [];
	$('#ezf-box').find('.dads-children').each(function(){
	    positionArray.push($(this).attr('data-dad-id'));
	});
	
	$.post('".Url::to(['/ezbuilder/ezform-builder/order-update'])."',{position:positionArray},function(result){
	    
	});
    }
});

$('#ezf-box').on('click', '.btn-edit', function() {
    modalEzform($(this).attr('data-url'));
});

$('#ezf-box').on('click', '.btn-clone', function() {
    modalEzform($(this).attr('data-url'));
});

$('#ezf-box').on('click', '.btn-size', function() {
    var id = $(this).parent().parent().attr('data-dad-id');
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezbuilder/ezform-fields/resize'])."',
	data: {id:id, method:1},
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		$('.dads-children[data-dad-id=\"'+result.data+'\"]').removeClass('col-md-'+result.oldSize);	
		$('.dads-children[data-dad-id=\"'+result.data+'\"]').addClass('col-md-'+result.newSize);
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
});

$('#ezf-box').on('click', '.btn-size-small', function() {
    var id = $(this).parent().parent().attr('data-dad-id');
    $.ajax({
	method: 'POST',
	url:'".Url::to(['/ezbuilder/ezform-fields/resize'])."',
	data: {id:id, method:2},
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		$('.dads-children[data-dad-id=\"'+result.data+'\"]').removeClass('col-md-'+result.oldSize);	
		$('.dads-children[data-dad-id=\"'+result.data+'\"]').addClass('col-md-'+result.newSize);
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
});

$('#ezf-box').on('click', '.btn-delete', function() {
    var id = $(this).parent().parent().attr('data-dad-id');
    yii.confirm('".Yii::t('app', 'คุณต้องการลบคำถามนี้หรือไม่')."', function() {
	$.ajax({
	    method: 'POST',
	    url:'".Url::to(['/ezbuilder/ezform-fields/delete'])."',
	    data: {id:id},
	    dataType: 'JSON',
	    success: function(result, textStatus) {
		if(result.status == 'success') {
		    ". SDNoty::show('result.message', 'result.status') ."
		    $('.dads-children[data-dad-id=\"'+result.data+'\"]').hide('slow', function(){ $(this).remove(); });	
		} else {
		    ". SDNoty::show('result.message', 'result.status') ."
		}
	    }
	});
    });
    
});

$('#btn-add-question').on('click', function() {
    modalEzform($(this).attr('data-url'));
});

$('#btn-add-space').on('click', function() {
    $.ajax({
	method: 'POST',
	url:$(this).attr('data-url'),
	dataType: 'JSON',
	success: function(result, textStatus) {
	    if(result.status == 'success') {
		". SDNoty::show('result.message', 'result.status') ."
		$('#ezf-box').append(result.html);
		$('#ezf-box').removeClass('dads-children');	
	    } else {
		". SDNoty::show('result.message', 'result.status') ."
	    }
	}
    });
});

");
?>