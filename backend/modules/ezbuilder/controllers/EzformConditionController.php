<?php

namespace backend\modules\ezbuilder\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformCondition;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\models\EzformFields;
use yii\helpers\Json;

/**
 * EzformConditionController implements the CRUD actions for EzformCondition model.
 */
class EzformConditionController extends Controller {

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

    public function beforeAction($action) {
	if (parent::beforeAction($action)) {
	    if (in_array($action->id, array('create', 'update'))) {
		
	    }
	    return true;
	} else {
	    return false;
	}
    }

    public function actionFields($id) {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = EzformFields::find()
		    ->where('ezf_id = :id AND ezf_field_type <> 57', [':id' => $id])
		    ->orderBy('ezf_field_order')
		    ->all();

	    return $this->renderAjax('/ezform-fields/_fields_view', [
			'model' => $model,
	    ]);
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Creates a new EzformCondition model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCondition() {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;

	    $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
	    $ezf_field_name = isset($_POST['ezf_field_name']) ? $_POST['ezf_field_name'] : '';
	    $ezf_field_value = isset($_POST['ezf_field_value']) ? $_POST['ezf_field_value'] : '';

	    $model = EzformCondition::find()
		    ->where('ezf_id=:ezf_id AND ezf_field_name=:ezf_field_name AND ezf_field_value=:ezf_field_value', [':ezf_id' => $ezf_id, ':ezf_field_name' => $ezf_field_name, ':ezf_field_value' => $ezf_field_value])
		    ->one();

	    if ($model) {
		$model->cond_jump = Json::decode($model->cond_jump);
		$model->cond_require = Json::decode($model->cond_require);

                if(is_array($model->cond_jump)){
                    $cond_jump = implode(',', $model->cond_jump);
                }else {
                    $cond_jump = '';
                }
                
                if(is_array($model->cond_require)){
                    $cond_require = implode(',', $model->cond_require);
                } else {
                    $cond_require = '';
                }

		$model->label_jump = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_label', $cond_jump), 'ezf_field_label');
		$model->label_require = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_label', $cond_require), 'ezf_field_label');
		$model->var_jump = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_name', $cond_jump), 'ezf_field_name');
		$model->var_require = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_name', $cond_require), 'ezf_field_name');
	    }

	    $dataEzf = [];
	    $dataEzf['ezf_id'] = $ezf_id;
	    $dataEzf['ezf_field_name'] = $ezf_field_name;
	    $dataEzf['ezf_field_value'] = $ezf_field_value;
	    $dataEzf['cond_jump'] = isset($model->cond_jump) ? $model->cond_jump : '';
	    $dataEzf['cond_require'] = isset($model->cond_require) ? $model->cond_require : '';
	    $dataEzf['label_jump'] = isset($model->label_jump) ? $model->label_jump : '';
	    $dataEzf['label_require'] = isset($model->label_require) ? $model->label_require : '';
	    $dataEzf['var_jump'] = isset($model->var_jump) ? $model->var_jump : '';
	    $dataEzf['var_require'] = isset($model->var_require) ? $model->var_require : '';

	    if ($model) {
		$result = [
		    'status' => 'success',
		    'action' => 'create',
		    'message' => '<strong><i class="glyphicon glyphicon-remove-sign"></i> Success!</strong> ' . Yii::t('app', 'Data completed.'),
		    'data' => $dataEzf,
		];

		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => '<strong><i class="glyphicon glyphicon-remove-sign"></i> Success!</strong> ' . Yii::t('app', 'Can not load data.'),
		    'data' => $dataEzf,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Finds the EzformCondition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EzformCondition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
	if (($model = EzformCondition::findOne($id)) !== null) {
	    return $model;
	} else {
	    throw new NotFoundHttpException('The requested page does not exist.');
	}
    }

}
