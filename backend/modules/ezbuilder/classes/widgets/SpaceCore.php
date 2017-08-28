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
class SpaceCore extends Object {

    /**
     * Initializes this TextInput.
     */
    public function init() {
	
    }

    public function generateViewEditor($input, $model) {
	$view = Yii::$app->getView();
	
	return $view->renderAjax('/widgets/space/_view_editor', [
	    'model'=>$model,
	]);
    }
    
    public function generateViewInput($field) {
	$view = new View();
	if(Yii::$app->getRequest()->isAjax){
            $view = Yii::$app->getView();
        }
        
	return $view->renderAjax('/../../ezbuilder/views/widgets/space/_view_item', [
	    'field'=>$field,
	]);
    }

    public function generateOptions($input, $model) {
	return '';
    }
    
    public function generateValidations($input, $model) {
	return '';
    }
    
}
