<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;
use appxq\sdii\widgets\GridView;
use appxq\sdii\widgets\ModalForm;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;

/* @var $this yii\web\View */
/* @var $searchModel backend\modules\ezforms2\models\EzformSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ezforms');
?>
<div class="ezform-index">

    <div class="sdbox-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <?php
    $items = [
        [ 
            'label' => '<i class="glyphicon glyphicon-home"></i> '.Yii::t('ezform', 'My Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '1']),
            'encode'=>false,
            'active' => $tab == '1',
            'template' => '{update} {trash}'
        ],
        [
            'label' => '<i class="fa fa-users"></i> '.Yii::t('ezform', 'Co-Creator Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '2']),
            'encode'=>false,
            'active' => $tab == '2',
            'template' => '{update}'
        ],
        [
            'label' => '<i class="glyphicon glyphicon-globe"></i> '.Yii::t('ezform', 'Public Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '3']),
            'active' => $tab == '3',
            'encode'=>false,
            'template' => '{view}'
        ],
        [
            'label' => '<i class="glyphicon glyphicon-send"></i> '.Yii::t('ezform', 'Assign Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '4']),
            'active' => $tab == '4',
            'encode'=>false,
            'template' => '{insert} {view}'
        ],
        [
            'label' => '<i class="glyphicon glyphicon-star"></i> '.Yii::t('ezform', 'Favorite Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '5']),
            'active' => $tab == '5',
            'encode'=>false,
            'template' => '{view}'
        ],
        [
            'label' => '<i class="glyphicon glyphicon-trash"></i> '.Yii::t('ezform', 'Trash Forms'),
            'url' => yii\helpers\Url::to(['/ezforms2/ezform/index', 'tab' => '6']),
            'active' => $tab == '6',
            'encode'=>false,
            'template' => '{undo} {delete}'
        ],
    ];
    ?>
    <div class="pull-right" style="margin-top: 10px;">
        <?= Html::a('<i class="glyphicon glyphicon-import"></i> ' . Yii::t('ezform', 'Import'), ['/ezforms2/ezform/import'], ['class' => 'btn btn-info btn-flat']) ?>
    </div>
    
    <?=
    \yii\bootstrap\Nav::widget([
        'items' => $items,
        'options' => ['class' => 'nav nav-tabs', 'style' => 'margin: 10px 0px;'],
    ])
    ?>
    <?php Pjax::begin(['id' => 'ezform-grid-pjax', 'timeout' => FALSE]); ?>
    <?php
    echo GridView::widget([
        'id' => 'ezform-grid',
        'panelBtn' => $this->render('search', ['model' => $searchModel, 'tab' => $tab, 'userlist' => $userlist]),
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => ['style' => 'text-align: center;'],
                'contentOptions' => ['style' => 'width:60px;text-align: center;'],
            ],
            'ezf_name',
            [
                'attribute' => 'fullname',
                'label' => Yii::t('ezform', 'Created By')
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:d/m/Y']
            ],
            'ezf_detail:ntext',
            [
                'class' => 'backend\modules\ezforms2\classes\ActionColumn',
                'template' => $items[$tab - 1]['template'],
                'contentOptions' => ['style' => 'width:200px;text-align: center;'],
            ]
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>

<?=
ModalForm::widget([
    'id' => 'modal-ezform',
    'size' => 'modal-lg',
]);
?>

<?php $this->registerJs("
$('#ezform-grid-pjax').on('click', '#modal-addbtn-ezform,#modal-searchbtn-ezform', function() {
    modalEzform($(this).attr('data-url'));
});

$('#ezform-grid-pjax').on('dblclick', 'tbody tr', function() {
    var id = $(this).attr('data-key');
    location.href='" . Url::to(['/ezbuilder/ezform-builder/update', 'id' => '']) . "'+id;
});	

$('#ezform-grid-pjax').on('click', 'tbody tr td a', function() {
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');

    if(action === 'update' || action === 'view') {
	//modalEzform(url);
        location.href=url;
    } else if(action === 'delete') {
        var txtConfirm = $(this).attr('data-confirm');
	yii.confirm(txtConfirm, function() {
	    $.post(
		url
	    ).done(function(result) {
		if(result.status == 'success') {
		    " . SDNoty::show('result.message', 'result.status') . "
		    $.pjax.reload({container:'#ezform-grid-pjax',timeout: false});
		} else {
		    " . SDNoty::show('result.message', 'result.status') . "
		}
	    }).fail(function() {
		" . SDNoty::show("'" . SDHtml::getMsgError() . "Server Error'", '"error"') . "
		console.log('server error');
	    });
	});
    }
    return false;
});

function modalEzform(url) {
    $('#modal-ezform .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
    $('#modal-ezform').modal('show')
    .find('.modal-content')
    .load(url);
}

"); ?>