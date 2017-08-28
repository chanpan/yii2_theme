<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\modules\ezforms2\classes\EzfUiFunc;
use appxq\sdii\utils\SDUtility;


/**
 * Select2Controller implements the CRUD actions for EzformInput model.
 */
class Select2Controller extends Controller
{
    
    public function actionCreate()
    {
	if (Yii::$app->getRequest()->isAjax) {
	    Yii::$app->response->format = Response::FORMAT_JSON;
	    
	    $row = isset($_POST['row'])?$_POST['row']:0;
	    
	    $html = $this->renderAjax('//../modules/ezbuilder/views/widgets/select2/_formitem', [
		'row' => $row,
	    ]);
	    
	    $result = [
		'status' => 'success',
		'action' => 'create',
		'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
		'html' => $html,
	    ];
	    return $result;
	} else {
	    throw new NotFoundHttpException('Invalid request. Please do not repeat this request again.');
	}
    }
    
    public function actionHospital($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT lpad(`code`,'5','0') AS `code`,`name` FROM `const_hospital` WHERE `name` LIKE :q OR `code` LIKE :q LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;

            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['code'],'text'=>$value["code"]." : ".$value["name"]];
                $i++;
            }
        
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
//        
        return $out;
    }
    
    public function actionSnomed($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $q=urlencode($q);
        $json = file_get_contents("http://www.cascap.in.th:9201/snomed/description/_search?q=TERM:{$q}&size=50&sort=_score:desc");
        $arrayJson = json_decode($json,true);
        $data=$arrayJson['hits']['hits'];
        $i=0;
        foreach ($data as $snomed) {
                $json2 = file_get_contents("http://www.cascap.in.th:9201/snomed/concept/_search?q=".$snomed['_source']['CONCEPTID']);
                $arrayJson2 = json_decode($json2,true);
                $data2=$arrayJson2['hits']['hits']['0']['_source'];
                //print_r($data2);exit;
                $out['results'][$i] = ['id' => $snomed['_source']['DESCRIPTIONID'], 'text' => "<b>" . $snomed['_source']['TERM']."</b> (".$data2['FULLYSPECIFIEDNAME'].")"];
                $i++;
        }
        return  $out;
    }
    

    public function actionIcd10($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT * FROM `const_icd10` WHERE CONCAT(`code`, `name`) LIKE :q LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['code'],'text'=>$value["code"]." : ".$value["name"]];
                $i++;
            }
        
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
//        
        return $out;
    }
    
    public function actionIcd9($q = null, $id = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];
        if (is_null($q)) {
            $q = '';
        }
            $sql = "SELECT * FROM `const_icd9` WHERE CONCAT(`code`, `name`) LIKE :q LIMIT 0,50";
            $data = Yii::$app->db->createCommand($sql, [':q'=>"%$q%"])->queryAll();
            $i = 0;
            
            foreach($data as $value){
                $out["results"][$i] = ['id'=>$value['code'],'text'=>$value["code"]." : ".$value["name"]];
                $i++;
            }
        
//        if ($id > 0) {
//            $out['results'] = ['id' => $id, 'text' => City::find($id)->name];
//        }
//        
        return $out;
    }
    
    public static function initHospital($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT lpad(`code`,'5','0') AS `code`,`name` FROM `const_hospital` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public static function initSnomed($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT lpad(`code`,'5','0') AS `code`,`name` FROM `const_hospital` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public static function initIcd10($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT * FROM `const_icd10` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public static function initIcd9($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        if(isset($code) && !empty($code)){
            $sql = "SELECT * FROM `const_icd9` WHERE `code`=:code";
            $data = Yii::$app->db->createCommand($sql, [':code'=>$code])->queryOne();
            
            $str = $data['code'].' : '. $data['name'];
        }
        
        return $str;
    }
    
    public function actionFindComponent($q = null, $id = null) {
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $out = ['results' => []];

        $dataEzf = EzfQuery::getEzformTargetField($ezf_field_id);
        $modelFields = EzfQuery::findSpecialOne($ezf_id);
        
        if ($dataEzf) {
            $table = $dataEzf['ezf_table'];
            $ref_id = $dataEzf['ref_field_id'];
            $nameConcat = EzfFunc::array2ConcatStr($dataEzf['ref_field_desc']);
            if (!$nameConcat) {
                return $out;
            }

            $searchConcat = EzfFunc::array2ConcatStr($dataEzf["ref_field_search"]);
            if (!$searchConcat) {
                return $out;
            }
        } else {
            return $out;
        }

        if (is_null($q)) {
            $q = '';
        }
        
        $query = new \yii\db\Query();
        $query->select(["`$ref_id` AS id", "$nameConcat AS`name`"]);
        $query->from("`$table`");
        $query->where("$searchConcat LIKE :q  AND rstat not in(0, 3)", [':q' => "%$q%"]);
        $query->limit(50);
        
        if($modelFields){
           $query->andWhere('xsourcex = :site', [':site'=>Yii::$app->user->identity->profile->sitecode]);
        }
        
        $data = $query->createCommand()->queryAll();
        
        foreach ($data as $value) {
            $out["results"][] = ['id' => $value['id'], 'text' => $value["name"]];
        }

        return $out;
    }

    public static function initComponent($model, $modelFields) {
        $code = $model[$modelFields['ezf_field_name']];
        $str = '';
        
        $modelEzf = EzfQuery::getEzformOne($modelFields['ref_ezf_id']);

        $table = $modelEzf['ezf_table'];
        $ref_id = $modelFields['ref_field_id'];
        $nameConcat = EzfFunc::array2ConcatStr($modelFields['ref_field_desc']);
        if (!$nameConcat) {
            return $str;
        }
        
        if (isset($code) && !empty($code)) {
            $sql = "SELECT `$ref_id` AS id, $nameConcat AS`name` FROM `$table` WHERE `$ref_id` =:id";
            $data = Yii::$app->db->createCommand($sql, [':id' => $code])->queryOne();

            $str = $data['name'];
        }

        return $str;
    }
    
    public static function actionCheckComp() {
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        $modal = isset($_GET['modal']) ? $_GET['modal'] : '';
        $dataid = isset($_GET['dataid']) ? $_GET['dataid'] : '';
        
        $dataEzf = EzfQuery::getEzformTargetField($ezf_field_id);
        if($dataid!=''){
            $dataFields = EzfQuery::getRefFieldById($ezf_field_id);
            if($dataFields){
                if($dataFields['ref_field_id']!='id'){
                    
                    $newId = EzfQuery::builderSqlGetScalar(["id"], $dataFields['ezf_table'], "{$dataFields['ref_field_id']} = :dataid  AND rstat not in(0, 3)", [':dataid' => $dataid]);
                    if($newId){
                        $dataid = $newId;
                    }
                }
            }
        }
        
        $userProfile = Yii::$app->user->identity->profile;
        $user_id = $userProfile->user_id;
        $created_by = 0;
        $codev = [];
        
        if ($dataEzf) {
            $created_by = $dataEzf['user_by'];
            $codev = SDUtility::string2Array($dataEzf['co_dev']);
        } 

        $html = '';
        $html .= Html::button('<i class="glyphicon glyphicon-cog"></i> ', ['class'=>'btn btn-default btn-cong', 'data-active'=>1, 'data-url'=>Url::to(['/ezforms2/select2/check-comp', 'ezf_field_id'=>$ezf_field_id, 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'dataid'=>'']), 'data-id'=>$dataid]).' ';
        if($created_by == $user_id || $dataEzf['public_edit']==1 || in_array($user_id, $codev)){
            if($dataid!=''){
                $html .= Html::button('<i class="glyphicon glyphicon-eye-open"></i> ', ['data-toggle'=>'tooltip', 'title'=> Yii::t('ezform', 'Open Form'), 'class'=>'btn btn-primary btn-open-ezform btn-edit', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$ezf_id, 'modal'=>$modal, 'dataid'=>'']), 'data-id'=>$dataid, 'style'=>$dataid>0?'':'display: none;']).' ';
            }
            $html .= Html::button('<i class="glyphicon glyphicon-plus"></i> ', ['data-toggle'=>'tooltip', 'title'=>Yii::t('app', 'New'), 'class'=>'btn btn-success btn-open-ezform btn-add', 'data-url'=>Url::to(['/ezforms2/ezform-data/ezform', 'ezf_id'=>$ezf_id, 'modal'=>$modal ])]).' ';
        } else {
            $html .= Html::button(Yii::t('ezform', 'Form creator disabled.'), ['class'=>'btn btn-danger ']).' ';
        }
        
        return $html;
    }
    
}
