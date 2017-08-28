<?php
use yii\helpers\Html;
use appxq\sdii\helpers\SDNoty;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Url;

$ezformAll = backend\modules\ezforms2\classes\EzfQuery::getEzformCoDevAll();
//$modelFields = \backend\modules\ezforms2\models\EzformFields::find()
//            ->where('ezf_id = :ezf_id', [':ezf_id' => $ezf_id])
//            ->orderBy(['ezf_field_order' => SORT_ASC])
//            ->all();

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['style'=>'text-align: center;'],
        'contentOptions' => ['style'=>'width:60px;text-align: center;'],
    ],
];
if(!$disabled){
   $columns[] = [
        'class' => 'appxq\sdii\widgets\ActionColumn',
        'contentOptions' => ['style'=>'width:110px;text-align: center;'],
        'template' => '{view} {update} {delete} ',
        'buttons'=>[
            'view' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $modal) {
                if(backend\modules\ezforms2\classes\EzfUiFunc::showViewDataEzf($data, Yii::$app->user->id, $data['user_create'])){
                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', 
                            Url::to(['/ezforms2/ezform-data/ezform-view', 
                                'ezf_id'=>$ezf_id, 
                                 'dataid'=>$data['data_id'],
                                 'modal'=>$modal,
                                'reloadDiv'=>$reloadDiv,
                            ]), 
                            [
                             'data-action' => 'update',
                             'title' => Yii::t('yii', 'View'),
                             'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
                             'class'=> 'btn btn-default btn-xs',
                     ]);
                }
            },
            'update' => function ($url, $data, $key) use($ezf_id, $reloadDiv, $modal) {
                if(backend\modules\ezforms2\classes\EzfUiFunc::showEditDataEzf($data, Yii::$app->user->id, $data['user_create'])){
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', 
                            Url::to(['/ezforms2/ezform-data/ezform', 
                                'ezf_id'=>$ezf_id, 
                                 'dataid'=>$data['data_id'],
                                 'modal'=>$modal,
                                'reloadDiv'=>$reloadDiv,
                            ]), 
                            [
                             'data-action' => 'update',
                             'title' => Yii::t('yii', 'Update'),
                             'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
                             'class'=> 'btn btn-primary btn-xs',
                     ]);
                }
            },
            'delete' => function ($url, $data, $key) use($ezf_id, $reloadDiv) {
                if(backend\modules\ezforms2\classes\EzfUiFunc::showDeleteDataEzf($data, Yii::$app->user->id, $data['user_create'])){
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', 
                            Url::to(['/ezforms2/ezform-data/delete', 
                                 'ezf_id'=>$ezf_id, 
                                 'dataid'=>$data['data_id'],
                                 'reloadDiv'=>$reloadDiv,
                            ]),  
                            [
                             'data-action' => 'delete',
                             'title' => Yii::t('yii', 'Delete'),
                             'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                             'data-method' => 'post',
                             'data-pjax' => isset($this->pjax_id)?$this->pjax_id:'0',
                             'class'=> 'btn btn-danger btn-xs',
                     ]);
                }
            },
        ],
    ];
}            
$columns[] = [
            'attribute'=>'ezf_id',
            'value'=>function ($data){
                return $data['ezf_name'];
                
            },
            'contentOptions'=>['style'=>'width:300px;'],
            'filter'=> Html::activeDropDownList($searchModel, 'ezf_id', yii\helpers\ArrayHelper ::map($ezformAll, 'ezf_id', 'ezf_name'), ['class'=>'form-control', 'prompt'=>'All']),
    ];
$columns[] = [
            'attribute'=>'ezf_detail',
            'format'=>'raw',
            'value'=>function ($data){
                $detail = appxq\sdii\utils\SDUtility::string2Array($data['ezf_detail']);
                $concat = '';
                $comma = '';
                if(is_array($detail)){
                    foreach ($detail as $value) {
                        $concat .= $comma.$value;
                        $comma = ", ' ', ";
                    }
                }
                
                if($concat!=''){
                    $select = "CONCAT($concat) AS ezf_detail";
                    $query = new \yii\db\Query();
                    $query->select([$select]);
                    $query->from($data['ezf_table']);
                    $query->where('id=:id', [':id'=>$data['data_id']]);
                    
                    return $query->createCommand()->queryScalar();
                }
                
                return '';
                
            },
            'filter'=>'',
    ];
$columns[] = [
            'attribute'=>'xsourcex',
            'format'=>'raw',
            'value'=>function ($data){
                return "<span class=\"label label-success\" data-toggle=\"tooltip\" title=\"{$data['sitename']}\">{$data['xsourcex']}</span>";
            },
            'headerOptions'=>['style'=>'text-align: center;'],
            'contentOptions'=>['style'=>'width:100px;text-align: center;'],
    ];
$columns[] = [
            'attribute'=>'update_date',
            'value'=>function ($data){return !empty($data['update_date'])?\appxq\sdii\utils\SDdate::mysql2phpThDateSmall($data['update_date']):'';},
            'headerOptions'=>['style'=>'text-align: center;'],
            'contentOptions'=>['style'=>'width:100px;text-align: center;'],
            'filter'=>'',
    ];
$columns[] = [
            'attribute'=>'userby',
            'contentOptions'=>['style'=>'width:200px;'],
            'filter'=>'',
    ];            
$columns[] = [
            'attribute'=>'rstat',
            'format'=>'raw',
            'value'=>function ($data){
                $alert = 'label-default';
                if($data['rstat']==0){
                    $alert = 'label-info';
                } elseif($data['rstat']==1){
                    $alert = 'label-warning';
                } elseif($data['rstat']==2){
                    $alert = 'label-success';
                } elseif($data['rstat']==3){
                    $alert = 'label-danger';
                } 
                
                $rstat = backend\modules\core\classes\CoreFunc::itemAlias('rstat', $data['rstat']);
                return "<h4 style=\"margin: 0;\"><span class=\"label $alert\">$rstat</span></h4>";
            },
            'headerOptions'=>['style'=>'text-align: center;'],
            'contentOptions'=>['style'=>'width:120px;text-align: center;'],
            'filter'=> Html::activeDropDownList($searchModel, 'rstat', backend\modules\core\classes\CoreFunc::itemAlias('rstat'), ['class'=>'form-control', 'prompt'=>'All']),
    ];

?>

<?= yii\grid\GridView::widget([
	'id' => "$reloadDiv-emr-grid",
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>
<?php
//$sub_modal = '<div id="modal-'.$ezf_id.'" class="fade modal" role="dialog"><div class="modal-dialog modal-xxl"><div class="modal-content"></div></div></div>';
    
$this->registerJs("

$('#$reloadDiv-emr-grid tbody tr td a').on('click', function() {
    
    var url = $(this).attr('href');
    var action = $(this).attr('data-action');
    
    if(action === 'update' || action === 'create'){
        $('#$modal .modal-content').html('<div class=\"sdloader \"><i class=\"sdloader-icon\"></i></div>');
        $('#$modal').modal('show')
        .find('.modal-content')
        .load(url);
    } else if(action === 'delete') {
        yii.confirm('".Yii::t('app', 'Are you sure you want to delete this item?')."', function(){
                $.post(
                        url, {'_csrf':'".Yii::$app->request->getCsrfToken()."'}
                ).done(function(result){
                        if(result.status == 'success'){
                                ". SDNoty::show('result.message', 'result.status') ."
                            var urlreload =  $('#$reloadDiv').attr('data-url');        
                            getUiAjax(urlreload, '$reloadDiv');          
                        } else {
                                ". SDNoty::show('result.message', 'result.status') ."
                        }
                }).fail(function(){
                        ". SDNoty::show("'" . "Server Error'", '"error"') ."
                        console.log('server error');
                });
        });
    }
    return false;
});

$('#$reloadDiv-emr-grid').on('beforeFilter', function(e) {
    var \$form = $(this).find('form');
    $.ajax({
	method: 'GET',
	url: \$form.attr('action'),
        data: \$form.serialize(),
	dataType: 'HTML',
	success: function(result, textStatus) {
	    $('#$reloadDiv').html(result);
	}
    });
    return false;
});

$('#$reloadDiv-emr-grid .pagination a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

$('#$reloadDiv-emr-grid thead tr th a').on('click', function() {
    getUiAjax($(this).attr('href'), '$reloadDiv');
    return false;
});

function getUiAjax(url, divid) {
    $.ajax({
        method: 'POST',
        url: url,
        dataType: 'HTML',
        success: function(result, textStatus) {
            $('#'+divid).html(result);
        }
    });
}

");

?>