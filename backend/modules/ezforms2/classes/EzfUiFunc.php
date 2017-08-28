<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use backend\modules\ezforms2\models\TbdataAll;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezforms2\models\EzformTarget;
use appxq\sdii\utils\SDUtility;

/**
 * Description of EzfUiFunc
 *
 * @author appxq
 */
class EzfUiFunc {

    public static function loadUniqueRecord($model, $ezf_table, $target) {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);
            $strWhere = '';
            $params = [':target' => $target];
            if ($model->id) {
                $strWhere = 'id<>:id';
                $params[':id'] = $model->id;
            }
            $modelSave = $modelSave->find()->where('target=:target AND rstat not in(0,3) ' . $strWhere, $params)->one();

            if ($modelSave) {
                return $modelSave;
            }

            return false;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }

    public static function loadNewRecord($model, $ezf_table, $userid) {
        try {
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table);

            $modelSave = $modelSave->find()->where('user_create=:userid AND rstat = 0', [':userid' => $userid])->one();
            if (!$modelSave) {
                return false;
            }

            $model->attributes = $modelSave->attributes;
            $model->afterFind();

            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return false;
        }
    }

    public static function loadTbData($ezf_table, $dataid) {
        try {
            $model = new TbdataAll();
            $model->setTableName($ezf_table);

            $model = $model->find()->where('id=:id AND rstat not in(3)', [':id' => $dataid])->one();
            if (!$model) {
                return FALSE;
            }
            return $model;
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return FALSE;
        }
    }

    public static function loadData($model, $ezf_table, $dataid = '', $special = false) {
        if ($dataid != '') {

            $modelSave = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if (!$modelSave) {
                return FALSE;
            }

            $model->attributes = $modelSave->attributes;

            $model->afterFind();
        } else {

            $model->init();
        }

        return $model;
    }

    public static function saveData($model, $ezf_table, $ezf_id, $dataid = '') {
        try {
            $insert = true;

            //load
            $modelSave = new TbdataAll();
            $modelSave->setTableName($ezf_table); //$modelEzf->ezf_table

            if ($dataid != '') {
                $modelTbData = EzfUiFunc::loadTbData($ezf_table, $dataid);
                if ($modelTbData) {
                    $modelSave = $modelTbData;
                    $insert = false;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                        'data' => $dataid,
                    ];
                    return $result;
                }
            }

            //Save()
            $model->beforeSave($insert);
            $modelSave->attributes = $model->attributes;
            //\appxq\sdii\utils\VarDumper::dump($modelSave->attributes);
            $result = $modelSave->save();

            $model->afterSave($insert, $modelSave->attributes);

            if ($result) {
                self::saveTarget($model, $ezf_id);
                self::saveLog($model, $ezf_id);

                $result = [
                    'status' => 'success',
                    'action' => $insert ? 'create' : 'update',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Save completed.'),
                    'data' => $modelSave->attributes,
                ];
                return $result;
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not save the data.'),
                    'data' => $modelSave->attributes,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error') . ' ' . $e->getMessage(),
            ];
            return $result;
        }
    }

    public static function deleteDataRstat($model, $ezf_table, $ezf_id, $dataid, $reloadDiv = '') {
        try {
            $modelSave = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($modelSave) {
                $modelSave->rstat = 3;

                $model->attributes = $modelSave->attributes; //ส่งค่าให้กับ event
                $model->beforeSave(FALSE);
                $result = $modelSave->save();
                $model->afterSave(FALSE, $modelSave->attributes);

                if ($result) {
                    self::saveTarget($model, $ezf_id);

                    $result = [
                        'status' => 'success',
                        'action' => 'delete',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                        'data' => $modelSave->attributes,
                        'reloadDiv' => $reloadDiv,
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                        'data' => $modelSave->attributes,
                    ];
                    return $result;
                }
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                    'data' => $dataid,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error'),
            ];
            return $result;
        }
    }

    public static function deleteData($model, $ezf_table, $ezf_id, $dataid, $reloadDiv = '') {
        try {
            $modelSave = EzfUiFunc::loadTbData($ezf_table, $dataid);
            if ($modelSave) {
                $model->attributes = $modelSave->attributes; //ส่งค่าให้กับ event
                $model->beforeDelete();
                $result = $modelSave->delete();
                $model->afterDelete();

                if ($result) {
                    self::deleteTarget($model, $ezf_id);

                    $result = [
                        'status' => 'success',
                        'action' => 'delete',
                        'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Delete completed.'),
                        'data' => $modelSave->attributes,
                        'reloadDiv' => $reloadDiv,
                    ];
                    return $result;
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                        'data' => $modelSave->attributes,
                    ];
                    return $result;
                }
            } else {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'No results found.'),
                    'data' => $dataid,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Database error'),
            ];
            return $result;
        }
    }

    public static function modelSearch($model, $ezform, $targetField, $colSearch, $params) {
        //$model = new TbdataAll();

        $query = $model->find()->where('rstat not in(0,3)'); //->where('rstat not in(0, 3)');
        $modelEvent = EzfQuery::getEventFields($ezform->ezf_id);
        $modelFields;
        if ($modelEvent) {
            foreach ($modelEvent as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $modelFields = EzfQuery::findSpecialOne($ezform->ezf_id);
                } elseif ($value['ezf_special'] == 1) {
                    $modelFields = true;
                }
            }
        }



        if (isset($modelFields)) {
            $query->andWhere('xsourcex = :site', [':site' => Yii::$app->user->identity->profile->sitecode]);
        }

        if ($ezform['public_listview'] != 1) {
            $showStatus = self::showListDataEzf($ezform, Yii::$app->user->id);

            $query->andWhere("user_create=:created_by || $showStatus", [':created_by' => Yii::$app->user->id]);
        }

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
//            'sort' => [
//                'route' => '/ezforms2/fileinput/grid-update',
//            ]
        ]);

        $model->load($params);

        if ($targetField) {
            $query->andFilterWhere(['like', $targetField['ezf_field_name'], $model[$targetField['ezf_field_name']]]);
        }

        if (isset($colSearch) && empty($colSearch)) {
            $colSearch = ['id', 'sitecode', 'ptid', 'target', 'xsourcex', 'ptcode', 'hptcode', 'hsitecode', 'rstat'];
        }
//        $query->andFilterWhere([
//            'id' => $model->id,
//        ]);

        foreach ($colSearch as $field) {
            if (is_array($field)) {
                if (isset($field['attribute'])) {
                    $query->andFilterWhere(['like', $field, $model[$field['attribute']]]);
                }
            } else {
                $query->andFilterWhere(['like', $field, $model[$field]]);
            }
        }


        return $dataProvider;
    }

    public static function modelEmrSearch($model, $target, $ezf_id, $params, $showall = 0) {
        //$model = new EzformTarget();

        $query = $model->find()->where('ezform_target.rstat not in(0,3)'); //->where('rstat not in(0, 3)');

        if (isset($target) && $target != '') {
            $query->andWhere('ezform_target.target_id = :target', [':target' => $target]);
        }

        if (!$showall) {
            $query->andWhere('ezform_target.ezf_id = :ezf_id', [':ezf_id' => $ezf_id]);
        }

        $query->innerJoin('profile', 'profile.user_id = ezform_target.user_update');
        $query->innerJoin('ezform', 'ezform.ezf_id = ezform_target.ezf_id');

        $query->select([
            'ezform_target.*',
            'ezform.ezf_name',
            'ezform.ezf_table',
            'ezform.co_dev',
            'ezform.assign',
            'ezform.public_listview',
            'ezform.public_edit',
            'ezform.public_delete',
            "(SELECT IFNULL(field_detail,'') AS field_detail FROM ezform ezf WHERE ezf.ezf_id = ezform_target.ezf_id ) AS ezf_detail",
            'ezform_target.xsourcex AS sitename',
            "concat(profile.firstname, ' ', profile.lastname) AS userby"
        ]);

        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            //'route' => '/ezforms2/fileinput/grid-update',
            ],
//            'sort' => [
//                'route' => '/ezforms2/fileinput/grid-update',
//            ]
        ]);

        $model->load($params);

        $query->andFilterWhere(['like', 'ezform_target.ezf_id', $model->ezf_id]);
        $query->andFilterWhere(['like', 'ezform_target.user_update', $model->user_update]);
        $query->andFilterWhere(['like', 'ezform_target.xsourcex', $model->xsourcex]);
        $query->andFilterWhere(['like', 'ezform_target.rstat', $model->rstat]);

        return $dataProvider;
    }

    public static function setSystemProperty($model, $target, $dataTarget, $tableForm, $fieldTarget, $fieldSpecial, $special, $userProfile, $rstat) {
        $userid = $userProfile['user_id'];
        $hsitecode = $userProfile['sitecode'];
        $xsourcex = $userProfile['sitecode'];
        $department = $userProfile['department'];
        $id = $model->id;

        $insert = true;
        if (isset($id) && !empty($id)) {
            $insert = false;
        } else {
            $id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        }

        $ptid = $id;
        $sitecode = $hsitecode;
        $hptcode = '';
        $ptcode = $hptcode;
        if ($target != '') {
            $hptcode = $dataTarget['hptcode'];
            $hsitecode = $dataTarget['hsitecode'];
            //ถ้าลบออกไซต์อื่นแก้ไข จะมีค่าที่ต่างกัน
            $xsourcex = $dataTarget['xsourcex'];
            $department = $dataTarget['xdepartmentx'];
        } elseif ($target == '' && $insert) {
            $hptcode = EzfQuery::getMaxCodeBySitecode($tableForm, $hsitecode);

            $ptcode = $hptcode;
            if (isset($model->ptid) && !empty($model->ptid)) {
                $ptid = $model->ptid;
                $sitecode = $model->sitecode;
                $ptcode = $model->ptcode;
                $ptcodefull = $model->ptcodefull;
            }
        } elseif ($target == '' && !$insert) {
            $hptcode = $model->hptcode;
            $hsitecode = $model->hsitecode;
            $ptid = $model->ptid;
            $sitecode = $model->sitecode;
            $ptcode = $model->ptcode;
            $ptcodefull = $model->ptcodefull;
            //ถ้าลบออกไซต์อื่นแก้ไข จะมีค่าที่ต่างกัน
            $xsourcex = $model->xsourcex;
            $department = $model->xdepartmentx;
        }

        $ptcodefull = $ptcode . $sitecode;

        $modelSystem = [
            'id' => $id,
            'ptid' => isset($dataTarget['ptid']) ? $dataTarget['ptid'] : $ptid,
            'sitecode' => isset($dataTarget['sitecode']) ? $dataTarget['sitecode'] : $sitecode,
            'ptcode' => isset($dataTarget['ptcode']) ? $dataTarget['ptcode'] : $ptcode,
            'ptcodefull' => isset($dataTarget['ptcodefull']) ? $dataTarget['ptcodefull'] : $ptcodefull,
            'target' => $target != '' ? $target : $id,
            'hptcode' => $hptcode,
            'hsitecode' => $hsitecode,
            'xsourcex' => $xsourcex,
            'xdepartmentx' => $department,
            'user_update' => $userid,
            'update_date' => new \yii\db\Expression('NOW()'),
        ];

        if (isset($fieldTarget) && $fieldTarget != '') {
            $modelSystem[$fieldTarget] = $target != '' ? $target : $id;
        }

        if (isset($fieldSpecial) && $fieldSpecial != '') {
            $modelSystem[$fieldSpecial] = $model[$fieldSpecial];
        }

        if ($insert) {
            $modelSystem['error'] = NULL;
            $modelSystem['rstat'] = $rstat;
            $modelSystem['user_create'] = $userid;
            $modelSystem['create_date'] = new \yii\db\Expression('NOW()');
            $r = Yii::$app->db->createCommand()->insert($tableForm, $modelSystem)->execute();
        } else {
            $r = Yii::$app->db->createCommand()->update($tableForm, $modelSystem, 'id=:id', [':id' => $id])->execute();
        }

        return $modelSystem;
    }

    public static function saveTarget($model, $ezf_id) {
        $modelTarget = EzformTarget::find()->where('ezf_id=:ezf_id AND data_id=:data_id AND target_id=:target_id', [':ezf_id' => $ezf_id, ':data_id' => $model->id, ':target_id' => $model->target])->one();
        if (!$modelTarget) {
            $modelTarget = new EzformTarget();
        }
        $modelTarget->ezf_id = $ezf_id;
        $modelTarget->data_id = $model->id;
        $modelTarget->target_id = $model->target;
        $modelTarget->ptid = $model->ptid;
        $modelTarget->user_create = $model->user_create;
        $modelTarget->create_date = $model->create_date;
        $modelTarget->user_update = $model->user_update;
        $modelTarget->update_date = $model->update_date;
        $modelTarget->rstat = $model->rstat;
        $modelTarget->xsourcex = $model->xsourcex;

        return $modelTarget->save();
    }

    public static function saveLog($model, $ezf_id) {
        $modelLog = new \backend\modules\ezforms2\models\EzformLog();

        $modelLog->id = SDUtility::getMillisecTime();
        $modelLog->ezf_id = $ezf_id;
        $modelLog->data_id = $model->id;
        $modelLog->user_id = $model->user_update;
        $modelLog->create_date = $model->update_date;
        $modelLog->sql_log = SDUtility::array2String($model->attributes);
        $modelLog->rstat = $model->rstat;
        $modelLog->xsourcex = $model->xsourcex;

        return $modelLog->save();
    }

    public static function deleteTarget($model, $ezf_id) {
        $modelTarget = EzformTarget::find()->where('ezf_id=:ezf_id AND data_id=:data_id', [':ezf_id' => $ezf_id, ':data_id' => $model->id])->one();
        if ($modelTarget) {
            return $modelTarget->delete();
        }
        return false;
    }

    public static function showListDataEzf($ezform, $user_id) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        return ($ezform['public_listview'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    public static function showViewDataEzf($ezform, $user_id, $created_by) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        return ($created_by == $user_id || $ezform['public_listview'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    public static function showDeleteDataEzf($ezform, $user_id, $created_by) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        return ($created_by == $user_id || $ezform['public_delete'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    public static function showEditDataEzf($ezform, $user_id, $created_by) {
        $codev = SDUtility::string2Array($ezform['co_dev']);
        $assign = SDUtility::string2Array($ezform['assign']);
        return ($created_by == $user_id || $ezform['public_edit'] == 1 || in_array($user_id, $codev) || in_array($user_id, $assign)) ? 1 : 0;
    }

    public function backgroundInsert($ezf_id, $target, $initdata = []) {
        $dataid = '';
        
        $modelEzf = EzfQuery::getEzformOne($ezf_id);
        Yii::$app->session['show_varname'] = 0;
        Yii::$app->session['ezf_input'] = EzfQuery::getInputv2All();
        $userProfile = Yii::$app->user->identity->profile;

        $modelFields = \backend\modules\ezforms2\models\EzformFields::find()
                ->where('ezf_id = :ezf_id', [':ezf_id' => $modelEzf->ezf_id])
                ->orderBy(['ezf_field_order' => SORT_ASC])
                ->all();

        $model = EzfFunc::setDynamicModel($modelFields, $modelEzf->ezf_table, Yii::$app->session['ezf_input'], Yii::$app->session['show_varname']);
        $model = EzfUiFunc::loadData($model, $modelEzf->ezf_table, $dataid);

        $targetReset = false;
        if (!isset($model->id)) {// ถ้ามี new record ที่คนนั้นสร้างไว้ ให้ใช้อันนั้น
            $modelNewRecord = EzfUiFunc::loadNewRecord($model, $modelEzf->ezf_table, $userProfile->user_id);

            if ($modelNewRecord) {
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
            if ($targetReset) {
                $model[$evenFields['target']['ezf_field_name']] = '';
            }

            $modelEzfTarget = EzfQuery::getEzformOne($evenFields['target']['ref_ezf_id']);
            $target = ($target == '') ? $model[$evenFields['target']['ezf_field_name']] : $target;
            $dataTarget = EzfQuery::getTargetNotRstat($modelEzfTarget->ezf_table, $target);

            if ($dataTarget) {//เลือกเป้าหมายแล้ว
                if (isset($modelEzf['unique_record']) && $modelEzf['unique_record'] == 2) {
                    $unique = EzfUiFunc::loadUniqueRecord($model, $modelEzf->ezf_table, $target);
                    //\appxq\sdii\utils\VarDumper::dump($unique);
                    if ($unique) {
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
                return false;
            }
        } else {// ไม่มีเป้าหมาย
            $fieldSpecial = EzfFunc::checkSpecial($model, $evenFields, $targetReset);

            if ($model->id) {
                $dataTarget = EzfQuery::getTarget($modelEzf->ezf_table, $model->id);
            } else {
                $dataTarget = [];
            }
            
            //เพิ่มและแก้ไขข้อมูล system
            $model->attributes = EzfUiFunc::setSystemProperty($model, $target, $dataTarget, $modelEzf->ezf_table, '', $fieldSpecial, $special, $userProfile, 0);
        }
        
        $model->rstat = 1;
        $model->user_update = $userProfile->user_id;
        $model->update_date = new \yii\db\Expression('NOW()');

        $result = EzfUiFunc::saveData($model, $modelEzf->ezf_table, $modelEzf->ezf_id, $model->id);

        return $result;
        
    }

}
