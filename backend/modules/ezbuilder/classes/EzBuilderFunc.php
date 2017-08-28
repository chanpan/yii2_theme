<?php
namespace backend\modules\ezbuilder\classes;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Yii;
use yii\web\View;
use appxq\sdii\utils\SDUtility;
use backend\modules\ezforms2\models\EzformChoice;
use appxq\sdii\helpers\SDHtml;
use backend\modules\ezbuilder\classes\EzBuilderQuery;
use backend\modules\ezforms2\models\EzformFields;
use yii\helpers\Json;
use backend\modules\ezforms2\models\EzformCondition;
use yii\helpers\ArrayHelper;
use backend\modules\ezforms2\classes\EzfQuery;

/**
 * Description of EzBuilderFunc
 *
 * @author appxq
 */
class EzBuilderFunc {
    public static function createChildrenItem($field, $htmlInput) {
	$style_color = '';
	if ($field['ezf_field_color'] != '') {
	    $style_color = "background-color: {$field['ezf_field_color']};";
	}
	$view = new View();
        if(Yii::$app->getRequest()->isAjax){
            $view = Yii::$app->getView();
        }
        
	return $view->renderAjax('/../../ezbuilder/views/widgets/_dads_children', [
		    'field_id' => $field['ezf_field_id'],
		    'field_size' => $field['ezf_field_lenght'],
		    'field_order' => $field['ezf_field_order'],
		    'style_color' => $style_color,
		    'field_item' => $htmlInput,
	]);
    }
    
    public static function uploadOption($model, $pathFrom, $pathTo) {
        
        if (stristr($model['ezf_field_default'], 'tmp.png') == TRUE && $pathFrom!='' && $pathTo!='') {
            
            $fileName = $model['ezf_field_default'];
            $newFileName = $fileName;
            $nameEdit = false;
            
            if (stristr($fileName, 'tmp.png') == TRUE) {
                $nameEdit = true;
                $newFileName = SDUtility::getMillisecTime() . '.png';
                
                @copy(Yii::getAlias($pathFrom) . $fileName, Yii::getAlias($pathTo) . $newFileName);
                @unlink(Yii::getAlias($pathFrom) . $fileName);
            }
            
            $model['ezf_field_default'] = $newFileName;
            $modelTmp = EzformFields::find()->where(['ezf_field_id' => $model['ezf_field_id']])->one();

            if (isset($modelTmp['ezf_field_id'])) {
                $fileName = $modelTmp['ezf_field_default'];
                if ($nameEdit && $fileName != '') {
                    @unlink(Yii::getAlias($pathTo) . $fileName);
                }
            }
            return $newFileName;
        }

        return $model['ezf_field_default'];
    }
    
    public static function saveCondition($data) {
	$condArr = [];
	if (isset($data)) {
	    $condArr = Json::decode($data);

	    foreach ($condArr as $key => $value) {
		$model = EzformCondition::find()
				->where('ezf_id=:ezf_id AND ezf_field_name=:ezf_field_name AND ezf_field_value=:ezf_field_value', [':ezf_id' => $value['ezf_id'], ':ezf_field_name' => $value['ezf_field_name'], ':ezf_field_value' => $value['ezf_field_value']])
				->one();

		if ($model) {
			$model->cond_jump = Json::encode($value['cond_jump']);
			$model->cond_require = Json::encode($value['cond_require']);
		} else {
			$model = new EzformCondition();
			$model->ezf_id = (int)$value['ezf_id'];
			$model->ezf_field_name = (string)$value['ezf_field_name'];
			$model->ezf_field_value = (string)$value['ezf_field_value'];
			$model->cond_jump = ($value['cond_jump']!='')?Json::encode($value['cond_jump']):'';
			$model->cond_require = ($value['cond_require']!='')?Json::encode($value['cond_require']):'';
		}

		if ($model->isNewRecord) {
			if ($value['cond_jump'] != '' || $value['cond_require'] != '') {
				$action = $model->save();
			}
		} else {
			$action = $model->save();
		}
	    }
	}

    //unset($_COOKIE['gen_condition']);
    }
    
    public static function setEzfData($dataItems, $ezf_field_id, $ezf_id, $oldName , $newName, $dataType, $dataEzf) {
        
        if(isset($dataItems['condition'])){
            //\appxq\sdii\utils\VarDumper::dump($dataItems,1,0);
            self::saveCondition($dataItems['condition']);
	    unset($dataItems['condition']);
        }
            
	if(isset($dataItems['builder']) && !empty($dataItems['builder'])){
            
	    foreach ($dataItems['builder'] as $key => $value) {
                //\appxq\sdii\utils\VarDumper::dump($dataEzf);
		//set items
                
		if($dataType == 'radio'){
		    $dataItems['items']['data'][$value['value']] = $value['label'];
		    if(isset($value['other'])){
			$dataItems['items']['other'][$value['value']] = $value['other'];
		    }
		} elseif($dataType == 'fields'){
                    
                    if(isset($value['fields'])){
                        $last_xy;
                        
                        foreach ($value['fields'] as $xy => $obj){
                            $dataItems['fields'][$xy] = $obj;
                            $last_xy = $xy;
                        }
                        if(isset($value['other'])){
                            $dataItems['fields'][$last_xy]['other'] = $value['other'];
                        }
                    }
                } elseif($dataType == 'select') {
		    $dataItems['items'][$value['value']] = $value['label'];
		}
		
		//save items
                if($dataType == 'fields'){
                    if(is_array($value['fields'])){
                        foreach ($value['fields'] as $xy => $obj) {
                            if($obj['action']=='update'){
                                $updateField = EzBuilderQuery::updateFields($obj, $dataEzf, $xy);
                                if(!$updateField){
                                    $result = [
                                        'status' => 'error',
                                        'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Column <code>{attribute}</code> correction failed.', ['attribute'=>$obj['attribute']]),
                                    ];
                                    return $result;
                                }
                            } else {
                                $createField = EzBuilderQuery::addFields($obj, $ezf_id, $ezf_field_id, $dataEzf, $xy);
                                if(!$createField){
                                    $result = [
                                        'status' => 'error',
                                        'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Column <code>{attribute}</code> creation failed.', ['attribute'=>$obj['attribute']]),
                                    ];
                                    return $result;
                                }
                            }
                            
                            $dataItems['builder'][$key]['fields'][$xy]['action'] = 'update';
                            
                            if(isset($obj['data']) && is_array($obj['data'])){
                                foreach ($obj['data'] as $key_item => $value_item) {
                                    if($value_item['action']=='update'){
                                        $model_item = EzformChoice::find()->where('ezf_choice_id=:id', [':id'=>$key_item])->one();
                                        $model_item->ezf_id = $ezf_id;
                                        $model_item->ezf_field_id = $ezf_field_id;
                                        $model_item->ezf_choicevalue = $value_item['value'];
                                        $model_item->ezf_choicelabel = $value_item['label'];
                                        $model_item->ezf_choiceetc = null;
                                        $model_item->save();
                                    } else {
                                        $model_item = new EzformChoice();
                                        $model_item->ezf_choice_id = $key_item;
                                        $model_item->ezf_id = $ezf_id;
                                        $model_item->ezf_field_id = $ezf_field_id;
                                        $model_item->ezf_choicevalue = $value_item['value'];
                                        $model_item->ezf_choicelabel = $value_item['label'];
                                        $model_item->ezf_choiceetc = null;
                                        $model_item->ezf_choice_col = 12;
                                        $model_item->save();
                                    }

                                    $dataItems['builder'][$key]['fields'][$xy]['data'][$key_item]['action'] = 'update';
                                }
                            }
                        }
                    }
                    
                    
                } elseif(in_array ($dataType, ['select', 'radio'])) {
		    if($value['action']=='update'){
			$model = EzformChoice::find()->where('ezf_choice_id=:id', [':id'=>$key])->one();
                        $model->ezf_id = $ezf_id;
			$model->ezf_field_id = $ezf_field_id;
			$model->ezf_choicevalue = $value['value'];
			$model->ezf_choicelabel = $value['label'];
                        $model->ezf_choiceetc = isset($value['other']['id'])?$value['other']['id']:null;
                        $model->save();
		    } else {
			$model = new EzformChoice();
			$model->ezf_choice_id = $key;
                        $model->ezf_id = $ezf_id;
			$model->ezf_field_id = $ezf_field_id;
			$model->ezf_choicevalue = $value['value'];
			$model->ezf_choicelabel = $value['label'];
			$model->ezf_choiceetc = isset($value['other']['id'])?$value['other']['id']:null;
			$model->ezf_choice_col = 12;
			$model->save();
		    }
		}
                
                if(isset($value['other'])){
                    if($value['other']['action']=='update'){
                        $updateField = EzBuilderQuery::updateFields($value['other'], $dataEzf);
                        if(!$updateField){
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Column <code>{attribute}</code> correction failed.', ['attribute'=>$value['other']['attribute'] ]),
                            ];
                            return $result;
                        }
                    } else {
                        $createField = EzBuilderQuery::addFields($value['other'], $ezf_id, $ezf_field_id, $dataEzf);
                        if(!$createField){
                            $result = [
                                'status' => 'error',
                                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Column <code>{attribute}</code> creation failed.', ['attribute'=>$value['other']['attribute'] ]),
                            ];
                            return $result;
                        }
                    }
                }
		
		//commit
                if(isset($dataItems['builder'][$key]['other'])){
                    $dataItems['builder'][$key]['other']['action'] = 'update';
                }
		$dataItems['builder'][$key]['action'] = 'update';
	    }
	    
	    if($oldName!=$newName){
		EzBuilderQuery::deleteCondition($ezf_id, $oldName);
	    }
	    
	    unset($dataItems['func']);
	}
        
        //delete items
        if(isset($dataItems['delete_fields']) && !empty($dataItems['delete_fields'])){
	    foreach ($dataItems['delete_fields'] as $id=>$varname) {
		$model = EzformFields::find()->where('ezf_field_id=:ezf_field_id', [':ezf_field_id' => $id])->one();
                if($model){
                    $alterTable = self::alterDropColumn($dataEzf['ezf_table'], $varname);
                    if(!$alterTable){
                        $result = [
                            'status' => 'error',
                            'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Column <code>{attribute}</code> delete failed.', ['attribute'=>$varname]),
                            'data' => $model,
                        ];
                        return $result;
                    }
                    $model->delete();
                } else {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Deleted columns can not be deleted because no field <code>{attribute}</code> was found.', ['attribute'=>$varname]),
                        'data' => $model,
                    ];
                    return $result;
                }
	    }
	}
	unset($dataItems['delete_fields']);
        
        
        if(isset($dataItems['delete']) && !empty($dataItems['delete'])){
	    foreach ($dataItems['delete'] as $id) {
                $model = EzformChoice::find()->where('ezf_choice_id=:id', [':id'=>$id])->one();
                $model->delete();
	    }
	}
	unset($dataItems['delete']);
        
	
	return SDUtility::array2String($dataItems);
    }
    
    public static function setEzfFields($dataInput, $dataItems, $unset = true, $merge=true) {
	$tmp_data = SDUtility::string2Array($dataInput);
	$data = isset($dataItems) ? $dataItems : [];
        
        if($merge){
            $data = ArrayHelper::merge($tmp_data, $data);
        }
        
	if ($unset) {
	    foreach ($data as $keyRow => $valueRow) {
		if ($valueRow == '') {
		    unset($data[$keyRow]);
		} elseif (is_array($valueRow)) {
		    foreach ($valueRow as $key => $value) {
			if ($value == '') {
			    unset($data[$keyRow][$key]);
			}
		    }
		}
	    }
	}
	
	return SDUtility::array2String($data);
    }

    public static function setSpecific($dataInput, $options, $merge=true) {
	$dataItems = isset($options['specific']) ? $options['specific'] : [];
	$tmp_specific = SDUtility::string2Array($dataInput['input_specific']);
	$specific = [];
	$sColor = '';
        
	if (isset($dataItems['color']) && !empty($dataItems['color'])) {
	    $sColor = 'color:' . $dataItems['color'];
	    $specific['labelOptions'] = ['style' => $sColor];
	}
        
	if (isset($dataItems['icon']) && !empty($dataItems['icon'])) {
	    $specific['template'] = "<i class='fa {$dataItems['icon']}' style='$sColor'></i> {label}\n{input}\n{hint}\n{error}";
	}
        
	$prefix = '';
	if (isset($dataItems['prefix']) && !empty($dataItems['prefix'])) {
	    $prefix = '<span class="input-group-addon">' . $dataItems['prefix'] . '</span>';
	}
        
	$suffix = '';
	if (isset($dataItems['suffix']) && !empty($dataItems['suffix'])) {
	    $suffix = '<span class="input-group-addon">' . $dataItems['suffix'] . '</span>';
	}
        
        unset($dataItems['suffix']);
        unset($dataItems['prefix']);
        unset($dataItems['icon']);
        unset($dataItems['color']);

	if ($prefix != '' || $suffix != '') {
	    $specific['inputTemplate'] = '<div class="input-group">' . $prefix . '{input}' . $suffix . '</div>';
	}
        
        $specific = ArrayHelper::merge($specific, $dataItems);
        
        if($merge){
            $specific = ArrayHelper::merge($tmp_specific, $specific);
        }
        
	return SDUtility::array2String($specific);
    }
    
    
    
    //$index = INDEX, PRIMARY, UNIQUE, FULLTEXT
    //$length 0 = not set
    public static function alterTableAdd($table, $column, $type, $length = 0, $index=0) {
	$strLen = '';
	$strIndex = '';
        
        if($index){
            $strIndex = ", ADD INDEX (`$column`)";
        }
        
	if ($length > 0) {
	    $strLen = "($length)";
	}

	$type = "$type $strLen NULL DEFAULT NULL $strIndex";

	try {
            
	    Yii::$app->db->createCommand()->addColumn($table, $column, $type)->execute();
	    return true;
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    
    public static function alterDropColumn($table, $column) {
	try {
	   Yii::$app->db->createCommand()->dropColumn($table, $column)->execute();
	    return true;
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    
    public static function alterTableChange($table, $column, $newColumn, $type, $length = 0, $index, $oldType, $oldLength, $oldIndex) {
	$strLen = '';
	$strIndex = '';
        //ALTER TABLE `nhis`.`zdata_test3` DROP INDEX `ddd`;
        
        if($index){
            $strIndex = ", ADD INDEX (`$column`)";
        }
        
	if ($length > $oldLength) {
	    $strLen = "($length)";
	} else {
            $strLen = "($oldLength)";
        }
        
        if($oldType=='TEXT'){
            $type = 'TEXT';
        }
        
        if($type=='TEXT'){
            $strLen = '';
            $strIndex = '';
            
            if($oldIndex){
                $sql = "ALTER TABLE `$table` DROP INDEX `$column`";
                try {
                    Yii::$app->db->createCommand($sql)->execute();
                } catch (\yii\db\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                }
            }
            
        }
        
        if($oldIndex==1 && $index==0){
            $sql = "ALTER TABLE `$table` DROP INDEX `$column`";
            Yii::$app->db->createCommand($sql)->execute();
        }elseif($oldIndex==1 && $index==1){
            $strIndex = '';
        }
        
	try {
	    $sql = "ALTER TABLE `$table` CHANGE `$column` `$newColumn` {$type}{$strLen} NULL DEFAULT NULL $strIndex";
	    Yii::$app->db->createCommand($sql)->execute();
	    return true;
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    
    public static function resetFieldData($ezf_field_id, $table) {
	try {
            EzfQuery::deleteChoice($ezf_field_id);
            
            $model = EzformFields::find()->where('ezf_field_ref=:ezf_field_ref', [':ezf_field_ref' => $ezf_field_id])->all();
            if($model){
                foreach ($model as $key => $value) {
                    self::alterDropColumn($table, $value->ezf_field_name);
                }
                EzfQuery::deleteFieldOther($ezf_field_id);
            }
            
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    
    public static function systemCreateField($column) {
	try {
            $modelField = EzfQuery::getFieldById($column['ezf_field_id']);
            if($modelField){
                return self::systemUpdateField($column['ezf_field_id'], $column);
            } else {
                return Yii::$app->db->createCommand()->insert('ezform_fields', $column)->execute();
            }
	    
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    
    public static function systemUpdateField($ezf_field_id, $column) {
	try {
	    Yii::$app->db->createCommand()->update('ezform_fields', $column, 'ezf_field_id=:ezf_field_id', [':ezf_field_id'=>$ezf_field_id])->execute();
	    return true;
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    
    public static function systemDeleteField($ezf_field_id) {
	try {
	    Yii::$app->db->createCommand()->delete('ezform_fields', 'ezf_field_id=:ezf_field_id', [':ezf_field_id'=>$ezf_field_id])->execute();
	    return true;
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    
    public static function checkFieldTable($table, $var) {
	try {
	    $fields = EzfQuery::showColumn($table);

            if($fields){
                foreach ($fields as $key => $value) {
                    if($value['Field'] == $var){
                        return Yii::t('ezform', '<code>{attribute}</code> variable is already in use', ['attribute'=>$var]);
                    }
                }
            }
            
	    return false;
	} catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
	    return false;
	}
    }
    /**
     * Creates EzformFields
     * 
     * Below is an example:
     *
     * ```php
     * 
     * $model = new EzformFields();
     * $model->ezf_id = $ezf_id;
     * $model->ezf_field_id = SDUtility::getMillisecTime();
     * $model->ezf_field_name = EzfFunc::generateFieldName($ezf_id);
     * $model->ezf_field_order = EzfQuery::getFieldsCountById($ezf_id);
     * 
     * $oldModel = $model;
     * 
     * $dataEzf = Ezform::find()->where('ezf_id=:id', [':id'=>$ezf_id])->one();
     * $dataInput = EzformInput::find()->where('input_id=:id', [':id'=>$ezf_field_type])->one();
     * 
     * $data = isset($_POST['data']) ? $_POST['data'] : [];
     * $options = isset($_POST['options']) ? $_POST['options'] : [];
     * $validate = isset($_POST['validate']) ? $_POST['validate'] : [];
     * 
     * EzBuilderFunc::saveEzField($model, $oldModel, $dataEzf, $dataInput, $data, $options, $validate);
     * 
     * ```
     *
     * @param object $model ezform fields model (EzformFields)
     * @param object $model ezform fields model (EzformFields)
     * @param array $dataEzf ข้อมูลของ ezform model
     * @param array $dataInput ข้อมูลของ ezform input model
     * @param array $data list คำถาม ถ้าไม่มีให้ใส่ []
     * @param array $options Yii2 widget > Html/jQuery ถ้าไม่มีให้ใส่ []
     * @param array $validate Yii2 rules model ถ้าไม่มีให้ใส่ []
     * 
     * @return array message
     */
    
    public static function saveEzField($model, $oldModel, $dataEzf, $dataInput, $data, $options, $validate) {
        if ($dataInput) {
            $oldName = $oldModel['ezf_field_name'];
	    $oldType = $oldModel['table_field_type'];
	    $oldLength = $oldModel['table_field_length'];
            $oldIndex = $oldModel['table_index'];
            
            $isNewRecord = $model->isNewRecord;
            //check params
            if (!isset($model->ezf_field_id) || !isset($model->ezf_id)) {
                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgWarning() . Yii::t('ezform', 'The requested field was not found. <code>[ezf_id, ezf_field_id]</code>'),
                ];
                return $result;
            }
            
            //check fields
            if ($oldName != $model->ezf_field_name) {
                $check_fields = EzBuilderFunc::checkFieldTable($dataEzf['ezf_table'], $model->ezf_field_name);
                if ($check_fields) {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgWarning() . $check_fields,
                    ];
                    return $result;
                }
                $varFields = EzfQuery::getFieldByName($model->ezf_id, $model->ezf_field_name);
                if($varFields){
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgWarning() . Yii::t('ezform', '<code>{attribute}</code> variable is already in use', ['attribute'=>$varFields['ezf_field_name']]),
                    ];
                    return $result;
                }
            }
            
            
            //เป้าหมาย คำถามพิเศษ
            if($model->ezf_target==1 || $model->ezf_special==1){
                $eventFields = EzfQuery::checkEventFields($model->ezf_id, $model->ezf_field_id);
                if($eventFields){
                    $type = $eventFields['ezf_target']==1?Yii::t('ezform', 'Target question type'):Yii::t('ezform', 'Special question type');
                    $typeThis = $model->ezf_target==1?Yii::t('ezform', 'Target question type'):Yii::t('ezform', 'Special question type');
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgWarning() . Yii::t('ezform', '`{typethis}` can not be created because it has `{type}` is currently <code>{attribute}</code>', ['typethis'=>$typeThis, 'type'=>$type, 'attribute'=>$eventFields['ezf_field_name']]),
                    ];
                    return $result;
                }
            }
            
            $model->table_field_type = $dataInput['table_field_type'];
            $model->table_field_length = $dataInput['table_field_length'];

            //set data 
            //EX. data['items'=>[], 'func'=>'', 'builder'=>[], 'delete'=>[]]
            if ($dataInput['input_function'] == 'widget') {
                $dataType = isset($options['options']['data-type']) ? $options['options']['data-type'] : null;
                $dataFrom = isset($options['options']['data-from']) ? $options['options']['data-from'] : '';
                $dataTo = isset($options['options']['data-to']) ? $options['options']['data-to'] : '';
                $dataName = isset($options['options']['data-name']) ? $options['options']['data-name'] : '';
            } else {
                $dataType = isset($options['data-type']) ? $options['data-type'] : null;
                $dataFrom = isset($options['data-from']) ? $options['data-from'] : '';
                $dataTo = isset($options['data-to']) ? $options['data-to'] : '';
                $dataName = isset($options['data-name']) ? $options['data-name'] : '';
            }
            
            if ($dataType == 'file') {
                $model->ezf_field_default = EzBuilderFunc::uploadOption($model, $dataFrom, $dataTo);
                $options[$dataName] = $model->ezf_field_default;
            } elseif ($dataType == 'target') {
                if (empty($model->ref_ezf_id) || empty($model->ref_field_id) || empty($model->ref_field_desc) || empty($model->ref_field_search)) {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Please complete the fields.') . ' <code>'.Yii::t('ezform', 'Form').'</code>, <code>'.Yii::t('ezform', 'Variable').'</code>, <code>'.Yii::t('ezform', 'Display variables').'</code>, <code>'.Yii::t('ezform', 'Search variables').'</code>',
                    ];
                    return $result;
                }
                //if($model->ezf_target==1){
                    $options['ezf_id'] = $model->ref_ezf_id;
                //}
                
            } elseif ($dataType == 'ref' || $dataType == 'viewer') {
                if (empty($model->ref_ezf_id) || empty($model->ref_field_id) ) {
                    $result = [
                        'status' => 'error',
                        'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Please complete the fields.') . ' <code>'.Yii::t('ezform', 'Form').'</code>, <code>'.Yii::t('ezform', 'Variable').'</code>',
                    ];
                    return $result;
                }
                
                $options['ezf_id'] = $model->ref_ezf_id;
                 
                if($dataType == 'ref'){
                    $modelRef = EzfQuery::getFieldByName($model->ref_ezf_id, $model->ref_field_id);
                    $model->ezf_field_ref = $modelRef->ezf_field_id;
                    
                    $model->table_field_type = $modelRef->table_field_type;
                    $model->table_field_length = $modelRef->table_field_length;
                    $model->table_index = $modelRef->table_index;
                }
            } else {
                $model->ezf_field_ref = NULL;
            }
            
            $model->ezf_field_data = EzBuilderFunc::setEzfData($data, $model->ezf_field_id, $model->ezf_id, $oldName, $model->ezf_field_name, $dataType, $dataEzf);
            if (is_array($model->ezf_field_data)) {
                if($isNewRecord){
                    EzBuilderFunc::resetFieldData($model->ezf_field_id, $dataEzf['ezf_table']);
                }
                
                return $model->ezf_field_data;
            }
            //set options
            $model->ezf_field_options = EzBuilderFunc::setEzfFields($dataInput['input_option'], $options, FALSE);

            //fix specific
            $model->ezf_field_specific = EzBuilderFunc::setSpecific($dataInput, $options);

            //set validation
            $model->ezf_field_validate = EzBuilderFunc::setEzfFields($dataInput['input_validate'], $validate, true, true);
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'The selected question type was not found.'),
                'data' => $model,
            ];
            return $result;
        }

        //Create table fields
        $alterTable = true;
        
        
        if($isNewRecord){
            if ($dataInput['table_field_type'] != 'none' && $dataInput['table_field_type'] != 'field') {
                $alterTable = EzBuilderFunc::alterTableAdd($dataEzf['ezf_table'], $model->ezf_field_name, $model->table_field_type, $model->table_field_length, $model->table_index);
            }
        } else {
           
            if ($model->ezf_field_name != $oldName || $model->table_field_type != $oldType || $model->table_field_length != $oldLength || $oldIndex != $model->table_index) {
                 
                if ($oldType == 'none' && ($model->table_field_type != 'none' && $model->table_field_type != 'field')) {
                    $alterTable = EzBuilderFunc::alterTableAdd($dataEzf['ezf_table'], $model->ezf_field_name, $model->table_field_type, $model->table_field_length, $model->table_index);
                } elseif (($oldType != 'none' && $oldType != 'field') && $model->table_field_type == 'none') {
                    $alterTable = EzBuilderFunc::alterDropColumn($dataEzf['ezf_table'], $oldName);
                    //Drop OTHER ด้วย ยังไม่ทำ
                    //EzBuilderFunc::resetFieldData($model->ezf_field_id, $dataEzf['ezf_table']);
                } elseif ($oldType == 'field' && ($model->table_field_type != 'none' && $model->table_field_type != 'field')) {
                    $alterTable = EzBuilderFunc::alterTableAdd($dataEzf['ezf_table'], $model->ezf_field_name, $model->table_field_type, $model->table_field_length, $model->table_index);
                    //EzBuilderFunc::resetFieldData($model->ezf_field_id, $dataEzf['ezf_table']);
                } elseif (($oldType != 'none' && $oldType != 'field') && $model->table_field_type == 'field') {
                    $alterTable = EzBuilderFunc::alterDropColumn($dataEzf['ezf_table'], $oldName);
                } elseif (($oldType != 'none' && $oldType != 'field') && ($model->table_field_type != 'none' && $model->table_field_type != 'field')) {
                    
                    $alterTable = EzBuilderFunc::alterTableChange($dataEzf['ezf_table'], $oldName, $model->ezf_field_name, $model->table_field_type, $model->table_field_length, $model->table_index, $oldType, $oldLength, $oldIndex);
                } 
            }
        }

        if (!$alterTable) {
            //EzBuilderFunc::resetFieldData($model->ezf_field_id, $dataEzf['ezf_table']);

            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('ezform', 'Column <code>{attribute}</code> creation failed.', ['attribute'=>$model->ezf_field_name]),
                'data' => $model,
            ];
            return $result;
        }

        try {
            if ($model->save()) {

                $inputWidget = Yii::createObject($dataInput['system_class']);
                $htmlInput = $inputWidget->generateViewInput($model->attributes);

                $html = EzBuilderFunc::createChildrenItem($model->attributes, $htmlInput);

                if($model->ezf_target==1){
                    $parentUpdate = EzfQuery::setParentFields($model->ezf_id, $model->parent_ezf_id);
                }
                
                //\appxq\sdii\utils\VarDumper::dump($model->attributes,1,0);

                $result = [
                    'status' => 'success',
                    'action' => $isNewRecord?'create':'update',
                    'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Data completed.'),
                    'data' => $model,
                    'html' => $html,
                    'alterTable' => $alterTable,
                ];
                return $result;
            } else {
                if($isNewRecord){
                    //reset field data
                    EzBuilderFunc::resetFieldData($model->ezf_field_id, $dataEzf['ezf_table']);
                    //reset table
                    EzBuilderFunc::alterDropColumn($dataEzf['ezf_table'], $model->ezf_field_name);
                }

                $result = [
                    'status' => 'error',
                    'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not update the data.'),
                    'data' => $model,
                ];
                return $result;
            }
        } catch (\yii\db\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            if($isNewRecord){
                //reset field data
                EzBuilderFunc::resetFieldData($model->ezf_field_id, $dataEzf['ezf_table']);
                //reset table
                EzBuilderFunc::alterDropColumn($dataEzf['ezf_table'], $model->ezf_field_name);
            }
            
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
                'data' => $model,
            ];
            return $result;
        } catch (\yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            if($isNewRecord){
                //reset field data
                EzBuilderFunc::resetFieldData($model->ezf_field_id, $dataEzf['ezf_table']);
                //reset table
                EzBuilderFunc::alterDropColumn($dataEzf['ezf_table'], $model->ezf_field_name);
            }
            
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not create the data.'),
                'data' => $model,
            ];
            return $result;
        }
    }

    public static function deleteEzField($ezf_field_id) {
        $model = EzformFields::find()->where('ezf_field_id=:ezf_field_id', [':ezf_field_id'=>$ezf_field_id])->one();
        
        $modelEzf = EzfQuery::getEzformOne($model->ezf_id);
        $dataEzf = $modelEzf->attributes;
            
        $column = $model->ezf_field_name;
        $ezf_field_id = $model->ezf_field_id;

        $ezf_target = $model->ezf_target;
        $ezf_id = $model->ezf_id;
        
        if ($model->delete()) {
            //reset all
            if($ezf_target==1){
                $parentUpdate = EzfQuery::setParentFields($ezf_id, $ezf_id);
            }
            
            $alterTable = EzBuilderFunc::alterDropColumn($dataEzf['ezf_table'], $column);
            EzBuilderFunc::resetFieldData($ezf_field_id, $dataEzf['ezf_table']);

            $result = [
                'status' => 'success',
                'action' => 'update',
                'message' => SDHtml::getMsgSuccess() . Yii::t('app', 'Deleted completed.'),
                'data' => $ezf_field_id,
            ];
            return $result;
        } else {
            $result = [
                'status' => 'error',
                'message' => SDHtml::getMsgError() . Yii::t('app', 'Can not delete the data.'),
                'data' => $ezf_field_id,
            ];
            return $result;
        }
    }
    
}
