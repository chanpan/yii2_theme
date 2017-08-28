<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;
use backend\modules\ezforms2\classes\EzfHelper;

?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 class="modal-title" id="itemModalLabel"><?= $ezform->ezf_name ?> <small><?= $ezform->ezf_detail ?></small> 
        <?= EzfHelper::btnAdd($ezform->ezf_id, '', [], 'modal-divview-'.$ezform->ezf_id, 'modal-'.$ezform->ezf_id) ?></h3>
    
</div>
<div class="modal-body">
    <?= EzfHelper::uiGrid($ezform->ezf_id, '', 'modal-divview-'.$ezform->ezf_id, 'modal-'.$ezform->ezf_id, $data_column) ?>
</div>
<div class="modal-footer">
<?= Html::button('<i class="glyphicon glyphicon-remove"></i> '.Yii::t('app', 'Close'), ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>    
</div>

<?php
$sub_modal = '<div id="modal-'.$ezform->ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
        
$this->registerJs("
$('#ezf-modal-box').append('$sub_modal');    

$('.ezform-main-open').on('click', function(){
    var url = $(this).attr('data-url');
    modal_{$ezform->ezf_id}(url);
});
        
$('#modal-{$ezform->ezf_id}').on('hidden.bs.modal', function(e){
    var hasmodal = $('body .modal').hasClass('in');
    if(hasmodal){
        $('body').addClass('modal-open');
    }
});

        
function modal_{$ezform->ezf_id}(url) {
    $('#modal-{$ezform->ezf_id} .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-{$ezform->ezf_id}').modal('show')
    .find('.modal-content')
    .load(url);
}


");

?>