<?php
namespace backend\modules\ezforms2\classes;

use Yii;
use backend\modules\ezforms2\models\Ezform;
use backend\modules\ezforms2\models\EzformFields;

/**
 * OvccaQuery class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class EzfQuery {
    public static function getIntUserAll() {
	$sql = "SELECT user_id as id, 
		    CONCAT(firstname, ' ', lastname) AS text
		FROM profile
		";
	
	return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
    public static function getInputv2All() {
	$sql = "SELECT *
		FROM ezform_input
		WHERE input_version='v2' AND input_active=1
		ORDER BY input_order
		";
	
	return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
    public static function getFieldsCountById($id) {
	$sql = "SELECT MAX(ezf_field_order)+1 AS num
		FROM ezform_fields
		WHERE ezf_id=:id AND ezf_field_ref IS NULL AND ezf_field_type<>0
		";
	$order = Yii::$app->db->createCommand($sql, [':id'=>$id])->queryScalar();
	return isset($order)?$order:1;
    }
    
    public static function getEzformById($id) {
	
	$sql = "SELECT ezf_id, ezf_name, ezf_table, comp_id_target, field_detail, unique_record FROM ezform WHERE ezf_id = :id";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$id])->queryOne();
    }
    
    public static function getConditionFieldsName($field, $cond) {
	$sql = "SELECT $field
		FROM ezform_fields 
		WHERE ezform_fields.ezf_field_id in($cond) ";
	
	if($cond!=''){
            $data = Yii::$app->db->createCommand($sql)->queryAll();
            if($data){
                return $data;
            }
	}
	return [];
    }
    
    public static function deleteFieldOther($ezf_field_id) {
	$sql = "DELETE FROM `ezform_fields` WHERE `ezf_field_ref` = :id ";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$ezf_field_id])->execute();
    }
    
    public static function deleteChoice($ezf_field_id) {
	$sql = "DELETE FROM `ezform_choice` WHERE `ezf_field_id` = :id ";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$ezf_field_id])->execute();
    }
    
    
    
    public static function getCondition($ezf_id, $ezf_field_name) {
	$sql = "SELECT *
		FROM ezform_condition
		WHERE ezform_condition.ezf_id = :ezf_id AND ezform_condition.ezf_field_name = :ezf_field_name
		ORDER BY ezform_condition.cond_id;";

	return Yii::$app->db->createCommand($sql, [':ezf_id'=>$ezf_id, ':ezf_field_name'=>$ezf_field_name])->queryAll();
    }
    
    public static function getEzformReportById($id) {
	
	$sql = "SELECT ezform.ezf_id, ezform.ezf_name, ezf_table, comp_id_target, field_detail, unique_record , ezform_config.*
		FROM ezform INNER JOIN ezform_config ON ezform_config.ezf_id = ezform.ezf_id
		WHERE ezform.ezf_id = :id AND ezform_config.config_type = 'report' ";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$id])->queryOne();
    }
    
    public static function getEzformReportByIdAll($id) {
	
	$sql = "SELECT ezform_config.*
		FROM ezform_config
		WHERE ezform_config.ezf_id = :id AND ezform_config.config_type = 'report' ";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$id])->queryAll();
    }
    
    public static function getEzformReportByUser($user_id, $ezf_id) {
	
	$sql = "SELECT ezform_config.*
		FROM ezform_report INNER JOIN ezform_config ON ezform_config.config_id = ezform_report.config_id
		WHERE ezform_report.ezf_id = :id AND ezform_report.user_id = :user_id ";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$ezf_id, ':user_id'=>$user_id])->queryAll();
    }
    
    public static function getProvince(){
        $sql = "SELECT `PROVINCE_ID`, `PROVINCE_CODE`,`PROVINCE_NAME` FROM `const_province`";
        return Yii::$app->db->createCommand($sql)->queryAll();
    }

    public static function getFormTableName($ezf_id) {
        $ezform = Ezform::find()
                ->select('ezf_table, ezf_name, unique_record')
                ->where(['ezf_id' => $ezf_id])
                ->one();
        return $ezform;
    }
    
    public static function getDynamicFormById($table, $id) {
        $sql = "SELECT *
		FROM $table
		WHERE id = :id ";

        return Yii::$app->db->createCommand($sql, [':id' => $id])->queryOne();
    }
    
    public static function showColumn($table) {
	
	$sql = "SHOW COLUMNS FROM $table;";
	
	return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
    public static function copyTable($tableNew, $tableCopy) {
        $sql = "CREATE TABLE $tableNew LIKE $tableCopy";

        return Yii::$app->db->createCommand($sql)->execute();
    }
    
    public static function dropTable($table) {
        $sql = "DROP TABLE `$table`";

        return Yii::$app->db->createCommand($sql)->execute();
    }

     public static function getEzformAll($ezf_id) {
         $model = Ezform::find()->where('ezf_id<>:ezf_id', [':ezf_id'=>$ezf_id])
                 ->andWhere('ezform.status = :status', [':status' => 1])
                 ->all();
         return $model;
     }
     
     public static function getEzformCoDevAll() {
         $model = Ezform::find()
                    ->where('created_by=:created_by AND status = 1', [':created_by' => Yii::$app->user->id])
                    ->orWhere('ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id)', [':user_id' => Yii::$app->user->id])
                    ->orderBy('created_at DESC')
                    ->all();
         return $model;
     }
     
     public static function getEzformCoDev($ezf_id) {
         $model = Ezform::find()
                    ->where('created_by=:created_by AND ezf_id<>:ezf_id AND status = 1', [':created_by' => Yii::$app->user->id, ':ezf_id'=>$ezf_id])
                    ->orWhere('ezf_id in (SELECT ezf_id FROM ezform_co_dev WHERE user_co = :user_id AND ezf_id<>:ezf_id)', [':user_id' => Yii::$app->user->id, ':ezf_id'=>$ezf_id])
                    ->orderBy('created_at DESC')
                    ->all();
         return $model;
     }
     
     public static function getEzformOne($ezf_id) {
         $model = Ezform::find()->where('ezf_id=:ezf_id', [':ezf_id'=>$ezf_id])->one();
         
         return $model;
     }
     
     public static function getEzformWithField($ezf_field_id) {
         $sql = "SELECT
            `ezform_fields`.*,
            `ezform`.`ezf_version`,
            `ezform`.`ezf_name`,
            `ezform`.`ezf_detail`,
            `ezform`.`xsourcex`,
            `ezform`.`ezf_table`,
            `ezform`.`status`,
            `ezform`.`shared`,
            `ezform`.`public_listview`,
            `ezform`.`public_edit`,
            `ezform`.`public_delete`,
            `ezform`.`co_dev`,
            `ezform`.`assign`,
            `ezform`.`category_id`,
            `ezform`.`field_detail`,
            `ezform`.`ezf_sql`,
            `ezform`.`ezf_js`,
            `ezform`.`ezf_error`,
            `ezform`.`query_tools`,
            `ezform`.`unique_record`,
            `ezform`.`consult_tools`,
            `ezform`.`consult_users`,
            `ezform`.`consult_telegram`,
            `ezform`.`ezf_options`
            FROM `ezform`
            INNER JOIN `ezform_fields` ON `ezform`.`ezf_id` = `ezform_fields`.`ezf_id`
            WHERE ezf_field_id=:ezf_field_id
            ";
	
	return Yii::$app->db->createCommand($sql, [':ezf_field_id'=>$ezf_field_id])->queryOne();
     }
     
     public static function getEzformTargetField($ezf_field_id) {
         $sql = "SELECT
            `ezform_fields`.*,
            `ezform`.`ezf_version`,
            `ezform`.`ezf_name`,
            `ezform`.`ezf_detail`,
            `ezform`.`xsourcex`,
            `ezform`.`ezf_table`,
            `ezform`.`status`,
            `ezform`.`shared`,
            `ezform`.`public_listview`,
            `ezform`.`public_edit`,
            `ezform`.`public_delete`,
            `ezform`.`co_dev`,
            `ezform`.`assign`,
            `ezform`.`category_id`,
            `ezform`.`field_detail`,
            `ezform`.`ezf_sql`,
            `ezform`.`ezf_js`,
            `ezform`.`ezf_error`,
            `ezform`.`query_tools`,
            `ezform`.`unique_record`,
            `ezform`.`consult_tools`,
            `ezform`.`consult_users`,
            `ezform`.`consult_telegram`,
            `ezform`.`created_by` as user_by,
            `ezform`.`ezf_options`
            FROM `ezform`
            INNER JOIN `ezform_fields` ON `ezform`.`ezf_id` = `ezform_fields`.`ref_ezf_id`
            WHERE ezf_field_id=:ezf_field_id
            ";
	
	return Yii::$app->db->createCommand($sql, [':ezf_field_id'=>$ezf_field_id])->queryOne();
     }
     
     public static function getPtid($table, $target) {
        try {
            $data = Yii::$app->db->createCommand("SELECT ptid, sitecode, ptcode, ptcodefull FROM `" . $table . "` WHERE id = :id;", [':id' => $target])->queryOne();
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            $data = [];
        }
        return $data;
    }

    public static function findTable($table) {
        $sql = "SELECT TABLE_NAME FROM information_schema.TABLES 
            WHERE TABLE_NAME = :table";

        return Yii::$app->db->createCommand($sql, [':table' => $table])->queryOne();
    }

    public static function getRightEzform($ezf_id, $user_id) {
        $sql = "SELECT
            (SELECT COUNT(created_by) FROM ezform WHERE ezf_id = :ezf_id AND created_by=:user_id) AS ezform,
            (SELECT COUNT(user_co) FROM ezform_co_dev WHERE ezf_id = :ezf_id AND user_co=:user_id) AS codev,
            (SELECT COUNT(user_id) FROM ezform_assign WHERE ezf_id = :ezf_id AND user_id=:user_id) AS assign,
            (SELECT shared FROM ezform WHERE ezf_id = :ezf_id) AS shared";

        return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':user_id' => $user_id])->queryOne();
    }

    public static function insertEzformCoDev($ezf_id, $user_id) {
        $sql = "INSERT IGNORE ezform_co_dev VALUES(:id,:user_id,1)";
        return Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':user_id' => $user_id])->execute();
    }

    public static function insertEzformAssign($ezf_id, $user_id) {
        $sql = "INSERT IGNORE ezform_assign VALUES(:id,:user_id,1)";
        return Yii::$app->db->createCommand($sql, [':id' => $ezf_id, ':user_id' => $user_id])->execute();
    }

    public static function getTarget($table, $id) {
        if($id==''){
            return false;
        }
        
	$sql = "SELECT *
		FROM $table
                WHERE id = :id AND rstat not in(0, 3)
		";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$id])->queryOne();
    }
    
    public static function getTargetNotRstat($table, $id) {
        if($id==''){
            return false;
        }
        
	$sql = "SELECT *
		FROM $table
                WHERE id = :id AND rstat not in(3)
		";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$id])->queryOne();
    }
    
    public static function getMaxCodeBySitecode($table, $hsitecode) {
        $maxcode = Yii::$app->db->createCommand("SELECT MAX(CAST(hptcode AS UNSIGNED)) AS ptcode FROM $table WHERE xsourcex = :hsitecode ORDER BY id DESC ", [':hsitecode'=>$hsitecode])->queryScalar();
        $hptcode = str_pad($maxcode+1, 5, '0', STR_PAD_LEFT);
	return $hptcode;
    }
    
    public static function getMaxCode($table) {
        $maxcode = Yii::$app->db->createCommand("SELECT MAX(CAST(hptcode AS UNSIGNED)) AS ptcode FROM $table ORDER BY id DESC ")->queryScalar();
        $hptcode = str_pad($maxcode+1, 5, '0', STR_PAD_LEFT);
	return $hptcode;
    }
    
    public static function getEzfSpecial($ezf_id) {
        $modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id AND ezf_special=1', [':ezf_id' => $ezf_id])
                    ->orderBy(['ezf_field_order' => SORT_ASC])
                    ->all();
            return $modelFields;
    }
    
    public static function checkCidAll($table, $field, $cid) {
	$sql = "SELECT * FROM $table WHERE rstat not in(0,3) AND $field = :cid";
	return Yii::$app->db->createCommand($sql, [':cid'=>$cid])->queryAll();
    }
    
    public static function getFieldById($ezf_field_id) {
	$modelFields = EzformFields::find()
                    ->where('ezf_field_id = :ezf_field_id', [':ezf_field_id'=>$ezf_field_id])
                    ->one();
            return $modelFields;
    }
    
    public static function getRefFieldById($ezf_field_id) {
	$sql = "SELECT
                `ezform`.`ezf_id`,
                `ezform`.`ezf_name`,
                `ezform`.`ezf_table`,
                `ezform_fields`.*
                FROM
                `ezform`
                INNER JOIN `ezform_fields`
                ON `ezform`.`ezf_id` = `ezform_fields`.`ref_ezf_id`
                WHERE
                `ezform_fields`.`ezf_field_id`= :ezf_field_id";
        
            return Yii::$app->db->createCommand($sql, [':ezf_field_id'=>$ezf_field_id])->queryOne();
    }

    public static function getRefFieldByName($ezf_id, $varname) {
	$sql = "SELECT
                `ezform`.`ezf_id`,
                `ezform`.`ezf_name`,
                `ezform`.`ezf_table`,
                `ezform_fields`.`ref_ezf_id`,
                `ezform_fields`.`ref_field_id`
                FROM
                `ezform`
                INNER JOIN `ezform_fields`
                ON `ezform`.`ezf_id` = `ezform_fields`.`ref_ezf_id`
                INNER JOIN `ezform_fields` AS `efa1`
                ON `ezform`.`ezf_id` = `efa1`.`ezf_id`
                WHERE
                `ezform_fields`.`ref_ezf_id` = :ezf_id AND `ezform_fields`.`ref_field_id`=:ezf_field_name AND `efa1`.`ezf_field_name`= :ezf_field_name";
        
            return Yii::$app->db->createCommand($sql, [':ezf_id' => $ezf_id, ':ezf_field_name'=>$varname])->queryOne();
    }
    
    public static function getFieldByName($ezf_id, $varname) {
	$modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id AND ezf_field_name=:ezf_field_name', [':ezf_id' => $ezf_id, ':ezf_field_name'=>$varname])
                    ->one();
            return $modelFields;
    }
    
    public static function getTargetOne($ezf_id) {
	$modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id AND ezf_target=1', [':ezf_id' => $ezf_id])
                    ->one();
            return $modelFields;
    }
    
    public static function findSpecialOne($ezf_id) {
	$modelFields = EzformFields::find()
                    ->select('ef2.*')
                    ->innerJoin('ezform_fields ef2', 'ezform_fields.parent_ezf_id=ef2.ezf_id')
                    ->where('ezform_fields.ezf_id = :ezf_id AND ef2.ezf_special=1', [':ezf_id' => $ezf_id])
                    ->one();
            return $modelFields;
    }
    
    public static function getSpecialOne($ezf_id) {
	$modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id AND ezf_special=1', [':ezf_id' => $ezf_id])
                    ->one();
            return $modelFields;
    }
    
    public static function getEventFields($ezf_id) {
	$modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id AND (ezf_special=1 OR ezf_target=1)', [':ezf_id' => $ezf_id])
                    ->all();
            return $modelFields;
    }
    
    public static function checkEventFields($ezf_id, $ezf_field_id) {
	$modelFields = EzformFields::find()
                    ->where('ezf_id = :ezf_id AND ezf_field_id <> :ezf_field_id AND (ezf_special=1 OR ezf_target=1)', [':ezf_id' => $ezf_id, ':ezf_field_id' => $ezf_field_id])
                    ->one();
            return $modelFields;
    }
    
    public static function setParentFields($ezf_id, $parentId) {
        $countLv = 0;
	$modelFields = EzformFields::find()
                    ->where('`ezf_target`=1 AND `ref_ezf_id`=:ezf_id', [':ezf_id' => $ezf_id])
                    ->one();
        
        if($modelFields){
            $modelFields->parent_ezf_id = $parentId;
            $modelFields->save();
            $countLv++; 
            
            self::setParentFields($modelFields['ezf_id'], $parentId);
        }
        
        return $countLv;
    }

    public static function getDepartment(){
        $sql = "SELECT sect_code,sect_name FROM dept_sect";
	return Yii::$app->db->createCommand($sql)->queryAll();
    }
    
    public static function getRefFields($ezf_field_ref) {
	$sql = "SELECT
            `ezform`.`ezf_name`,
            `ezform`.`ezf_table`,
            `ezform`.`ezf_id`,
            `ezform_fields`.`ezf_field_name`,
            `ezform_fields`.`ezf_field_label`,
            `ezform_fields`.`ezf_field_ref`
            FROM
            `ezform`
            JOIN `ezform_fields`
            ON `ezform`.`ezf_id` = `ezform_fields`.`ezf_id`
            WHERE
            `ezform_fields`.`ezf_field_ref` = :ezf_field_ref
		";
	
	return Yii::$app->db->createCommand($sql, [':ezf_field_ref'=>$ezf_field_ref])->queryAll();
    }
    
    public static function builderSql($select, $table, $where, $params=[]) {
        $query = new \yii\db\Query();
        $query->select($select);
        $query->from($table);
        $query->where($where, $params);

        return $query;
    }
    
    public static function builderSqlGetScalar($select, $table, $where, $params=[]) {
        $query = self::builderSql($select, $table, $where, $params);

        return $query->createCommand()->queryScalar();
    }
    
    public static function builderSqlGetOne($select, $table, $where, $params=[]) {
        $query = self::builderSql($select, $table, $where, $params);

        return $query->createCommand()->queryOne();
    }
    
    public static function builderSqlGetAll($select, $table, $where, $params=[]) {
        $query = self::builderSql($select, $table, $where, $params);

        return $query->createCommand()->queryAll();
    }
}
