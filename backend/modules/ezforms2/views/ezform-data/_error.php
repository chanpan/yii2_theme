<?php
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Html;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= $modelEzf->ezf_name ?> <small><?= $modelEzf->ezf_detail ?></small></h3>
</div>
<div class="modal-body">
    <div class="alert alert-danger" role="alert"> <?=SDHtml::getMsgError()?> <?=$msg?></div>
</div>
<div class="modal-footer">
<?= Html::button(Yii::t('app', '<i class="glyphicon glyphicon-remove"></i> ปิด'), ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>    
</div>