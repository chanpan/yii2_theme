<?php

namespace backend\modules\ezbuilder\controllers;

use Yii;
use backend\modules\ezforms2\models\EzformFields;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use backend\modules\ezbuilder\classes\EzBuilderFunc;
use backend\modules\ezbuilder\classes\EzBuilderQuery;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;

/**
 * EzformFieldsController implements the CRUD actions for EzformFields model.
 */
class EzformFieldsController extends Controller
{
    public function behaviors()
    {
        return [
//	    'access' => [
//		'class' => AccessControl::className(),
//		'rules' => [
//		    [
//			'allow' => true,
//			'actions' => [], 
//			'roles' => ['?', '@'],
//		    ],
//		    [
//			'allow' => true,
//			'actions' => ['view-input', 'create', 'update', 'delete', 'clone'], 
//			'roles' => ['@'],
//		    ],
//		],
//	    ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    
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
     * Creates a new EzformFields model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($ezf_id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = new EzformFields();
	    $model->ezf_id = $ezf_id;
	    $model->ezf_field_id = SDUtility::getMillisecTime();
	    $model->ezf_field_type = 51;
	    $model->ezf_field_label = Yii::t('ezform', 'Unspecified question');
	    $model->ezf_field_name = EzfFunc::generateFieldName($ezf_id);
	    $model->ezf_field_order = EzfQuery::getFieldsCountById($ezf_id);
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                
                $modelEzf = EzfQuery::getEzformOne($ezf_id);
		$dataEzf = $modelEzf->attributes;
                
		//fix
		$model->ezf_id = $ezf_id;
		$model->ref_field_desc = SDUtility::array2String($model->ref_field_desc);
                $model->ref_field_search = SDUtility::array2String($model->ref_field_search);
                
		$dataInput;
	    
		if(isset(Yii::$app->session['ezf_input'])){
		    $dataInput = EzfFunc::getInputByArray($model->ezf_field_type, Yii::$app->session['ezf_input']);
		}
		
                $data = isset($_POST['data']) ? $_POST['data'] : [];
                $options = isset($_POST['options']) ? $_POST['options'] : [];
                $validate = isset($_POST['validate']) ? $_POST['validate'] : [];
                
		$result = EzBuilderFunc::saveEzField($model, $model, $dataEzf, $dataInput, $data, $options, $validate);
		return $result;
                
	    } else {
		return $this->renderAjax('create', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionSpace($ezf_id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $model = new EzformFields();
	    $model->ezf_id = $ezf_id;
	    $model->ezf_field_id = SDUtility::getMillisecTime();
	    $model->ezf_field_lenght = 6;
	    $model->ezf_field_label = '';
	    $model->ezf_field_name = EzfFunc::generateFieldName($ezf_id);
	    $model->ezf_field_order = EzfQuery::getFieldsCountById($ezf_id);
	    $model->ezf_field_type = 57;
	    $model->table_field_type = 'none';

            if ($model->save()) {
		if(isset(Yii::$app->session['ezf_input'])){
		    $dataInput = EzfFunc::getInputByArray($model->ezf_field_type, Yii::$app->session['ezf_input']);
		}
		if($dataInput){
		    $inputWidget = Yii::createObject($dataInput['system_class']);
		    $htmlInput = $inputWidget->generateViewInput($model->attributes);
		    $html = EzBuilderFunc::createChildrenItem($model->attributes, $htmlInput);
		    //Create table fields
		    $alterTable = true;
		    
		    
		    $result = [
			'status' => 'success',
			'action' => 'create',
			'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
			'data' => $model,
			'html'=>$html,
			'alterTable'=>$alterTable,
		    ];
		    return $result;
		}
		
	    } 
	    $result = [
		'status' => 'error',
		'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
		'data' => $model,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    public function actionClone($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $modelClone = $this->findModel($id);
	    
	    $model = new EzformFields();
	    $model->attributes = $modelClone->attributes;
	    $model->ezf_field_id = SDUtility::getMillisecTime();
	    $model->ezf_field_name = EzfFunc::generateFieldName($modelClone->ezf_id);
	    $model->ezf_field_order = EzfQuery::getFieldsCountById($modelClone->ezf_id);
	    
	    $data = [];
	    $field_data = SDUtility::string2Array($modelClone->ezf_field_data);
	    if(isset($field_data['builder']) && !empty($field_data['builder'])){
                $i=1;
		foreach ($field_data['builder'] as $key => $value) {
		    $id = SDUtility::getMillisecTime();
		    $value['action'] = 'create';
		    if(isset($value['other'])){
                        $value['other']['attribute'] = $model->ezf_field_name.'_other_'.$i;
                        $value['other']['id'] = SDUtility::getMillisecTime();
                        $value['other']['action'] = 'create';
                    }
                    
		    $data['builder'][$id] = $value;
                    
                    $i++;
		}
	    }
	    
	    $model->ezf_field_data = $data;
	    $model->ezf_field_options = SDUtility::string2Array($modelClone->ezf_field_options);
	    $model->ezf_field_specific = SDUtility::string2Array($modelClone->ezf_field_specific);
	    $model->ezf_field_validate = SDUtility::string2Array($modelClone->ezf_field_validate);
	    $model->ref_field_desc = SDUtility::string2Array($model->ref_field_desc);
            $model->ref_field_search = SDUtility::string2Array($model->ref_field_search);
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
		$modelEzf = EzfQuery::getEzformOne($model->ezf_id);
		$dataEzf = $modelEzf->attributes;
                
		$model->ezf_field_id = SDUtility::getMillisecTime();
		$model->ref_field_desc = SDUtility::array2String($model->ref_field_desc);
                $model->ref_field_search = SDUtility::array2String($model->ref_field_search);
                
		$dataInput;
	    
		if(isset(Yii::$app->session['ezf_input'])){
		    $dataInput = EzfFunc::getInputByArray($model->ezf_field_type, Yii::$app->session['ezf_input']);
		}
		
                $data = isset($_POST['data']) ? $_POST['data'] : [];
                $options = isset($_POST['options']) ? $_POST['options'] : [];
                $validate = isset($_POST['validate']) ? $_POST['validate'] : [];
                
		$result = EzBuilderFunc::saveEzField($model, $model, $dataEzf, $dataInput, $data, $options, $validate);
		return $result;
                
	    } else {
		return $this->renderAjax('update', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Updates an existing EzformFields model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $ezf_field_id
     * @param integer $ezf_field_icon
     * @return mixed
     */
    public function actionUpdate($id)
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $model = $this->findModel($id);
            
	    $model->ezf_field_data = SDUtility::string2Array($model->ezf_field_data);
	    $model->ezf_field_options = SDUtility::string2Array($model->ezf_field_options);
	    $model->ezf_field_specific = SDUtility::string2Array($model->ezf_field_specific);
	    $model->ezf_field_validate = SDUtility::string2Array($model->ezf_field_validate);
	    $model->ref_field_desc = SDUtility::string2Array($model->ref_field_desc);
            $model->ref_field_search = SDUtility::string2Array($model->ref_field_search);
                
	    $oldModel = $model->attributes;
            
	    if ($model->load(Yii::$app->request->post())) {
		Yii::$app->response->format = Response::FORMAT_JSON;
                $modelEzf = EzfQuery::getEzformOne($model->ezf_id);
		$dataEzf = $modelEzf->attributes;
                
                $model->ref_field_desc = isset($_POST['EzformFields']['ref_field_desc'])?$_POST['EzformFields']['ref_field_desc']:[];
                $model->ref_field_search = isset($_POST['EzformFields']['ref_field_search'])?$_POST['EzformFields']['ref_field_search']:[];
                
                $model->ref_field_desc = SDUtility::array2String($model->ref_field_desc);
                $model->ref_field_search = SDUtility::array2String($model->ref_field_search);

                $dataInput;
                if(isset(Yii::$app->session['ezf_input'])){
                    $dataInput = EzfFunc::getInputByArray($model->ezf_field_type, Yii::$app->session['ezf_input']);
                }
                
                $data = isset($_POST['data']) ? $_POST['data'] : [];
                $options = isset($_POST['options']) ? $_POST['options'] : [];
                $validate = isset($_POST['validate']) ? $_POST['validate'] : [];

                $result = EzBuilderFunc::saveEzField($model, $oldModel, $dataEzf, $dataInput, $data, $options, $validate);
                
		return $result;
                    
	    } else {
		return $this->renderAjax('update', [
		    'model' => $model,
		]);
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }

    /**
     * Deletes an existing EzformFields model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $ezf_field_id
     * @param integer $ezf_field_icon
     * @return mixed
     */
    public function actionDelete()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $ezf_field_id = isset($_POST['id'])?$_POST['id']:0;
	    Yii::$app->response->format = Response::FORMAT_JSON;
            
            $result = EzBuilderFunc::deleteEzField($ezf_field_id);
            return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionResize()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    $id = isset($_POST['id'])?$_POST['id']:0;
	    $method = isset($_POST['method'])?$_POST['method']:1;
	    
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    $model = $this->findModel($id);
	    $oldSize = $model->ezf_field_lenght;
	    
	    if($method==1){
		$size = $oldSize+1;
		$size = $size>12?1:$size;
	    } else {
		$size = $oldSize-1;
		$size = $size==0?12:$size;
	    }
	    
	    $model->ezf_field_lenght = $size;
	    
	    if ($model->save()) {
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
		    'data' => $id,
		    'oldSize' => $oldSize,
		    'newSize' => $size,
		];
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

    public function actionViewInput()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    $id = isset($_POST['id'])?$_POST['id']:0;
	    $ezf_field_id = isset($_POST['ezf_field_id'])?$_POST['ezf_field_id']:0;
            $ezf_id = isset($_POST['ezf_id'])?$_POST['ezf_id']:0;
	    $name = isset($_POST['name'])?$_POST['name']:$attrEzf['ezf_field_name'];
            $label = isset($_POST['label'])?$_POST['label']:$attrEzf['ezf_field_label'];
            $newitem = isset($_POST['newitem'])?(int)$_POST['newitem']:1;
            
	    $dataInput;
	    
	    if(isset(Yii::$app->session['ezf_input'])){
		$dataInput = EzfFunc::getInputByArray($id, Yii::$app->session['ezf_input']);
	    }

            if ($dataInput && !empty($dataInput['system_class'])) {
		try {
		    $inputWidget = Yii::createObject($dataInput['system_class']);
                    $model = EzformFields::find()->where('ezf_field_id=:ezf_field_id', [':ezf_field_id'=>$ezf_field_id])->one();
                    if($model){
                        $model->ezf_field_data = SDUtility::string2Array($model->ezf_field_data);
                        $model->ezf_field_options = SDUtility::string2Array($model->ezf_field_options);
                        $model->ezf_field_specific = SDUtility::string2Array($model->ezf_field_specific);
                        $model->ezf_field_validate = SDUtility::string2Array($model->ezf_field_validate);
                        $model->ref_field_desc = SDUtility::string2Array($model->ref_field_desc);
                        $model->ref_field_search = SDUtility::string2Array($model->ref_field_search);
                    } else {
                        $model = new EzformFields();
                        $model->ezf_field_name = $name;
                        $model->ezf_field_label = $label;
                        $model->ezf_field_type = $id;
                        $model->ezf_id = $ezf_id;
                        $model->ezf_field_id = $ezf_field_id;
                    }

                    $size = (isset($model->ezf_field_lenght) && $model->ezf_field_lenght>0)?$model->ezf_field_lenght:$dataInput['input_size'];
                    
                    if($newitem){
                        $model->ezf_field_data = '';
                    }
		    
		    $html = $inputWidget->generateViewEditor($dataInput, $model);
		    $options = $inputWidget->generateOptions($dataInput, $model);
		    $validations = $inputWidget->generateValidations($dataInput, $model);
		    
		    $result = [
			'status' => 'success',
			'action' => 'update',
			'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Generate completed.'),
			'data' => $id,
			'html' => $html,
			'options' => $options,
			'validations' => $validations,
                        'size' => $size,
		    ];
		    return $result;
		} catch (\ReflectionException $e) {
		    $result = [
			'status' => 'error',
			'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Class `{class}` does not exist.', ['class'=>$dataInput['system_class']]),
			'data' => $id,
		    ];
		    return $result;
		}
                
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Question type not found.'),
		    'data' => $id,
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionViewValidations()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    $view = isset($_POST['view'])?$_POST['view']:0;
	    $row = isset($_POST['row'])?$_POST['row']:0;
	    
	    if ($view) {
		$html = $this->renderAjax($view, [
		    'row'=>$row,
		]);
		
		$result = [
		    'status' => 'success',
		    'action' => 'update',
		    'message' => SDHtml::getMsgSuccess() . Yii::t('ezform', 'Generate completed.'),
		    'html' => $html,
		];
		return $result;
	    } else {
		$result = [
		    'status' => 'error',
		    'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Question type not found.'),
		];
		return $result;
	    }
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    /**
     * Finds the EzformFields model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $ezf_field_id
     * @param integer $ezf_field_icon
     * @return EzformFields the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($ezf_field_id)
    {
        if (($model = EzformFields::findOne(['ezf_field_id' => $ezf_field_id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
