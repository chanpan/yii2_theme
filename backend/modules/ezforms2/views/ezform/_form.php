<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use kartik\tree\TreeViewInput;
use backend\modules\ezforms2\models\EzformTree;
use kartik\widgets\Select2;
use backend\modules\core\classes\CoreFunc;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Ezform */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="ezform-form">

    <?php
    $form = ActiveForm::begin([
                'id' => $model->formName()
                , 'action' => ($model->isNewRecord ? '' : Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]))
    ]);
    ?>
    <?php if ($model->isNewRecord) { ?>
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="itemModalLabel">Ezform</h4>
    </div>
    <?php } ?>
    <div class="modal-body">
        <div class='row'>
            <div class='col-md-6'>
                <?= $form->field($model, 'ezf_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class='col-md-6'>
                <?php
                if ($model->isNewRecord) {
                    $userName = Yii::$app->user->identity->profile->attributes;
                } else {
                    $userName = common\modules\user\models\User::findOne(['id' => $model->created_by])->profile->attributes;
                }
                echo $form->field($model, 'created_by')->textInput(['value' => $userName['firstname'] . ' ' . $userName['lastname'], 'disabled' => true]);
                ?>
            </div>
        </div>
        
        <div class="box box-primary" id="ezfBoxSet" >
            <div class="box-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#ezfSetTab1" aria-controls="ezfSetTab1" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'General settings')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab2" aria-controls="ezfSetTab2" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'Property')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab3" aria-controls="ezfSetTab3" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'Add code')?></a></li>
                    <li role="presentation"><a href="#ezfSetTab4" aria-controls="ezfSetTab4" role="tab" data-toggle="tab"><?= Yii::t('ezform', 'TDC Mapping')?></a></li>
                </ul>

                <!-- Tab panes -->
                <div class="panel-body">
                    <div class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="ezfSetTab1">
                            <div class='row'>
                                <div class='col-md-6'>
                                    <?=
                                    $form->field($model, 'category_id')->widget(TreeViewInput::classname(), [
                                        'name' => 'category_id',
                                        'id' => 'category_id',
                                        'query' => EzformTree::find()->where('readonly=1 or userid=' . Yii::$app->user->id . ' or id IN (select distinct root from ezform_tree where userid=' . Yii::$app->user->id . ')')->addOrderBy('root, lft'),
                                        'headingOptions' => ['label' => 'Categories'],
                                        'asDropdown' => true,
                                        'multiple' => false,
                                        'fontAwesome' => true,
                                        'rootOptions' => [
                                            'label' => '<i class="fa fa-home"></i> ',
                                            'class' => 'text-success',
                                            'options' => ['disabled' => false]
                                        ],
                                    ])
                                    ?>
                                </div>
                                <div class='col-md-6'>
                                    <?php
                                    $model->co_dev = SDUtility::string2Array($model->co_dev); // initial value
                                    echo $form->field($model, 'co_dev')->widget(Select2::className(), [
                                        'data' => $userlist,
                                        'options' => ['placeholder' => Yii::t('ezform', 'Co-creator'), 'multiple' => true, 'class' => 'form-control ezform-co_dev'],
                                        'pluginOptions' => [
                                            'tags' => true,
                                            'tokenSeparators' => [',', ' '],
                                        ],
                                    ]);
                                    ?>
                                </div>
                            </div>
                            <div class='row'>
                                <div class='col-md-6'>
                                    <?= $form->field($model, 'ezf_detail')->textarea(['rows' => 8]) ?>
                                </div>
                                <div class='col-md-6'>
                                    <?php
                                    if ($model->isNewRecord) {
                                        $model->shared = 0; //default value
                                    }
                                    echo $form->field($model, 'shared')->radioList([
                                        Yii::t('ezform', 'Private'),
                                        Yii::t('ezform', 'Public'),
                                        Yii::t('ezform', 'Assign to'),
                                        Yii::t('ezform', 'Everyone in site'),
                                    ]);
                                    ?>
                                    <?php
                                    $model->assign = SDUtility::string2Array($model->assign); // initial value
                                    echo $form->field($model, 'assign')->widget(Select2::className(), [
                                        'data' => $userlist,
                                        'options' => ['placeholder' => Yii::t('ezform', 'Assign to'), 'multiple' => true],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'tags' => true,
                                            'tokenSeparators' => [',', ' '],
                                        ],
                                    ])
                                    ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?php
                                    $model->field_detail = SDUtility::string2Array($model->field_detail); // initial value
                                    echo $form->field($model, 'field_detail')->widget(Select2::classname(), [
                                        'data' => ArrayHelper::map($modelFields, 'ezf_field_name', 'ezf_field_label'),
                                        'options' => ['placeholder' => Yii::t('ezform', 'Display fields'), 'multiple' => true],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'tags' => true,
                                            'tokenSeparators' => [',', ' '],
                                        ],
                                    ]);
                                    if ($model->isNewRecord) {
                                        echo Html::activeHiddenInput($model, 'status', ['value' => '1']);
                                    } else {
                                        echo $form->field($model, 'status')->checkbox(['value' => ($model->status == 1 ? '1' : '0')])->label(Yii::t('ezform', 'Enable'));
                                    }
                                    ?>
                                </div>
                                <div class="col-md-6">
                                    <label><?= Yii::t('ezform', 'Right to use form')?></label>
                                    <?= $form->field($model, 'public_listview')->checkbox(['value' => ($model->public_listview == 1 ? '1' : '0')]) ?>
                                    <?= $form->field($model, 'public_edit')->checkbox(['value' => ($model->public_edit == 1 ? '1' : '0')]) ?>
                                    <?= $form->field($model, 'public_delete')->checkbox(['value' => ($model->public_delete == 1 ? '1' : '0')]) ?>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="ezfSetTab2">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->field($model, 'query_tools')->radioList([
                                        '1' => Yii::t('ezform', 'Disable'), 
                                        '2' => Yii::t('ezform', 'Enable for check error and no error only to be submitted'), 
                                        '3' => Yii::t('ezform', 'Enable for submission always possible'),
                                        ]); ?>
                                </div>
                                <div class="col-md-4">
                                        <?= $form->field($model, 'unique_record')->radioList([
                                            '1'=>Yii::t('ezform', 'Disable'), 
                                            '2'=>Yii::t('ezform', 'Enable') . ' ('.Yii::t('ezform', 'Add only 1 Record').')', 
                                            '3'=>Yii::t('ezform', 'Enable') . ' ('.Yii::t('ezform', 'Summit only 1 Record').') ',
                                            ]); ?>
                                </div>
                                <div class="col-md-4">
                                        <?= $form->field($model, 'consult_tools')->radioList([
                                            '1' => Yii::t('ezform', 'Disable'), 
                                            '2' => Yii::t('ezform', 'Enable')
                                            ]);
                                        ?>
                                    <div id="consult_tools_setting">
                                        <?= $form->field($model, 'consult_telegram')->textInput()->label('Telegram group'); ?>
                                        <?=
                                        $form->field($model, 'consult_users')->widget(Select2::className(), [
                                            'data' => $userlist,
                                            'options' => ['placeholder' => Yii::t('ezform', 'Consult admin'), 'multiple' => true],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'tags' => true,
                                                'tokenSeparators' => [',', ' '],
                                            ]
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="ezfSetTab3">
                            <div class="row">
                                <div class="col-md-6 ">
                                    <?=
                                    $form->field($model, 'ezf_sql')->widget('appxq\sdii\widgets\AceEditor', [
                                        'mode' => 'mysql', // programing language mode. Default "html"
                                        'id' => 'ezf_sql'
                                    ]);
                                    ?>

                                </div>
                                <div class="col-md-6 sdbox-col">
                            <?=
                            $form->field($model, 'ezf_js')->widget('appxq\sdii\widgets\AceEditor', [
                                'mode' => 'javascript', // programing language mode. Default "html"
                                'id' => 'ezf_js'
                            ]);
                            ?>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="ezfSetTab4">
        <?= Html::activeHiddenInput($model, 'xsourcex') ?>
        <?= Html::activeHiddenInput($model, 'ezf_table') ?>
        <?= Html::activeHiddenInput($model, 'ezf_error') ?>
        <?= Html::activeHiddenInput($model, 'ezf_options') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?= Html::activeHiddenInput($model, 'ezf_version') ?>
    <?= Html::activeHiddenInput($model, 'ezf_id') ?>
    <?= Html::activeHiddenInput($model, 'updated_at') ?>
<?= Html::activeHiddenInput($model, 'updated_by') ?>
<?= Html::activeHiddenInput($model, 'created_at') ?>
<?= Html::activeHiddenInput($model, 'created_by') ?>
    </div>
    <?php if ($model->isNewRecord) { ?>
    <div class="modal-footer">
<?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
<?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>
    <?php } ?>
<?php ActiveForm::end(); ?>

</div>
<?php
$js = "";
if (!$model->isNewRecord) {
    $js = "$('form#{$model->formName()}').on('change', function(e) {
    var \$form = $(this);
    formSave(\$form.attr('action'));});";

    $js .= "ezf_js.on('blur', function() {
    var url = '" . \yii\helpers\Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]) . "';
     formSave(url);});";

    $js .= "ezf_sql.on('blur', function() {
    var url = '" . \yii\helpers\Url::to(['/ezforms2/ezform/update', 'id' => $model->ezf_id]) . "';
     formSave(url);});";
}
?>
<?php $this->registerJs("
" . $js . "    
$('form#{$model->formName()}').on('beforeSubmit', function(e) {
    var \$form = $(this);
    $.post(
	\$form.attr('action'), //serialize Yii2 form
	\$form.serialize()
    ).done(function(result) {
	if(result.status == 'success') {
	    " . SDNoty::show('result.message', 'result.status') . "
	    if(result.action == 'create') {
                $(document).find('#modal-ezform').modal('hide');
		$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
	    } else if(result.action == 'update') {
		$(document).find('#modal-ezform').modal('hide');
		$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
	    }
	} else {
	    " . SDNoty::show('result.message', 'result.status') . "
	} 
    }).fail(function() {
	" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
    return false;
});

function formSave(url){
 $.post(url,$('#Ezform').serialize()).done(function(result) {
	 " . SDNoty::show('result.message', 'result.status') . "	
         //$.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
    }).fail(function() {
	" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
	console.log('server error');
    });
}

$('.field-ezform-assign').addClass('" . ($model->shared <> '2' ? 'hidden' : '') . "');
$('#consult_tools_setting').addClass('" . ($model->consult_tools <> '2' ? 'hidden' : '') . "');
$(\"input[name='Ezform[consult_tools]']\").on('change',function(){
    var consult_tools = $(\"input[name='Ezform[consult_tools]']:checked\").val();
    if(consult_tools === '2'){
        $('#consult_tools_setting').removeClass('hidden');
    }else {
        $('#consult_tools_setting').addClass('hidden');
    }
});
$(\"input[name='Ezform[shared]']\").on('change',function(){
    var shared = $(\"input[name='Ezform[shared]']:checked\").val();
    if(shared === '2'){
        $('.field-ezform-assign').removeClass('hidden');
    }else {
        $('.field-ezform-assign').addClass('hidden');
    }
    });
"); ?>