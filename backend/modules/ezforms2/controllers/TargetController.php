<?php

namespace backend\modules\ezforms2\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Json;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\classes\EzfFunc;
use backend\modules\ezforms2\classes\EzfQuery;
use yii\web\Response;
use appxq\sdii\helpers\SDHtml;

/**
 * TargetController implements the CRUD actions for EzformInput model.
 */
class TargetController extends Controller {

    public function actionGetFields() {
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $value = isset($_POST['value']) ? $_POST['value'] : 0;
        $multiple = isset($_POST['multiple']) ? (int)$_POST['multiple'] :0;
        
        $id = isset($_POST['id']) ? $_POST['id'] : \appxq\sdii\utils\SDUtility::getMillisecTime();
        $sql = "SELECT ezf_field_name AS `id`, concat(ezf_field_name, ' (', ezf_field_label, ')') AS`name` FROM `ezform_fields` WHERE `ezf_id` = :id AND table_field_type not in('none','field')";
        $data = Yii::$app->db->createCommand($sql, [':id' => $ezf_id])->queryAll();

        return $this->renderAjax("/widgets/_subselect", [
                    'id' => $id,
                    'name' => $name,
                    'value' => $value,
                    'multiple'=>$multiple,
                    'data' => \yii\helpers\ArrayHelper::map($data, 'id', 'name'),
        ]);
    }
    
    public function actionParentFields() {
        $ezf_id = isset($_POST['ezf_id']) ? $_POST['ezf_id'] : 0;
        $target = EzfQuery::getTargetOne($ezf_id);
        if($target){
            return $target['parent_ezf_id'];
        }
        
        return $ezf_id;
    }

    public function actionFindTarget($q = null, $id = null) {
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
        $query->select(["`$ref_id` AS id", "$nameConcat AS `name`"]);
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

    public static function initTarget($model, $modelFields) {
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
    
    public function actionCheckcid() {
        $cid = isset($_GET['cid']) ? $_GET['cid'] : 0;
        $ezf_id = isset($_GET['ezf_id']) ? $_GET['ezf_id'] : 0;
        $ezf_field_id = isset($_GET['ezf_field_id']) ? $_GET['ezf_field_id'] : 0;
        
        $data_field = EzfQuery::getEzformWithField($ezf_field_id);
        $userProfile = Yii::$app->user->identity->profile;
        $checkcid = EzfFunc::check_citizen($cid);
        if($checkcid){
            $data = EzfQuery::checkCidAll($data_field['ezf_table'], $data_field['ezf_field_name'], $cid);
            if($data){
                $dataSpecial;
                foreach ($data as $key => $value) {
                    if($value['xsourcex']==$userProfile->sitecode){
                        $dataSpecial['present']=$value;
                    } elseif($value['ptid']==$value['target']){
                        $dataSpecial['first']=$value;
                    }
                }
                
                if(isset($dataSpecial['present'])){
                    return '<div class="alert alert-danger" role="alert" style="font-size: 20px;">'.SDHtml::getMsgError() . Yii::t('ezform', 'Have this card number in the agency.').'</div>';
                    
                } elseif($dataSpecial['first']) {
                    //clone
                    unset($dataSpecial['first']['id']);
                    $initdata = $dataSpecial['first'];
                    $dataSet = EzfFunc::arrayEncode2String($initdata);

                    $initdataEmpty = [
                        'ptid'=>$dataSpecial['first']['ptid'],
                        'sitecode'=>$dataSpecial['first']['sitecode'],
                        'ptcode'=>$dataSpecial['first']['ptcode'],
                        'ptcodefull'=>$dataSpecial['first']['ptcodefull'],
                        $data_field['ezf_field_name']=>$cid
                    ];
                    $dataSetEmpty = EzfFunc::arrayEncode2String($initdataEmpty);
                    
                    return $this->renderAjax("/widgets/_cidselect", [
                                'initdata' => $dataSet,
                                'initdataEmpty' => $dataSetEmpty,
                                'ezf_id' => $ezf_id,
                                'data' => $dataSpecial['first'],
                                'type' => 2,
                    ]);
                }
            } 
            // new
            $initdata = [$data_field['ezf_field_name']=>$cid];
            $dataSet = EzfFunc::arrayEncode2String($initdata);
            
            return $this->renderAjax("/widgets/_cidselect", [
                        'initdata' => $dataSet,
                        'ezf_id' =>$ezf_id,
                        'type'=>1,
            ]);
            
        }
        return '<div class="alert alert-danger" role="alert" style="font-size: 20px;">'.SDHtml::getMsgError() . Yii::t('ezform', 'Invalid card number.').'</div>';
    }

}
