<?php

use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfStarterWidget;
use backend\modules\ezforms2\classes\EzfHelper;

$this->title = 'Demo';
$this->params['breadcrumbs'][] = Yii::t('backend', 'Demo');

?>

<?php EzfStarterWidget::begin(); ?>
<div class="modal-header">
    <h3 class="modal-title" id="itemModalLabel">Demo <small> ตัวอย่างการใช้งาน widget Ezform</small></h3>
</div>
<div class="modal-body">

    <div class="panel panel-default" >
        <div class="panel-heading">
            <h3 class="panel-title">ทดสอบปุ่ม</h3>
        </div>
        <div class="panel-body">
            <?= EzfHelper::btnAdd('1502520831007837200') ?>
            <?= EzfHelper::btnEdit('1500524459015751100', '1502030148023740900', ['var_3'=>111], 'form-test') ?>
            <?= EzfHelper::btnDelete('1500524459015751100', '1502030148023740900', 'form-test') ?>
            <?= EzfHelper::btnViewForm('5555', '1502546608030350600') ?>
            <?= EzfHelper::btnView('1502520831007837200') ?>
            
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">ทดสอบการแสดง Grid test <?= EzfHelper::btnAdd('1500524459015751100', '', [], 'form-test') ?></h3>
                </div>
                <div class="panel-body">
                    <?= EzfHelper::uiGrid('1500524459015751100', '', 'form-test', 'modal-ezform-main', ['id', [
		'attribute'=>'var_8',
		'headerOptions'=>['style'=>'text-align: center;'],
		'contentOptions'=>['style'=>'width:100px; text-align: center;'],
],
                        ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">ทดสอบการแสดง Grid test2 <?= EzfHelper::btnAdd('5555', '', [], 'form-test2') ?></h3>
                </div>
                <div class="panel-body">
                    <?= EzfHelper::uiGrid('5555', '', 'form-test2') ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">ทดสอบการแสดง Grid test3 <?= EzfHelper::btnAdd('1502354328075785000', '', [], 'form-test3') ?></h3>
                </div>
                <div class="panel-body">
                    <?= EzfHelper::uiGrid('1502354328075785000', '', 'form-test3', 'modal-ezform-main', ['id', 'rstat', 'var_7']) ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-12 ">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">ทดสอบการแสดง EMR test3 <?= EzfHelper::btnAdd('1502354328075785000', '', [], 'form-test3-emr') ?></h3>
                </div>
                <div class="panel-body">
                    <?= EzfHelper::uiEmr('1502354328075785000', '', 'form-test3-emr') ?>
                </div>
            </div>
        </div>
    </div>

    <?php

    $this->registerJs("
           
        ");
    ?>
</div>
<?php EzfStarterWidget::end(); ?>
