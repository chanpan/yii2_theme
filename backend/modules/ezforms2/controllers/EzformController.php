<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfForm;
use yii\web\UploadedFile;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\EzformCoDev;
use backend\modules\ezforms2\models\EzformChoice;
use backend\modules\ezforms2\models\EzformAssign;
use backend\modules\ezforms2\models\EzformCondition;

/**
 * EzformController implements the CRUD actions for Ezform model.
 */
class EzformController extends Controller {

    public function behaviors() {
        return [
            /* 	    'access' => [
              'class' => AccessControl::className(),
              'rules' => [
              [
              'allow' => true,
              'actions' => ['index', 'view'],
              'roles' => ['?', '@'],
              ],
              [
              'allow' => true,
              'actions' => ['view', 'create', 'update', 'delete', 'deletes'],
              'roles' => ['@'],
              ],
              ],
              ], */
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function beforeAction($action) {
        if (parent::beforeAction($action)) {
            if (in_array($action->id, array('create', 'update'))) {
                
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Lists all Ezform models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new EzformSearch();
        $tab = (Yii::$app->request->get('tab') ? Yii::$app->request->get('tab') : '1');
        $dataProvider = $searchModel->searchMyForm(Yii::$app->request->queryParams, $tab);
        $userlist = ArrayHelper::map(EzfQuery::getIntUserAll(), 'id', 'text');
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider, 'tab' => $tab, 'userlist' => $userlist
        ]);
    }

    /**
     * Displays a single Ezform model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'view');
        if (Yii::$app->getRequest()->isAjax) {
            return $this->renderAjax('view', [
                        'model' => $model,
            ]);
        } else {
            return $this->render('view', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Ezform model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        if (Yii::$app->getRequest()->isAjax) {
            $model = new Ezform();
            $model->ezf_id = SDUtility::getMillisecTime();
            $model->ezf_version = 'v2';
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $result = EzfForm::saveEzfForm($model);
                return $result;
            } else {
                return $this->renderAjax('create', $this->dataRender($model));
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    /**
     * Updates an existing Ezform model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'update');
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = EzfForm::saveEzfForm($model);
            return $result;
        } else {
            return $this->renderAjax('update', $this->dataRender($model));
        }
    }

    private function dataRender($model) {
        $userlist = ArrayHelper::map(EzfQuery::getIntUserAll(), 'id', 'text');
        $modelFields = EzformFields::find()
                ->select(['ezf_field_name', 'ezf_field_label'])
                ->where('ezf_id = :ezf_id', [':ezf_id' => $model->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();
        return [
            'model' => $model, 'userlist' => $userlist, 'modelFields' => $modelFields
        ];
    }

    /**
     * Deletes an existing Ezform model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionTrash($id) {
        $model = $this->findModel($id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'delete');
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = EzfForm::trashEzfForm($model);
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDelete($id) {
        $model = $this->findModel($id);
        EzfForm::checkEzfFormRight($model->ezf_id, Yii::$app->user->id, 'delete');
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $result = EzfForm::deleteEzfForm($model);
            return $result;
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionDeletes() {
        if (Yii::$app->getRequest()->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (isset($_POST['selection'])) {
                foreach ($_POST['selection'] as $id) {
                    $model = $this->findModel($id);
                    $result = EzfForm::deleteEzfForm($model);
                    if ($result['status'] == 'error') {
                        break;
                    }
                }

                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                    'data' => $id,
                ];
                return $result;
            }
        } else {
            throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
        }
    }

    public function actionImport() {
        $sum = [];

        if (isset($_FILES['excel_file']['name']) && $_FILES['excel_file']['name'] != '') {
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', '256M');

            $excel_file = UploadedFile::getInstanceByName('excel_file');

            if ($excel_file) {
                $data = \moonland\phpexcel\Excel::import($excel_file->tempName, [
                            'setFirstRecordAsKeys' => true,
                            'setIndexSheetByName' => true,
                                //'getOnlySheet' => 'sheet1',
                ]);

                $ezfError=1;
                $ezf_table = '';
                if (isset($data['Ezform']) && !empty($data['Ezform'])) {

                    $sum['Ezform']['all'] = 0;
                    $sum['Ezform']['tsum'] = 0;
                    $sum['Ezform']['fsum'] = 0;
                    $sum['Ezform']['esum'] = 0;
                    foreach ($data['Ezform'] as $value) {
                        try {
                            $sum['Ezform']['all']++;
                            $modelEzform = new Ezform();
                            $modelEzform->attributes = $value;
                            if ($modelEzform->save()) {
                                $ezf_table = $modelEzform->ezf_table;
                                EzfForm::createZdata($modelEzform->ezf_table);
                                $sum['Ezform']['tsum'] ++;
                            } else {
                                $sum['Ezform']['fsum'] ++;
                            }
                        } catch (\yii\db\Exception $e) {
                            $sum['Ezform']['esum'] ++;
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }
                    }
                    $ezfError = $sum['Ezform']['esum']+$sum['Ezform']['fsum'];
                }
                
                
                if (isset($data['EzformFields']) && !empty($data['EzformFields']) && $ezfError==0) {

                    $sum['Fields']['all'] = 0;
                    $sum['Fields']['tsum'] = 0;
                    $sum['Fields']['fsum'] = 0;
                    $sum['Fields']['esum'] = 0;
                    foreach ($data['EzformFields'] as $value) {
                        try {
                            $sum['Fields']['all']++;
                            $modelEzformFields = new EzformFields();
                            $modelEzformFields->attributes = $value;
                            if ($modelEzformFields->save()) {
                                if(!in_array($modelEzformFields->ezf_field_name, ['id', 'ptid', 'xsourcex', 'xdepartmentx', 'rstat', 'sitecode', 'ptcode', 'ptcodefull', 'hptcode', 'hsitecode', 'user_create', 'create_date', 'user_update', 'update_date', 'target', 'error', 'sys_lat', 'sys_lng'])){
                                    if($modelEzformFields->table_field_type!='none' && $modelEzformFields->table_field_type!='field'){
                                        \backend\modules\ezbuilder\classes\EzBuilderFunc::alterTableAdd($ezf_table, $modelEzformFields->ezf_field_name, $modelEzformFields->table_field_type, $modelEzformFields->table_field_length, $modelEzformFields->table_index);
                                    }
                                }
                                $sum['Fields']['tsum'] ++;
                            } else {
                                $sum['Fields']['fsum'] ++;
                            }
                        } catch (\yii\db\Exception $e) {
                            $sum['Fields']['esum'] ++;
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }
                    }
                }

                if (isset($data['EzformChoice']) && !empty($data['EzformChoice']) && $ezfError==0) {

                    $sum['Choice']['all'] = 0;
                    $sum['Choice']['tsum'] = 0;
                    $sum['Choice']['fsum'] = 0;
                    $sum['Choice']['esum'] = 0;
                    foreach ($data['EzformChoice'] as $value) {
                        try {
                            $sum['Choice']['all']++;
                            $modelEzformChoice = new EzformChoice();
                            $modelEzformChoice->attributes = $value;
                            if ($modelEzformChoice->save()) {
                                $sum['Choice']['tsum'] ++;
                            } else {
                                $sum['Choice']['fsum'] ++;
                            }
                        } catch (\yii\db\Exception $e) {
                            $sum['Choice']['esum'] ++;
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }
                    }
                }

                if (isset($data['EzformAssign']) && !empty($data['EzformAssign']) && $ezfError==0) {

                    $sum['Assign']['all'] = 0;
                    $sum['Assign']['tsum'] = 0;
                    $sum['Assign']['fsum'] = 0;
                    $sum['Assign']['esum'] = 0;
                    foreach ($data['EzformAssign'] as $value) {
                        try {
                            $sum['Assign']['all']++;
                            $modelEzformAssign = new EzformAssign();
                            $modelEzformAssign->attributes = $value;
                            if ($modelEzformAssign->save()) {
                                $sum['Assign']['tsum'] ++;
                            } else {
                                $sum['Assign']['fsum'] ++;
                            }
                        } catch (\yii\db\Exception $e) {
                            $sum['Assign']['esum'] ++;
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }
                    }
                }

                if (isset($data['EzformCoDev']) && !empty($data['EzformCoDev']) && $ezfError==0) {

                    $sum['CoDev']['all'] = 0;
                    $sum['CoDev']['tsum'] = 0;
                    $sum['CoDev']['fsum'] = 0;
                    $sum['CoDev']['esum'] = 0;
                    foreach ($data['EzformCoDev'] as $value) {
                        try {
                            $sum['CoDev']['all']++;
                            $modelEzformCoDev = new EzformCoDev();
                            $modelEzformCoDev->attributes = $value;
                            if ($modelEzformCoDev->save()) {
                                $sum['CoDev']['tsum'] ++;
                            } else {
                                $sum['CoDev']['fsum'] ++;
                            }
                        } catch (\yii\db\Exception $e) {
                            $sum['CoDev']['esum'] ++;
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }
                    }
                }

                if (isset($data['EzformCondition']) && !empty($data['EzformCondition']) && $ezfError==0) {

                    $sum['Condition']['all'] = 0;
                    $sum['Condition']['tsum'] = 0;
                    $sum['Condition']['fsum'] = 0;
                    $sum['Condition']['esum'] = 0;
                    foreach ($data['EzformCondition'] as $value) {
                        try {
                            $sum['Condition']['all']++;
                            $modelEzformCondition = new EzformCondition();
                            $modelEzformCondition->attributes = $value;
                            if ($modelEzformCondition->save()) {
                                $sum['Condition']['tsum'] ++;
                            } else {
                                $sum['Condition']['fsum'] ++;
                            }
                        } catch (\yii\db\Exception $e) {
                            $sum['Condition']['esum'] ++;
                            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                        }
                    }
                }

                Yii::$app->session->setFlash('alert', [
                    'body' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Import completed.'),
                    'options' => ['class' => 'alert-success']
                ]);
            } else {
                Yii::$app->session->setFlash('alert', [
                    'body' => SDHtml::getMsgSuccess() . Yii::t('yii', 'File upload failed.'),
                    'options' => ['class' => 'alert-success']
                ]);
            }
        }

        return $this->render('import', [
                    'sum' => $sum,
        ]);
    }

    /**
     * Finds the Ezform model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Ezform the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Ezform::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
