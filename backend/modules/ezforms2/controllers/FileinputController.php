<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use appxq\sdii\helpers\SDHtml;
use yii\helpers\Html;
use yii\helpers\Url;


/**
 * Select2Controller implements the CRUD actions for EzformInput model.
 */
class FileinputController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    public function actionDelete($id) {
        
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = \backend\modules\ezforms2\models\EzformUpload::findOne($id);
            $file_name = $model['file_name'];
            if ($model->delete()) {
                    @unlink(Yii::getAlias('@storage/ezform/fileinput/') . $file_name);
                    $result = [
                            'status' => 'success',
                            'action' => 'update',
                            'message' =>  Yii::t('app', 'Deleted completed.'),
                            'data' => $id,
                    ];
                    return $result;
            } else {
                    $result = [
                            'status' => 'error',
                            'message' => Yii::t('app', 'Can not delete the data.'),
                            'data' => $id,
                    ];
                    return $result;
            }
        } else {
                throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionGridUpdate() {
        
        if (Yii::$app->getRequest()->isAjax) {
            $ezf_id = isset($_GET['ezf_id'])?$_GET['ezf_id']:0;
            $ezf_field_id = isset($_GET['ezf_field_id'])?$_GET['ezf_field_id']:0;
            $dataid = isset($_GET['dataid'])?$_GET['dataid']:0;
            
            $fileModel = new \backend\modules\ezforms2\models\EzformUploadSearch();
            $fileModel->ezf_id = $ezf_id;
            $fileModel->ezf_field_id = $ezf_field_id;
            $fileModel->tbid = $dataid;

            $dataProvider = $fileModel->search(Yii::$app->request->queryParams);
            
            $html = '';
            if(isset($dataProvider->models[0])){
                if($dataProvider->models[0]->ezf_id) {
                        $ezform = \backend\modules\ezforms2\classes\EzfQuery::getFormTableName($dataProvider->models[0]->ezf_id);
                        $res = Yii::$app->db->createCommand("select rstat, xsourcex from `" . $ezform->ezf_table . "` where id = :id;", [':id' => $dataProvider->models[0]->tbid])->queryOne();
                        //$comp = \backend\modules\ezforms2\components\EzformQuery::checkIsTableComponent($dataProvider->models[0]->ezf_id);

                    if($res['xsourcex'] == Yii::$app->user->identity->profile->sitecode){
                            $visibleDel = true;
                    }else{
                            $visibleDel = false;
                    }
                }

                $html = \yii\grid\GridView::widget([
                    'id' => 'research-grid',
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    'columns' => [
                        //['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute'=>'file_name',
                            'header'=>'',
                            'value'=>function ($data){
                                $img = Yii::getAlias('@storageUrl/ezform/fileinput').'/'.$data['file_name'];
                                $img_old = $img;
                                $ext = strtolower(pathinfo($data['file_name'], PATHINFO_EXTENSION));
                                if ($ext == 'pdf') {
                                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/pdf_icon.png';
                                } elseif (in_array($ext, ['doc', 'docx'])) {
                                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/doc_icon.png';
                                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/xls_icon.png';
                                } elseif (in_array($ext, ['ppt', 'pptx'])) {
                                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/xls_icon.png';
                                } elseif (in_array($ext, ['png','jpg','jpeg'])) {

                                } else {
                                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/unknow_icon.png';
                                }
                                return '<img class="file-preview-image" src="'.$img.'" data-filename="'.$img_old.'" height="100">';
                            },
                            'format' => 'raw',	    
                            'headerOptions'=>['style'=>'text-align: center;'],
                            'contentOptions'=>['style'=>'width:100px;text-align: center;'],
                        ],
                        'file_name_old:ntext',
                        [
                            'attribute'=>'created_at',
                            'value'=>function ($data){return \appxq\sdii\utils\SDdate::mysql2phpDate($data['created_at']);},
                            'headerOptions'=>['style'=>'text-align: center;'],
                            'contentOptions'=>['style'=>'width:100px;text-align: center;'],
                            'filter'=>'',
                        ],
                        [
                            'class' => 'appxq\sdii\widgets\ActionColumn',
                            'template'=>'{delete}',
                            'visible' => $visibleDel,
                            'contentOptions'=>['style'=>'width:50px;text-align: center;'],
                            'buttons'=>[
                                'delete' => function ($url, $data, $key) {
                                    //if(Yii::$app->user->id==$data['created_by']){
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/ezforms2/fileinput/delete', 'id'=>$data['fid']]), [
                                            'data-action' => 'delete',
                                            'title' => Yii::t('yii', 'Delete'),
                                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'data-method' => 'post',
                                        ]);
                                    //}
                                },
                            ],
                        ],
                    ], 
                ]);
            }
            return $html;
        } else {
                throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public static function initial($model, $modelFields) {
        $fileImg = $model[$modelFields['ezf_field_name']];
        $imgPath = Yii::getAlias('@storageUrl/ezform/fileinput/'); //Yii::getAlias('@backendUrl') . '/fileinput/';
        $initialPreview = [];

        if (isset($fileImg) && !empty($fileImg)) {
            $active = false;
            $items = [];
            //file_active = 0 new upload
            //file_active = 1 file confirm
            //file_active = 9 file waiting for approve
            //file_active = -9 file disable (query tools)
            //file_active = -10 file disable (input data)
            $fileItemActive = \backend\modules\ezforms2\models\EzformUpload::find()->where('tbid=:tbid and ezf_field_id=:ezf_field_id and ezf_id=:ezf_id and mode = :mode AND file_active =1', [':tbid' => $model->id, ':ezf_id' => $modelFields->ezf_id, ':ezf_field_id' => $modelFields->ezf_field_id, ':mode' => 1])->orderBy('created_at desc')->one();
            if ($fileItemActive) {
                $items[] = $fileItemActive['file_name'];
                $active = true;
            } else {
                $fileItem = \backend\modules\ezforms2\models\EzformUpload::find()->where('tbid=:tbid and ezf_field_id=:ezf_field_id and ezf_id=:ezf_id and mode = :mode', [':tbid' => $model->id, ':ezf_id' => $modelFields->ezf_id, ':ezf_field_id' => $modelFields->ezf_field_id, ':mode' => 1])->orderBy('created_at desc')->one();
                if ($fileItem) {
                    $items[] = $fileItem['file_name'];
                }
            }

            foreach ($items as $item) {
                $img = $imgPath . $item;
                $img_old = $img;
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                if ($ext == 'pdf') {
                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/pdf_icon.png';
                } elseif (in_array($ext, ['doc', 'docx'])) {
                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/doc_icon.png';
                } elseif (in_array($ext, ['xls', 'xlsx'])) {
                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/xls_icon.png';
                } elseif (in_array($ext, ['ppt', 'pptx'])) {
                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/xls_icon.png';
                } elseif (in_array($ext, ['png','jpg','jpeg'])) {
                    
                } else {
                    $img = Yii::getAlias('@storageUrl') . '/ezform/img/unknow_icon.png';
                }
                
                list($width, $height, $type, $attr) = getimagesize($img);
                $height = $height>=245?245:$height;
                
                $initialPreview[] = \yii\helpers\Html::img($img, ['data-filename' => $img_old, 'height'=>$height.'px', 'class' => 'file-preview-image kv-preview-data rotate-1', 'alt' => $modelFields->ezf_field_label, 'title' => $modelFields->ezf_field_label]);
            }
        }

        return $initialPreview;
    }
}
