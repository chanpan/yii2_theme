<?php

namespace backend\modules\ezbuilder\classes\widgets;

use Yii;
use yii\base\Object;
use yii\base\InvalidConfigException;
use appxq\sdii\utils\SDUtility;
use yii\web\View;

/**
 * TextInput class file UTF-8
 * @author SDII <iencoded@gmail.com>
 * @copyright Copyright &copy; 2015 AppXQ
 * @license http://www.appxq.com/license/
 * @version 1.0.0 Date: 19 ส.ค. 2559 17:27:52
 * @link http://www.appxq.com/
 * @example backend\modules\ezforms2\classes\widgets\TextInput
 */
class DateTimeCore extends Object {

    /**
     * Initializes this TextInput.
     */
    public function init() {
//	if (self::BEHAVIOR_CLASS_NAME === null || self::BEHAVIOR_CLASS_NAME === '') {
//	    throw new InvalidConfigException('TextInput::BEHAVIOR_CLASS_NAME must be set.');
//	}
    }

    public function generateViewEditor($input, $model) {
	$options = SDUtility::string2Array($input['input_option']);
	$specific = SDUtility::string2Array($input['input_specific']);
	
	if (isset($options['class'])) {
	    $options['class'] .= ' form-control';
	} else {
	    $options['class'] = 'form-control';
	}
	
	$view = Yii::$app->getView();
	
	return $view->renderAjax('/widgets/datetime/_view_editor', [
	    'model'=>$model,
	    'options'=>$options,
	    'specific'=>$specific,
	]);
    }
    
    public function generateViewInput($field) {
	$options = SDUtility::string2Array($field['ezf_field_options']);
	$specific = SDUtility::string2Array($field['ezf_field_specific']);
	
	if (isset($options['class'])) {
	    $options['class'] .= ' form-control';
	} else {
	    $options['class'] = 'form-control';
	}
	
	$view = new View();
	if(Yii::$app->getRequest()->isAjax){
            $view = Yii::$app->getView();
        }
        
	return $view->renderAjax('/../../ezbuilder/views/widgets/datetime/_view_item', [
	    'field'=>$field,
	    'options'=>$options,
	    'specific'=>$specific,
	]);
    }

    public function generateOptions($input, $model) {
	$view = Yii::$app->getView();
	
	return $view->renderAjax('/widgets/datetime/_options', [
	    'model'=>$model,
	    'input'=>$input,
	]);
    }
    
    public function generateValidations($input, $model) {
	$view = Yii::$app->getView();
	
	return $view->renderAjax('/widgets/_default_validations', [
	    'model'=>$model,
	    'input'=>$input,
	]);
    }
    
}
