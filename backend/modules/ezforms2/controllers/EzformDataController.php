<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfForm;
use backend\modules\ezforms2\classes\EzfUiFunc;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\models\TbdataAll;
use backend\modules\ezforms2\models\EzformTarget;
use kartik\mpdf\Pdf;

/**
 * Description of EzformDataController
 *
 * @author appxq
 */
class EzformDataController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionDelete($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';

            $modelEzf = EzfQuery::getEzformOne($ezf_id);

            $modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();

            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], 0);

            $result = EzfUiFunc::deleteDataRstat($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $dataid, $reloadDiv);
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }
    
    public function actionDeleteData($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';

            $modelEzf = EzfQuery::getEzformOne($ezf_id);

            $modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();

            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], 0);

            $result = EzfUiFunc::deleteData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $dataid, $reloadDiv);
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionEzform($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];
            $disable = [];
            
            $modelEzf = EzfQuery::getEzformOne($ezf_id);

            Yii::$app->session['show_varname']=0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;

            $modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
            
            if (!$model) {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
                return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $dataid,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('app', 'No results found.'),
                ]);
            }
            
            
            
            $targetReset = false;
            if (!isset($model->id)) {// ถ้ามี new record ที่คนนั้นสร้างไว้ ให้ใช้อันนั้น
                $modelNewRecord = EzfUiFunc::loadNewRecord($model, $modelEzf->ezf_table, $userProfile->user_id);
                
                if($modelNewRecord){
                    $targetReset = true;
                    $model = $modelNewRecord;
                }
            }

            if (!empty($initdata)) {//กำหนดค่าเริ่มต้น
                $model->attributes = $initdata;
                $initdata = NULL;
            }
            
            //ขั้นตอนกรอกข้อมูลสำคัญ
            $evenFields = EzfFunc::getEvenField($modelFields);
            $special = isset($evenFields['special']) && !empty($evenFields['special']);
            
            if (isset($evenFields['target']) && !empty($evenFields['target'])) { //มีเป้าหมาย
                if($targetReset){
                    $model[$evenFields['target']['ezf_field_name']] = '';
                }
                
                $modelEzfTarget = EzfQuery::getEzformOne($evenFields['target']['ref_ezf_id']);
                $target = ($target == '') ? $model[$evenFields['target']['ezf_field_name']] : $target;
                $dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);
                
                $disable[$evenFields['target']['ezf_field_name']] = 1;
                
                if ($dataTarget) {//เลือกเป้าหมายแล้ว
                    
                    if(isset($modelEzf['unique_record']) && $modelEzf['unique_record']==2){
                        $unique = EzfUiFunc::loadUniqueRecord($model, $modelEzf->ezf_table, $target);
                        //\appxq\sdii\utils\VarDumper::dump($unique);
                        if($unique){
                            return $this->renderAjax('_error', [
                                        'ezf_id' => $ezf_id,
                                        'dataid' => $model->id,
                                        'modelEzf' => $modelEzf,
                                        'msg' => Yii::t('ezform', 'This form only records 1 record.'),
                            ]);
                        }
                    }
                    
                    //เพิ่มและแก้ไขข้อมูล system
                    $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, $evenFields['target']['ezf_field_name'], '', $special, $userProfile, 0);
                    
                } else { //ฟอร์มค้นหาเป้าหมาย
                    $targetFields = [$evenFields['target']];
                    return $this->renderAjax('_ezform_target', [//ขั้นตอนการเลือกเป้าหมาย
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'modelFields' => $targetFields,
                                'model' => $model,
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                                'initdata' => $initdata,
                                'type' => 1,
                    ]);
                }
            } else {// ไม่มีเป้าหมาย
                
                $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset); 
                
                if (!isset($fieldSpecial)) {
                    $specialFields = [$evenFields['special']];
                    
                    return $this->renderAjax('_ezform_target', [ //ตรวจสอบ คำถามพิเศษ
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'modelFields' => $specialFields,
                                'model' => $model,
                                'modal' => $modal,
                                'reloadDiv' => $reloadDiv,
                                'initdata' => $initdata,
                                'type' => 2,
                    ]);
                }

                if ($model->id) {
                    $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
                } else {
                    $dataTarget = [];
                }
                
                if(isset($evenFields['special']['ezf_field_name'])){
                    $disable[$evenFields['special']['ezf_field_name']] = 1;
                }
                
                //เพิ่มและแก้ไขข้อมูล system
                $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, 0);
            }
            
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                
                $rstat = Yii::$app->request->post('submit')?Yii::$app->request->post('submit'):$model->rstat;
                
                $model->rstat = $rstat;
                $model->user_update = $userProfile->user_id;
                $model->update_date = new \yii\db\Expression('NOW()');
                
                $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);

                return $result;
            }

            return $this->renderAjax('_ezform', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'modelEzf' => $modelEzf,
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $initdata,
                        'disable'=>$disable
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEzformView($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];

            $modelEzf = EzfQuery::getEzformOne($ezf_id);

            Yii::$app->session['show_varname']=0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;

            $modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

            if (!$model || $dataid=='') {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
                return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('app', 'No results found.'),
                ]);
            }

            return $this->renderAjax('_ezform-view', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'modelEzf' => $modelEzf,
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $initdata,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEzformPrint($ezf_id) {
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $initdata = isset($_GET['initdata']) ? EzfFunc::stringDecode2Array($_GET['initdata']) : [];

            $modelEzf = EzfQuery::getEzformOne($ezf_id);

            Yii::$app->session['show_varname']=0;
            Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
            //Yii::$app->session['ezform'] = $modelEzf->attributes;

            $userProfile = Yii::$app->user->identity->profile;

            $modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();

            $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
            $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

            if (!$model || $dataid=='') {// dataid ส่งมาผิดหาไม่เจอ / ไม่คิดรวมถ้าส่ง '' มา
                return $this->renderAjax('_error', [
                                'ezf_id' => $ezf_id,
                                'dataid' => $model->id,
                                'modelEzf' => $modelEzf,
                                'msg' => Yii::t('app', 'No results found.'),
                ]);
            }

            $content = $this->renderPartial('_ezform-view', [
                        'ezf_id' => $ezf_id,
                        'dataid' => $model->id,
                        'modelEzf' => $modelEzf,
                        'modelFields' => $modelFields,
                        'model' => $model,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'initdata' => $initdata,
            ]);
       
            // setup kartik\mpdf\Pdf component
            $pdf = new Pdf([
                // set to use core fonts only
                'mode' => Pdf::MODE_UTF8, 
                // A4 paper format
                'format' => Pdf::FORMAT_A4, 
                // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT, 
                // stream to browser inline
                'destination' => Pdf::DEST_BROWSER, 
                // your html content input
                'content' => $content,  
                // format content from your own css file if needed or use the
                // enhanced bootstrap css built by Krajee for mPDF formatting 
                'cssFile' => '@backend/web/css/pdf.css', //'@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
                // any css to be embedded if required
                'cssInline' => '.kv-heading-1{font-size:18px}', 
                 // set mPDF properties on the fly
                'options' => ['title' => $modelEzf->ezf_name],
                 // call mPDF methods on the fly
                'methods' => [ 
                    'SetHeader'=>[$modelEzf->ezf_name], 
                    'SetFooter'=>['{PAGENO}'],
                ]
            ]);

            // return the pdf output as per the destination setting
            return $pdf->render(); 
    }

    public function actionView($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $data_column = isset($_GET['data_column'])?$_GET['data_column']:'';
            $disabled = isset($_GET['disabled'])?$_GET['disabled']:0;
            
            $data_column = EzfFunc::stringDecode2Array($data_column);
            
            $ezform = EzfQuery::getEzformOne($ezf_id);

            $searchModel = new TbdataAll();
            $searchModel->setTableName($ezform->ezf_table);
            
            $targetField = EzfQuery::getTargetOne($ezform->ezf_id);
            if($targetField && $target!=''){
                $searchModel[$targetField['ezf_field_name']] = $target;
            }
            
            $dataProvider = EzfUiFunc::modelSearch($searchModel, $ezform, $targetField, $data_column, Yii::$app->request->queryParams);

            $view = $popup ? '_view-popup' : '_view';
            
            return $this->renderAjax($view, [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezform' => $ezform,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'data_column' => $data_column,
                        'target' => $target,
                        'disabled' => $disabled,
            ]);
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }
    
    public function actionEmr($ezf_id) {
        if (Yii::$app->getRequest()->isAjax) {
            $popup = isset($_GET['popup']) ? $_GET['popup'] : 0;
            $showall = isset($_GET['showall']) ? $_GET['showall'] : 0;
            $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
            $reloadDiv = isset($_GET['reloadDiv']) ? $_GET['reloadDiv'] : '';
            $target = isset($_GET['target']) ? $_GET['target'] : '';
            $disabled = isset($_GET['disabled'])?$_GET['disabled']:0;
            
            $searchModel = new EzformTarget();
            $dataProvider = EzfUiFunc::modelEmrSearch($searchModel, $target, $ezf_id, Yii::$app->request->queryParams);
            
            $view = $popup ? '_emr-popup' : '_emr';

            return $this->renderAjax($view, [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'ezf_id' => $ezf_id,
                        'modal' => $modal,
                        'reloadDiv' => $reloadDiv,
                        'target' => $target,
                        'showall' => $showall,
                        'disabled' => $disabled,
            ]);
            
        } else {
            throw new NotFoundHttpException(Yii::t('ezform', 'Do not allow this way.'));
        }
    }

}
