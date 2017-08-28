<?php

namespace backend\modules\ezforms2\classes;

use Yii;
use appxq\sdii\models\SDDynamicModel;
use backend\modules\ezforms2\classes\EzfQuery;
use appxq\sdii\utils\SDUtility;
use yii\helpers\ArrayHelper;
use yii\web\View;
use backend\modules\ezforms2\models\EzformCondition;
use yii\helpers\Html;
use backend\modules\core\classes\CoreFunc;

/**
 * OvccaFunc class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 9 ก.พ. 2559 12:38:14
 * @link http://www.appxq.com/
 * @example 
 */
class EzfFunc {

    public static function setDynamicModel($fields, $table, $ezf_input, $annotated = 0) {
        $attributes = ['ptid', 'xsourcex', 'xdepartmentx', 'rstat', 'sitecode', 'ptcode', 'ptcodefull', 'hptcode', 'hsitecode', 'user_create', 'create_date', 'user_update', 'update_date', 'target', 'error', 'sys_lat', 'sys_lng'];
        $labels = [];
        $required = [];
        $rules = [];
        //$rulesFields = [];
        $rulesFields['safe'] = ['ptid', 'xsourcex', 'xdepartmentx', 'rstat', 'sitecode', 'ptcode', 'ptcodefull', 'hptcode', 'hsitecode', 'user_create', 'create_date', 'user_update', 'update_date', 'target', 'error', 'sys_lat', 'sys_lng'];
        $condFields = [];
        $behavior = [];
        $fields_type = [];
        $ezf_id;

        if (!empty($fields)) {
            foreach ($fields as $value) {
                $ezf_id = $value['ezf_id'];
                $fields_type[$value['ezf_field_name']] = $value['ezf_field_type'];
                //Attributes array
                $attributes[$value['ezf_field_name']] = $value['ezf_field_default'];
                
                //Labels array
                $labels[$value['ezf_field_name']] = isset($value['ezf_field_label']) ? $value['ezf_field_label'] : '';
                if ($annotated == 1 && $value['table_field_type'] != 'none' && $value['table_field_type'] != 'field') {
                    $labels[$value['ezf_field_name']] .= " <code>{$value['ezf_field_name']}</code>";
                }
                
                //Rule array required
                if ($value['ezf_field_required'] == 1) {
                    $required[] = $value['ezf_field_name'];
                }

                //Rule array validate
                $validateArray = SDUtility::string2Array($value['ezf_field_validate']);
                if (is_array($validateArray)) {
                    $addRule = false;
                    foreach ($validateArray as $keyRule => $valueRule) {
                        if (is_array($valueRule)) {
                            $name = self::getRuleName($valueRule);
                            $rulesFields[$name][] = $value['ezf_field_name'];
                            $rules[$name] = $valueRule;
                        } else {
                            $addRule = true;
                            break;
                        }
                    }

                    if ($addRule) {
                        $name = self::getRuleName($validateArray);
                        $rulesFields[$name][] = $value['ezf_field_name'];
                        $rules[$name] = $validateArray;
                    }
                }

                $rulesFields['safe'][] = $value['ezf_field_name'];
                $rules['safe'] = ['safe'];

                if ($value['ezf_condition'] == 1) {
                    $condFields[] = self::getCondition($value['ezf_id'], $value['ezf_field_name']);
                }

                $dataInput;
                if ($ezf_input) {
                    $dataInput = EzfFunc::getInputByArray($value['ezf_field_type'], $ezf_input);
                }
                if ($dataInput) {
                    $behavior = ArrayHelper::merge($behavior, self::setBehavior($table, $value->attributes, $value->ezf_field_type, $value->ezf_field_name, $dataInput));
                }
            }
        }

        $model = new SDDynamicModel($attributes);
        $model->formName = "EZ$ezf_id";

        foreach ($rules as $key => $value) {
            $options = isset($value[1]) ? $value[1] : [];
            $model->addRule($rulesFields[$key], $value[0], $options);
        }

        $js = '';
        foreach ($condFields as $key => $value) {
            if (!empty($value)) {
                foreach ($required as $i => $v) {
                    foreach ($value as $k => $data) {
                        $inputId = Html::getInputId($model, $data['ezf_field_name']);
                        $inputName = Html::getInputName($model, $data['ezf_field_name']);

                        $setSelector = "#$inputId}";
                        $jumpCheck = false;
                        if (in_array($fields_type[$data['ezf_field_name']], CoreFunc::itemAlias('ezf_check_conditon'))) {
                            $jumpCheck = true;
                            $setSelector = "#$inputId:checked";
                        } elseif (in_array($fields_type[$data['ezf_field_name']], CoreFunc::itemAlias('ezf_radio_conditon'))) {
                            $setSelector = "input[name=\"$inputName\"]:checked";
                        }
                        //\appxq\sdii\utils\VarDumper::dump($data,0);
                        // required ก็ต่อมือ condition แสดง
                        if ((!empty($data['var_require']) && in_array($v, $data['var_require']))) {//|| (!empty($data['var_jump']) && in_array($v, $data['var_jump']))
                            $js .= "if(attribute.name == '$v') {
				    var r = $('$setSelector').val()=='{$data['ezf_field_value']}';
				    console.log(r);	
				    return r;	
			    }";
                        }
                        if ($jumpCheck) {
                            // required ก็ต่อมือ condition ซ่อน
                            if ((!empty($data['var_jump']) && in_array($v, $data['var_jump']))) {//|| (!empty($data['var_jump']) && in_array($v, $data['var_jump']))
                                $js .= "if(attribute.name == '$v') {
                                        var r = $('$setSelector').val()=='{$data['ezf_field_value']}';
                                        console.log(r);	
                                        return !r;	
                                }";
                            }
                        }
                    }
                }
            }
        }

        $whenClient = $js != '' ? ['whenClient' => "function (attribute, value) { $js }"] : [];

        $model->addRule($required, 'required', $whenClient);

        $model->addLabel($labels);
                        
        if (!empty($behavior)) {
            foreach ($behavior as $keyBehavior => $valueBehavior) {
                $model->attachBehavior($keyBehavior, $valueBehavior);
            }
        }

        return $model;
    }

    public static function setBehavior($table, $attributes, $ezf_field_type, $ezf_field_name, $dataInput) {

        $behavior = [];

        try {
            if ($dataInput) {
                if (isset($dataInput['input_behavior']) && $dataInput['input_behavior'] != '') {
                    $behavior[$dataInput['input_behavior'] . '_' . $ezf_field_name] = [
                        'class' => $dataInput['input_behavior'],
                        'ezf_field' => $attributes,
                        'ezf_table' => $table,
                    ];
                }
            }
        } catch (\ReflectionException $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
        }

        return $behavior;
    }

    public static function getBehavior($fields, $table, $dataInput) {

        $behavior = [];

        if (!empty($fields)) {
            foreach ($fields as $value) {
                if ($dataInput) {
                    $behavior = ArrayHelper::merge($behavior, self::setBehavior($table, $value->attributes, $value->ezf_field_type, $value->ezf_field_name, $dataInput));
                }
            }
        }

        return $behavior;
    }

    public static function generateInput($form, $model, $modelFields, $dataInput, $disableFields=0, $widgets = '//../modules/ezforms2/views/widgets/_view_item') {
        $html = '';
        $view = new View();
        try {
            if ($modelFields['table_field_type'] != 'none' && $modelFields['table_field_type'] != '') {
                $dataInput;

                if ($dataInput) {
                    $specific = SDUtility::string2Array($modelFields['ezf_field_specific']);
                    $options = SDUtility::string2ArrayJs($modelFields['ezf_field_options']);
                    unset($options['specific']);

                    $data = SDUtility::string2Array($modelFields['ezf_field_data']);

                    $label = "->label('{$model->getAttributeLabel($modelFields['ezf_field_name'])}')";
                    if (isset($modelFields['ezf_field_label']) && $modelFields['ezf_field_label'] == '') {
                        $label = "->label('')";
                    }
                    
                    //inline, label fix
                    if ($dataInput['input_function'] == 'widget') {
                        if (isset(Yii::$app->session['show_varname']) && Yii::$app->session['show_varname']){
                            $options['options']['annotated'] = 1;
                        }
                        
                        if ($disableFields){
                            $options['options']['disabled'] = $disableFields;
                        }
                        
                        if (!empty($data)) {
                            if (isset($data['items'])) {
                                $options['data'] = $data['items'];
                            }

                            if (isset($data['func'])) {
                                try {
                                    eval("\$dataItems = {$data['func']};");
                                } catch (\yii\base\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                    $dataItems = [];
                                }
                                $options['data'] = $dataItems;
                            }

                            if (isset($data['fields'])) {
                                $options['fields'] = $data['fields'];
                            }
                        }
                        
                        $widget_render = '';
                        if (isset($model[$modelFields['ezf_field_name']]) && !empty($model[$modelFields['ezf_field_name']])) {
                            
                            if (isset($options['options']['data-func-set']) && !empty($options['options']['data-func-set'])) {
                                $pathStr = [
                                    '{model}' => "\$model",
                                    '{modelFields}' => "\$modelFields",
                                ];
                                
                                $funcSet = strtr($options['options']['data-func-set'], $pathStr);

                                try {
                                    $initial = eval("return $funcSet;");
                                } catch (\yii\base\Exception $e) {
                                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                                    $initial = FALSE;
                                }
                                
                                
                                if ($initial) {
                                    if (isset($options['options']['data-name-in'])) {
                                        $data_in = $options['options']['data-name-in'];
                                        $data_set = self::addProperty($data_in, $options['options']['data-name-set'], $initial);
                                        $options = ArrayHelper::merge($options, $data_set);
                                    } else {
                                        $options[$options['options']['data-name-set']] = $initial;
                                    }
                                }
                            }

                            if (isset($options['options']['data-data-widget']) && !empty($options['options']['data-data-widget'])) {
                                $widget_render = $view->renderAjax($options['options']['data-data-widget'], [
                                    'model' => $model,
                                    'modelFields' => $modelFields,
                                ]);
                            }
                        }

                        $attribute = "\$modelFields['ezf_field_name']";
                        if (isset($options['options']['multiple']) && $options['options']['multiple'] == true) {
                            $attribute = "\$modelFields['ezf_field_name'].'[]'";
                        }

                        eval("\$html = \$form->field(\$model, $attribute, \$specific)->hint(\$modelFields['ezf_field_hint'])->{$dataInput['input_function']}({$dataInput['input_class']}, \$options)$label;");
                        $html .= $widget_render;
                    } else {
                        if (isset(Yii::$app->session['show_varname']) && Yii::$app->session['show_varname']){
                            $options['annotated'] = 1;
                        }
                        
                        if ($disableFields){
                            $options['disabled'] = $disableFields;
                        }
                        if (empty($data)) {

                            eval("\$html = \$form->field(\$model, \$modelFields['ezf_field_name'], \$specific)->hint(\$modelFields['ezf_field_hint'])->{$dataInput['input_function']}(\$options)$label;");
                        } else {
                            if (isset($data['func'])) {
                                eval("\$dataItems = {$data['func']};");
                            } else {
                                $dataItems = $data['items'];
                            }
                            eval("\$html = \$form->field(\$model, \$modelFields['ezf_field_name'], \$specific)->hint(\$modelFields['ezf_field_hint'])->{$dataInput['input_function']}(\$dataItems, \$options)$label;");
                        }
                    }
                } else {
                    $html = Html::activeHiddenInput($model, $modelFields['ezf_field_name']);
                }
            } else {

                if ($dataInput) {
                    $class = str_replace('::className()', '', $dataInput['input_class']);
                    $options = SDUtility::string2Array($modelFields['ezf_field_options']);
                    $options['name'] = $modelFields['ezf_field_name'];
                    $options['value'] = $modelFields['ezf_field_label'];
                    $options['model'] = $model;
                    
                    if ($disableFields){
                        $options['options']['disabled'] = true;
                    }

                    if ($modelFields['ezf_field_type'] == '57') {
                        $html = '';
                    } else {
                        eval("\$html = {$class}::widget(\$options);");
                        if (isset($modelFields['ezf_field_hint'])) {
                            $html .= $modelFields['ezf_field_hint'];
                        }
                    }
                }
            }

            $style_color = '';
            if ($modelFields['ezf_field_color'] != '') {
                $style_color = "background-color: {$modelFields['ezf_field_color']};";
            }

            $hide = $modelFields['ezf_field_type'] == 0 ? 'display: none;' : '';

            $widget = '<div class="col-md-' . $modelFields['ezf_field_lenght'] . '" item-id="' . $modelFields['ezf_field_id'] . '" style="' . $hide . $style_color . '">' . $html . '</div>';
            return $widget;
//          return $view->renderAjax($widgets, [
//                'field_id' => $modelFields['ezf_field_id'],
//                'field_size' => $modelFields['ezf_field_lenght'],
//                'style_color' => $style_color,
//                'field_item' => $html,
//                'hide'=>$modelFields['ezf_field_type']==0?'display: none;':'',
//          ]);
        } catch (yii\base\Exception $e) {
            \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
            return '<code>' . $e->getMessage() . '</code>';
        }
    }

    public static function addProperty($obj, $key, $data) {
        foreach ($obj as $i => $value) {
            if (is_array($value)) {
                $obj[$i] = self::addProperty($value, $key, $data);
            } else {
                if ($i == $key) {
                    $obj[$key] = $data;
                }
            }
        }
        return $obj;
    }

    private static function getRuleName($rule) {
        $name = $rule[0];
        if (count($rule) > 1) {
            $name = '';
            foreach ($rule as $key => $value) {
                if (is_integer($key)) {
                    $name .= $value;
                } else {
                    $name .= $key . $value;
                }
            }
        }
        return $name;
    }

    public static function getCondition($ezf_id, $ezf_field_name) {
        $model = EzformCondition::find()
                ->where('ezf_id=:ezf_id AND ezf_field_name=:ezf_field_name', [':ezf_id' => $ezf_id, ':ezf_field_name' => $ezf_field_name])
                ->all();

        $dataEzf = [];
        if ($model) {
            $k = 0;
            foreach ($model as $key => $value) {
                $arr_cond_jump = \yii\helpers\Json::decode($value['cond_jump']);
                $arr_cond_require = \yii\helpers\Json::decode($value['cond_require']);

                if (is_array($arr_cond_jump)) {
                    $cond_jump = implode(',', $arr_cond_jump);
                    $var_jump = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_name', $cond_jump), 'ezf_field_name');
                } else {
                    $var_jump = [];
                }

                if (is_array($arr_cond_require)) {
                    $cond_require = implode(',', $arr_cond_require);
                    $var_require = ArrayHelper::getColumn(EzfQuery::getConditionFieldsName('ezf_field_name', $cond_require), 'ezf_field_name');
                } else {
                    $var_require = [];
                }

                $dataEzf[$k]['ezf_id'] = $ezf_id;
                $dataEzf[$k]['ezf_field_name'] = $value['ezf_field_name'];
                $dataEzf[$k]['ezf_field_value'] = $value['ezf_field_value'];
                $dataEzf[$k]['var_jump'] = isset($var_jump) ? $var_jump : '';
                $dataEzf[$k]['var_require'] = isset($var_require) ? $var_require : '';
                $k++;
            }
        }

        return $dataEzf;
    }

    public static function generateFieldName($ezf_id) {
        return 'var_' . (EzfQuery::getFieldsCountById($ezf_id) + 1);
    }

    public static function getInputByArray($id, $input) {
        foreach ($input as $key => $value) {
            if ($value['input_id'] == $id) {
                return $value;
            }
        }
        return FALSE;
    }

    public static function mergeValidate($validate) {
        $addArry = [];
        if (isset($validate) && is_array($validate)) {
            foreach ($validate as $row => $items) {
                foreach ($items as $key => $value) {
                    $addArry[$items[0]][$key] = $value;
                }
            }
        }
        $returnArry = [];
        foreach ($addArry as $key => $value) {
            $returnArry[] = $value;
        }

        return $returnArry;
    }

    public static function generateCondition($modelTable, $field, $model, $view, $dataInput) {
        //$view = new View();
        $inputId = Html::getInputId($modelTable, $field['ezf_field_name']);
        $inputName = Html::getInputName($modelTable, $field['ezf_field_name']);
        $inputValue = Html::getAttributeValue($modelTable, $field['ezf_field_name']);

        $dataCond = EzfQuery::getCondition($field['ezf_id'], $field['ezf_field_name']);
        if ($dataCond) {
            //Edit Html
            $condition = SDUtility::string2Array($field['ezf_field_options']);

            $fieldId = $inputId;
            $dataType = 'none';

            if ($dataInput) {
                if ($dataInput['input_function'] == 'widget') {
                    if (isset($condition['options']['data-type'])) {
                        $dataType = $condition['options']['data-type'];
                    }
                } else {

                    if (isset($condition['data-type'])) {
                        $dataType = $condition['data-type'];
                    }
                }
            }

            if ($dataType == 'select' || $dataType == 'radio') {
                $fieldId = $field['ezf_field_name'];
            }



            $enable = TRUE;
            foreach ($dataCond as $index => $cvalue) {
                //if($inputValue == $cvalue['ezf_field_value'] || $inputValue == ''){
                $dataCond[$index]['cond_jump'] = \yii\helpers\Json::decode($cvalue['cond_jump']);
                $dataCond[$index]['cond_require'] = \yii\helpers\Json::decode($cvalue['cond_require']);


                if ($dataType == 'select' || $dataType == 'radio') {
                    if ($inputValue == $cvalue['ezf_field_value'] || $inputValue == '') {
                        if ($enable) {
                            $enable = false;
                            $jumpArr = \yii\helpers\Json::decode($cvalue['cond_jump']);
                            if (is_array($jumpArr)) {
                                foreach ($jumpArr as $j => $jvalue) {
                                    $view->registerJs("
					    var fieldIdj = '" . $jvalue . "';
					    var inputIdj = '" . $fieldId . "';
					    var valueIdj = '" . $inputValue . "';
					    var fixValuej = '" . $cvalue['ezf_field_value'] . "';
					    var fTypej = '" . $dataType . "';
					    domHtml(fieldIdj, inputIdj, valueIdj, fixValuej, fTypej, 'none');
				    ");
                                }
                            }

                            $requireArr = \yii\helpers\Json::decode($cvalue['cond_require']);
                            if (is_array($requireArr)) {
                                foreach ($requireArr as $r => $rvalue) {
                                    $view->registerJs("
					    var fieldIdr = '" . $rvalue . "';
					    var inputIdr = '" . $fieldId . "';
					    var valueIdr = '" . $inputValue . "';
					    var fixValuer = '" . $cvalue['ezf_field_value'] . "';
					    var fTyper = '" . $dataType . "';
					    domHtml(fieldIdr, inputIdr, valueIdr, fixValuer, fTyper, 'block');
				    ");
                                }
                            }
                        }
                    }
                } else {

                    $jumpArr = \yii\helpers\Json::decode($cvalue['cond_jump']);
                    if (is_array($jumpArr)) {
                        foreach ($jumpArr as $j => $jvalue) {
                            $view->registerJs("
				    var fieldIdj = '" . $jvalue . "';
				    var inputIdj = '" . $fieldId . "';
				    var valueIdj = '" . $inputValue . "';
				    var fixValuej = '" . $cvalue['ezf_field_value'] . "';
				    var fTypej = '" . $dataType . "';
				    domHtml(fieldIdj, inputIdj, valueIdj, fixValuej, fTypej, 'block');
			    ");
                        }
                    }

                    $requireArr = \yii\helpers\Json::decode($cvalue['cond_require']);
                    if (is_array($requireArr)) {

                        foreach ($requireArr as $r => $rvalue) {

                            $view->registerJs("
				    var fieldIdr = '" . $rvalue . "';
				    var inputIdr = '" . $fieldId . "';
				    var valueIdr = '" . $inputValue . "';
				    var fixValuer = '" . $cvalue['ezf_field_value'] . "';
				    var fTyper = '" . $dataType . "';
				    domHtml(fieldIdr, inputIdr, valueIdr, fixValuer, fTyper, 'none');

			    ");
                        }
                    }
                }
            }

            //Add Event
            if ($dataType == 'checkbox') {
                $view->registerJs("
			eventCheckBox('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "');
			setCheckBox('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "');
		    ");
            } else if ($dataType == 'select') {

                $view->registerJs("
			eventSelect('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "');
			setSelect('" . $inputId . "', '" . yii\helpers\Json::encode($dataCond) . "');
		    ");
            } else if ($dataType == 'radio') {
                $view->registerJs("
			eventRadio('" . $inputName . "', '" . yii\helpers\Json::encode($dataCond) . "');
			setRadio('" . $inputName . "', '" . yii\helpers\Json::encode($dataCond) . "');
		    ");
            }
        }
    }

    public static function itemAlias($code, $key = NULL) {
        $itemStr['reportItems'] = [
            'bar_chart' => Yii::t('ezform', 'Bar Chart'),
            'pie' => Yii::t('ezform', 'Pie'),
            'line_graph' => Yii::t('ezform', 'Line graph'),
        ];

        $return = $itemStr[$code];

        if (isset($key)) {
            return isset($return[$key]) ? $return[$key] : false;
        } else {
            return isset($return) ? $return : false;
        }
    }

    public static function genJs($varArry, $model, $field) {
        $jsPath = [];
        $createEvent = '';
        $inputName = Html::getInputName($model, $field['ezf_field_name']);

        foreach ($varArry as $varName) {
            $inputNameEvent = Html::getInputName($model, $varName);
            $inputIdEvent = Html::getInputId($model, $varName);

            $eventSelector = "input[name=\"$inputNameEvent\"],select[name=\"$inputNameEvent\"]";
            $jsPath['{' . $varName . '}'] = "Number(getValue('$eventSelector', '$inputIdEvent'))";

            $createEvent .= "
                $('$eventSelector').on('change', function() {
                    autocal_{$field['ezf_field_name']}();

                });
            ";
        }
        $inputSelector = "input[name=\"$inputName\"],select[name=\"$inputName\"]";
        $calJs = strtr($field['ezf_field_cal'], $jsPath);

        $createEvent .= "
            function autocal_{$field['ezf_field_name']}(){
                $('$inputSelector').val($calJs);
            }
        ";
        return $createEvent;
    }

    public static function array2ConcatStr($fieldsArry) {
        $arry = SDUtility::string2Array($fieldsArry);
        if (is_array($arry) && !empty($arry)) {
            $concat = 'CONCAT(';
            $prefix = '';
            foreach ($arry as $fieldName) {
                $concat .= $prefix . "`$fieldName`";
                $prefix = ", ' ', ";
            }
            $concat .= ')';

            return $concat;
        }
        return false;
    }

    public static function arrayEncode2String($arry) {
        if (!empty($arry) && is_array($arry)) {
            return base64_encode(SDUtility::array2String($arry));
        }
        return '';
    }

    public static function stringDecode2Array($str) {
        if (!empty($str) && $str != '') {
            return SDUtility::string2Array(base64_decode($str));
        }
        return [];
    }

    public static function getEvenField($modelFields) {
        $arry = [];
        if (!empty($modelFields) && is_array($modelFields)) {
            foreach ($modelFields as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $arry['target'] = $value;
                } elseif ($value['ezf_special'] == 1) {
                    $arry['special'] = $value;
                }
            }
        }
        return $arry;
    }

    public static function getTargetField($modelFields) {
        $arry = [];
        if (!empty($modelFields) && is_array($modelFields)) {
            foreach ($modelFields as $key => $value) {
                if ($value['ezf_target'] == 1) {
                    $arry[] = $value;
                    return $arry;
                }
            }
        }
        return $arry;
    }

    public static function getSpecialField($modelFields) {
        $arry = [];
        if (!empty($modelFields) && is_array($modelFields)) {
            foreach ($modelFields as $key => $value) {
                if ($value['ezf_special'] == 1) {
                    $arry[] = $value;
                    return $arry;
                }
            }
        }
        return $arry;
    }

    public static function check_citizen($personID) {
//	if (strlen($pid) != 13) return false;
//        for ($i = 0, $sum = 0; $i < 12; $i++)
//            $sum += (int)($pid{$i}) * (13 - $i);
//        if ((11 - ($sum % 11)) % 10 == (int)($pid{12}))
//            return true;
//	
//        return false;
        if (isset($personID) && !empty($personID)) {
            if (strlen($personID) != 13) {
                return false;
            }

            $rev = strrev($personID); // reverse string ขั้นที่ 0 เตรียมตัว
            $total = 0;
            for ($i = 1; $i < 13; $i++) { // ขั้นตอนที่ 1 - เอาเลข 12 หลักมา เขียนแยกหลักกันก่อน
                $mul = $i + 1;
                $count = $rev[$i] * $mul; // ขั้นตอนที่ 2 - เอาเลข 12 หลักนั้นมา คูณเข้ากับเลขประจำหลักของมัน
                $total = $total + $count; // ขั้นตอนที่ 3 - เอาผลคูณทั้ง 12 ตัวมา บวกกันทั้งหมด
            }
            $mod = $total % 11; //ขั้นตอนที่ 4 - เอาเลขที่ได้จากขั้นตอนที่ 3 มา mod 11 (หารเอาเศษ)
            $sub = 11 - $mod; //ขั้นตอนที่ 5 - เอา 11 ตั้ง ลบออกด้วย เลขที่ได้จากขั้นตอนที่ 4
            $check_digit = $sub % 10; //ถ้าเกิด ลบแล้วได้ออกมาเป็นเลข 2 หลัก ให้เอาเลขในหลักหน่วยมาเป็น Check Digit
            if ($rev[0] == $check_digit) {  // ตรวจสอบ ค่าที่ได้ กับ เลขตัวสุดท้ายของ บัตรประจำตัวประชาชน
                return true; /// ถ้า ตรงกัน แสดงว่าถูก
            } else {
                return false; // ไม่ตรงกันแสดงว่าผิด 
            }
        } else {
            return false;
        }
    }

    public static function checkSpecial($model, $evenFields, $targetReset=false) {
        $fieldSpecial = '';
        if (isset($evenFields['special']) && !empty($evenFields['special'])) {
            if($targetReset){
                $model[$evenFields['special']['ezf_field_name']] = '';
            }
            
            $fieldSpecial = $evenFields['special']['ezf_field_name'];
            $special = $model[$evenFields['special']['ezf_field_name']];

            //$checkcid = EzfFunc::check_citizen($special);
            $specialCheck = isset($special) && !empty($special);
            if (!$specialCheck) {
                $specialFields = [$evenFields['special']];
                return NULL;
            }
        }

        return $fieldSpecial;
    }

    public static function genBtnEzform($model, $modelEzf) {
        $html = '';
        if (isset($modelEzf['query_tools']) && $modelEzf['query_tools'] == 2) {
            if ($model['rstat'] != 2) {
                $html .= Html::submitButton('Save Draft', ['class' => 'btn btn-success btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'submit', 'value' => '2', 'data-loading-text' => 'Loading...']);
                
            } else {
                if ((Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite'))) {
                    $html .= Html::submitButton('ReSaveDraft', ['class' => 'btn btn-warning btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                }
            }
        } elseif (isset($modelEzf['query_tools']) && $modelEzf['query_tools'] == 3) {
            if ($model['rstat'] != 2) {
                $html .= Html::submitButton('Save Draft', ['class' => 'btn btn-success btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'submit', 'value' => '2', 'data-loading-text' => 'Loading...']);
                
            } else {
                if ((Yii::$app->user->can('administrator') || Yii::$app->user->can('adminsite'))) {
                    $html .= Html::submitButton('ReSaveDraft', ['class' => 'btn btn-warning btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
                }
            }
        } else {
            $html .= Html::submitButton('Submit', ['class' => 'btn btn-primary btn-submit', 'name' => 'submit', 'value' => '1', 'data-loading-text' => 'Loading...']);
            
        }
        
        return $html;
    }
    
    public static function cloneRefField($field) {
        $modelRef = EzfQuery::getFieldByName($field->ref_ezf_id, $field->ref_field_id);
        $options = \appxq\sdii\utils\SDUtility::string2Array($field->ezf_field_options);
        $disabled = 0;
        if(isset($options['config']) && $options['config']==1){
            $disabled = 1;
        }

        $field->ezf_field_type = $modelRef->ezf_field_type;
        $field->ezf_field_data = $modelRef->ezf_field_data;
        $field->ezf_field_specific = $modelRef->ezf_field_specific;
        $field->ezf_field_options = $modelRef->ezf_field_options;
        
        return [
            'field'=>$field,
            'disabled'=>$disabled,
        ];
    }
    
    public static function updateDataRefField($target, $ezf_field_ref, $value) {
        $data_ref = EzfQuery::getRefFields($ezf_field_ref);
        $error = [];
        if($data_ref){
            foreach ($data_ref as $key => $ezvalue) {
                try {
                    Yii::$app->db->createCommand()->update($ezvalue['ezf_table'], [$ezvalue['ezf_field_name']=>$value], 'target=:target', [':target'=>$target])->execute();
                } catch (\yii\db\Exception $e) {
                    \backend\modules\ezforms2\classes\EzfFunc::addErrorLog($e);
                    $error[] = $e->getMessage();
                }
            }
        }
        
        return $error;
    }
    
    public static function addErrorLog($error) {
        //$error = new \yii\db\Exception();
        $model = new \backend\modules\ezforms2\models\SystemError();
        
        $model->id = SDUtility::getMillisecTime();
        $model->code = $error->getCode();
        $model->name = $error->getName();
        $model->message = $error->getMessage();
        $model->line = $error->getLine();
        $model->file = $error->getFile();
        $model->trace_string = $error->getTraceAsString();
        $model->created_by = Yii::$app->user->id;
        $model->created_at = new \yii\db\Expression('NOW()');
        
        $model->save();
    }
    
    public static function getLanguage(){
        $languageArry = explode('-', Yii::$app->language);
        if(isset($languageArry[0])){
            $language = $languageArry[0];
        } else {
            $language = 'en';
        }
        return $language;
    }
    
}
