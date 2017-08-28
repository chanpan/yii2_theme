<?php
namespace backend\modules\ezbuilder\classes;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Yii;
use backend\modules\ezbuilder\classes\EzBuilderFunc;
use backend\modules\ezforms2\models\EzformFields;

/**
 * Description of EzBuilderQuery
 *
 * @author appxq
 */
class EzBuilderQuery {
    
    public static function deleteCondition($ezf_id, $ezf_field_name) {
	$sql = "DELETE FROM `ezform_condition` WHERE `ezf_id` = :id AND `ezf_field_name` = :name ";
	
	return Yii::$app->db->createCommand($sql, [':id'=>$ezf_id, ':name'=>$ezf_field_name])->execute();
    }
    
    public static function updateFields($obj, $dataEzf, $xy='') {
       
	$model = EzformFields::findOne(['ezf_field_id' => $obj['id']]);
        $oldNameOther = $model->ezf_field_name;
        
        $index = 0;
        $oldType = $model->ezf_field_type;
        $oldLength = $model->table_field_length;
        $oldIndex = $model->table_index;
                
        $type = self::getTypeTable($obj, $xy);
        $tableField = self::setTableField($type);
        $field_type = $tableField['type'];
        $field_length = $tableField['length'];
        
        $model->ezf_field_name = $obj['attribute'];
        $model->ezf_field_label = isset($obj['label'])?$obj['label']:$obj['suffix'];
        $model->ezf_field_type = 0;
        $model->table_field_type = $field_type;
        $model->table_field_length = $field_length;
        
                
        $alterTable = true;
        if($oldNameOther!=$model->ezf_field_name){
            $alterTable = EzBuilderFunc::alterTableChange($dataEzf['ezf_table'], $oldNameOther, $model->ezf_field_name, $model->table_field_type, $model->table_field_length, $index, $oldType, $oldLength, $oldIndex);
        }
        $action = $model->save();
	return $action && $alterTable;
    }
    
    public static function setTableField($type) {
        $field_length = 100;
        $field_type = 'VARCHAR';
        if($type=='textinput'){
            $field_length = 100;
        } elseif($type=='textarea'){
            $field_length = null;
            $field_type = 'TEXT';
        } elseif($type=='datetime'){
            $field_length = 20;
        } elseif($type=='checkbox'){
            $field_length = 1;
        } elseif($type=='id'){
            $field_length = 20;
        }
        
        return [
            'length'=>$field_length,
            'type'=>$field_type,
        ];
    }
    
    public static function getTypeTable($obj, $xy) {
        $type = isset($obj['type'])?$obj['type']:0;
        if ($xy!='') {
            if(isset($obj['header'])){
                foreach ($obj['header'] as $objkey => $objvalue) {
                    $col = explode('_', $xy);
                    if(isset($col[1]) && $col[1]==$objvalue['col']){
                        $type = $objvalue['type'];
                    }
                }
            } 
        }
        return $type;
    }
    
    public static function addFields($obj, $ezf_id, $ezf_field_id, $dataEzf, $xy='') {
        $type = self::getTypeTable($obj, $xy);
        $tableField = self::setTableField($type);
        $field_type = $tableField['type'];
        $field_length = $tableField['length'];
        
	$column = [
            'ezf_field_id'=> $obj['id'],
            'ezf_id'=>$ezf_id,
            'ezf_field_name'=>$obj['attribute'],
            'ezf_field_label'=>isset($obj['label'])?$obj['label']:$obj['suffix'],
            'ezf_field_type'=>0,
            'ezf_field_ref'=>$ezf_field_id,
            'ezf_field_lenght'=>12,
            'ezf_margin_col'=>0,
            'table_field_type'=>$field_type,
            'table_field_length'=>$field_length,
            'table_index'=>0,
            'ezf_field_required'=>0,
            'ezf_field_lenght'=>12,
        ];
        
        $create_field = EzBuilderFunc::systemCreateField($column);
        $alterTable = false;
        if($create_field){
            $alterTable = EzBuilderFunc::alterTableAdd($dataEzf['ezf_table'], $column['ezf_field_name'], $column['table_field_type'], $column['table_field_length'], 0);
        }
        return $alterTable;
    }
}
