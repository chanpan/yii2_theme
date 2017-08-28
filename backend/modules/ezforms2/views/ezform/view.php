<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\modules\ezforms2\models\Ezform */

$this->title = 'Ezform#'.$model->ezf_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ezforms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ezform-view">

    <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h4 class="modal-title" id="itemModalLabel"><?= Html::encode($this->title) ?></h4>
    </div>
    <div class="modal-body">
        <?= DetailView::widget([
	    'model' => $model,
	    'attributes' => [
		'ezf_id',
		'ezf_version',
		'ezf_name',
		'ezf_detail:ntext',
		'xsourcex',
		'ezf_table',
		'created_by',
		'created_at',
		'updated_by',
		'updated_at',
		'status',
		'shared',
		'public_listview',
		'public_edit',
		'public_delete',
		'co_dev:ntext',
		'assign:ntext',
		'category_id',
		'field_detail:ntext',
		'ezf_sql:ntext',
		'ezf_js:ntext',
		'ezf_error:ntext',
		'query_tools',
		'unique_record',
		'consult_tools',
		'consult_users:ntext',
		'consult_telegram',
		'ezf_options:ntext',
	    ],
	]) ?>
    </div>
</div>
