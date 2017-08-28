<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use yii\helpers\ArrayHelper;
use appxq\sdii\helpers\SDHtml;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\EzformCoDev;
use backend\modules\ezforms2\models\EzformAssign;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;
use backend\modules\ezforms2\models\EzformChoice;
use backend\modules\ezforms2\models\EzformCondition;
use backend\modules\ezforms2\models\EzformTarget;
/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class EzfForm {

    public static function saveEzfForm($model) {
        $isNewRecord = $model->isNewRecord;

        if ($isNewRecord) {
            $action = 'create';
            $model->ezf_table = 'zdata_' . $model->ezf_id; //gen Zdata table name
            if (EzfQuery::findTable($model->ezf_table)) {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Table `{table}` has already been taken.', ['table'=>$model->ezf_table]),
                    'data' => $model,
                ];
                return $result;
            }
        } else {
            $action = 'update';
        }

        //array2String to field database
        $model->co_dev = SDUtility::array2String($model->co_dev);
        $model->consult_users = SDUtility::array2String($model->consult_users);
        $model->assign = SDUtility::array2String($model->assign);
        $model->field_detail = SDUtility::array2String($model->field_detail);

        if ($model->save()) {
            EzfForm::saveEzfCoDev(SDUtility::string2Array($model->co_dev), $model->ezf_id);
            EzfForm::saveEzfAssign(SDUtility::string2Array($model->assign), $model->ezf_id);
            if ($isNewRecord) {
                EzfForm::initEzfField($model->ezf_id, ['id']);
                if (!EzfForm::createZdata($model->ezf_table)) {
                    self::deleteRecord($model->ezf_id);
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not create table.'),
                        'data' => $model,
                    ];
                    return $result;
                }
            }

            $result = [
                'status' => 'success',
                'action' => $action,
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                'data' => $model,
            ];
            return $result;
        } else {
            if (!$isNewRecord) {
                self::rollbackRecord($model);
            }

            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not Save the data.'),
                'data' => $model,
            ];
            return $result;
        }
    }

    public static function deleteEzfForm($model) {
        if (empty($model->ezf_id)) {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'The requested field was not found. {value}', ['value'=>'<code>[ezf_id]</code>']),
            ];
            return $result;
        }

        try {
            self::deleteRecord($model->ezf_id);
            if (!EzfForm::dropZdata($model->ezf_id)) {
                self::rollbackRecord($model, 'delete');

                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Delete the data Error.'),
                    'data' => $model,
                ];
                return $result;
            }

            $result = [
                'status' => 'success',
                'action' => 'delete',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                'data' => $model,
            ];
            return $result;
        } catch (\yii\db\Exception $e) {
            self::rollbackRecord($model, 'delete');

            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                'data' => $model,
            ];
            return $result;
        }
    }

    public static function trashEzfForm($model) {
        if ($model->status == '1') {
            $model->status = '0';
        } elseif ($model->status == '0') {
            $model->status = '1';
        }
        if ($model->save()) {
            $result = [
                'status' => 'success',
                'action' => 'create',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                'data' => $model,
            ];
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                'data' => $model,
            ];
        }
        return $result;
    }

    public static function saveEzfCoDev($arrCoDev, $ezf_id) {
        try {
            $coIn = "user_co NOT IN('" . implode("','", $arrCoDev) . "')";
            EzformCoDev::deleteAll("ezf_id = '$ezf_id' AND $coIn");
            foreach ($arrCoDev as $user_id) {
                EzfQuery::insertEzformCoDev($ezf_id, $user_id);
            }
        } catch (\yii\db\Exception $e) {
            EzformCoDev::deleteAll(['ezf_id' => $user_id]);
            return FALSE;
        }
    }

    public static function saveEzfAssign($arrAssign, $ezf_id) {
        try {
            $assIn = "user_id NOT IN('" . implode("','", $arrAssign) . "')";
            EzformAssign::deleteAll("ezf_id = '$ezf_id' AND $assIn");
            foreach ($arrAssign as $user_id) {
                EzfQuery::insertEzformAssign($ezf_id, $user_id);
            }
        } catch (\yii\db\Exception $e) {
            EzformAssign::deleteAll(['ezf_id' => $id]);
            return FALSE;
        }
    }

    /**
     * Function initEzfField.
     * @param string $id ezf_id เช่น '1502169443023044700'
     * @param array $arrField ส่งค่า ezf_id และชื่อที่ต้องการบันทึกเช่น ['id','item_id','var_comment']
     */
    public static function initEzfField($id, $arrField) {
        if (isset($id) && isset($arrField) && is_array($arrField)) {
            foreach ($arrField as $value) {
                $model = new EzformFields;
                $model->ezf_field_id = SDUtility::getMillisecTime();
                $model->ezf_id = $id;
                $model->ezf_field_name = $value;
                $model->ezf_field_label = $model->getAttributeLabel($value);
                $model->ezf_field_type = '0';
                $model->table_field_type = 'VARCHAR';
                $model->table_field_length = '150';
                $model->save();
            }
        } else {
            return FALSE;
        }
    }

    public static function createZdata($id) {
        try {
            EzfQuery::copyTable($id, 'zdata_init');
            return TRUE;
        } catch (\yii\db\Exception $e) {
            return FALSE;
        }
    }

    public static function dropZdata($id) {
        try {
            EzfQuery::dropTable('zdata_' . $id);
            return TRUE;
        } catch (\yii\db\Exception $e) {
            return FALSE;
        }
    }

    private static function deleteRecord($id) {
        Ezform::deleteAll(['ezf_id' => $id]);
        EzformCoDev::deleteAll(['ezf_id' => $id]);
        EzformAssign::deleteAll(['ezf_id' => $id]);
        EzformFields::deleteAll(['ezf_id' => $id]);
        EzformChoice::deleteAll(['ezf_id' => $id]);
        EzformCondition::deleteAll(['ezf_id' => $id]);
        //EzformTarget::deleteAll(['ezf_id' => $id]);
    }

    private static function rollbackRecord($model, $action = null) {
        $model->attributes = $model->oldAttributes;
        EzfForm::saveEzfCoDev(SDUtility::string2Array($model->co_dev), $model->ezf_id);
        EzfForm::saveEzfAssign(SDUtility::string2Array($model->assign), $model->ezf_id);
        if ($action == 'delete') {
            $modelEzfom = new Ezform;
            $modelEzfom->attributes = $model->attributes;
            $modelEzfom->save();

            EzfForm::createZdata($model->ezf_id);
            $modelFields = EzformFields::findOne(['ezf_id' => $model->ezf_id, 'ezf_field_name' => 'id']);
            if (!$modelFields) {
                EzfForm::initEzfField($model->ezf_id, ['id']);
            }
        } else {
            $model->save();
        }
    }

    /**
     * Function checkEzfFormRight.
     * @param string $id ezf_id เช่น '1502169443023044700'
     * @param string $user_id 
     * @param string $action 'view','update','delete' ส่งมาเพียง action เดียว
     */
    public static function checkEzfFormRight($id, $user_id, $action) {
        $result = FALSE;
        if (empty($id) || empty($user_id) || empty($action)) {
            return $result;
        }
        $right = EzfQuery::getRightEzform($id, $user_id);

        if ($action == 'view') {
            if ($right['shared'] == '1') {
                $result = TRUE;
            } elseif ($right['shared'] == '0' && !empty($right['ezform'])) {
                $result = TRUE;
            } elseif (!empty($right['codev']) || !empty($right['assign'])) {
                $result = TRUE;
            } elseif ($right['shared'] == '3') {
                //check in site
            }
        } elseif ($action == 'update') {
            if (!empty($right['ezform']) || !empty($right['codev'])) {
                $result = TRUE;
            }
        } elseif ($action == 'delete') {
            if (!empty($right['ezform'])) {
                $result = TRUE;
            }
        }
        if ($result === FALSE) {
            Yii::$app->session->setFlash('alert', [
                'body' => SDHtml::getMsgError() . Yii::t('ezform', 'You do not have the right to {action} form.', ['action'=> $action]),
                'options' => ['class' => 'alert-danger']
            ]);
            Yii::$app->controller->redirect(\yii\helpers\Url::to('/ezforms2/ezform'));
        }
    }
    
    public static function getDepartment() {
        return ArrayHelper::map(EzfQuery::getDepartment(), 'sect_code', 'sect_name');
    }

}

?>