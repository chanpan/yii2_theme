<?php

namespace backend\modules\ezbuilder\controllers;

use Yii;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\EzformCoDev;
use backend\modules\ezforms2\models\EzformChoice;
use backend\modules\ezforms2\models\EzformAssign;
use backend\modules\ezforms2\models\EzformCondition;

/**
 * EzformController implements the CRUD actions for Ezform model.
 */
class EzformBuilderController extends Controller {

    public function behaviors() {
	return [
//	    'access' => [
//		'class' => AccessControl::className(),
//		'rules' => [
//		    [
//			'allow' => true,
//			'actions' => ['index', 'view'],
//			'roles' => ['?', '@'],
//		    ],
//		    [
//			'allow' => true,
//			'actions' => ['view', 'create', 'update', 'delete', 'deletes'],
//			'roles' => ['@'],
//		    ],
//		],
//	    ],
//	    
//	    'verbs' => [
//		'class' => VerbFilter::className(),
//		'actions' => [
//		    'delete' => ['post'],
//		],
//	    ],
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
     * Updates an existing Ezform model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {

	$model = $this->findModel($id);
	
	Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
	//Yii::$app->session['ezform'] = $model->attributes;
        //unset(Yii::$app->session['ezform']);
        Yii::$app->session['show_varname'] = 1;
        
        $modelFields = EzformFields::find()
            ->where('ezf_id = :ezf_id', [':ezf_id' => $model->ezf_id])
            ->orderBy(['ezf_field_order' => SORT_ASC])
            ->all();
        
	$userlist = ArrayHelper::map(EzfQuery::getIntUserAll(), 'id', 'text'); //explode(",", $model1->assign);

	//$model->assign = explode(',', $model->assign);
	//$model->field_detail = explode(",", $model->field_detail);
	
        $modelCoDevs = EzformCoDev::find()
            ->where(['ezf_id' => $id])
            ->all();
	
	$userprofile = \common\modules\user\models\User::findOne(['id' => $model->created_by])->profile;
	
        $modelDynamic = EzfFunc::setDynamicModel($modelFields, $model->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        
        //get table from tcc bot
        $tccTables = [];
	
	return $this->render('update', [
	    'model' => $model,
	    'ezf_id' => $id,
	    'modelFields' => $modelFields,
	    'userlist' => $userlist,
	    'modelCoDevs' => $modelCoDevs,
	    'modelDynamic' => $modelDynamic,
	    'tccTables' => $tccTables,
	    'userprofile'=>$userprofile,
            
	]);
    }

    public function actionOrderUpdate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $position = isset($_POST['position'])?$_POST['position']:[];

	    $sql = '';
	    foreach ($position as $key => $field_id) {
		$order = $key+1;
		$sql .= "UPDATE `ezform_fields` SET `ezf_field_order`='$order' WHERE `ezf_field_id`='$field_id'; ";
	    }
	    try {
		Yii::$app->db->createCommand($sql)->execute();
	    }
	    catch (\yii\db\Exception $e)
	    {
		
	    }
	}
    }
    
    public function actionViewform($id)
    {
        date_default_timezone_set('UTC');
        
        $dataid = isset($_GET['dataid'])?$_GET['dataid']:'';
        
	$modelEzf = $this->findModel($id);
	
	Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        Yii::$app->session['show_varname'] = 0;
	//Yii::$app->session['ezform'] = $modelEzf->attributes;
	
        
        $modelFields = EzformFields::find()
            ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
            ->orderBy(['ezf_field_order' => SORT_ASC])
            ->all();
    
	$model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);
                
        if(!$model){
            Yii::$app->session->setFlash('alert', [
                    'body' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                    'options' => ['class' => 'alert-danger']
            ]);
            
            return $this->redirect(['viewform', 'id'=>$id]);
        }
        
	return $this->render('viewform', [
	    'modelEzf' => $modelEzf,
	    'ezf_id' => $id,
	    'modelFields' => $modelFields,
	    'model'=>$model,
	]);
    }
    
    public function actionExport($ezf_id)
    {
        $idName = '';
        if ($_SERVER["REMOTE_ADDR"] == '::1' || $_SERVER["REMOTE_ADDR"] == '127.0.0.1') {
            $idName = 'mycom';
        } else {
            $idName = str_replace('.', '_', $_SERVER["REMOTE_ADDR"]);
        }
        $fileName = 'export_'.$idName.'.xlsx';
        
        $schemaEzform = Ezform::getTableSchema();
        $schemaEzformFields = EzformFields::getTableSchema();
        $schemaEzformChoice = EzformChoice::getTableSchema();
        $schemaEzformAssign = EzformAssign::getTableSchema();
        $schemaEzformCoDev = EzformCoDev::getTableSchema();
        $schemaEzformCondition = EzformCondition::getTableSchema();

	$export = \appxq\sdii\widgets\SDExcel::export([
            'fileName'=>$fileName,
            'savePath'=> Yii::getAlias('@backend/web/print'),
            'format'=>'Excel2007',
            'asAttachment'=>false,
            'isMultipleSheet' => true,
            'models' => [
                    'Ezform' => Ezform::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all(), 
                    'EzformFields' => EzformFields::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all(), 
                    'EzformChoice' => EzformChoice::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all(),
                    'EzformAssign' => EzformAssign::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all(),
                    'EzformCoDev' => EzformCoDev::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all(),
                    'EzformCondition' => EzformCondition::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->all(),
            ], 
            'columns' => [
                    'Ezform' => $schemaEzform->columnNames, 
                    'EzformFields' => $schemaEzformFields->columnNames,
                    'EzformChoice' => $schemaEzformChoice->columnNames,
                    'EzformAssign' => $schemaEzformAssign->columnNames,
                    'EzformCoDev' => $schemaEzformCoDev->columnNames,
                    'EzformCondition' => $schemaEzformCondition->columnNames,
            ], 
            'headers' => [
                    'Ezform' => ArrayHelper::map($schemaEzform->columns, 'name', 'name'), 
                    'EzformFields' => ArrayHelper::map($schemaEzformFields->columns, 'name', 'name'), 
                    'EzformChoice' => ArrayHelper::map($schemaEzformChoice->columns, 'name', 'name'), 
                    'EzformAssign' => ArrayHelper::map($schemaEzformAssign->columns, 'name', 'name'), 
                    'EzformCoDev' => ArrayHelper::map($schemaEzformCoDev->columns, 'name', 'name'), 
                    'EzformCondition' => ArrayHelper::map($schemaEzformCondition->columns, 'name', 'name'), 
            ], 
        ]);
        
        $this->redirect(Yii::getAlias('@web/print/').$fileName);
    }
    
    
    
    
    public function actionDemo()
    {
        return $this->render('demo', [
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
