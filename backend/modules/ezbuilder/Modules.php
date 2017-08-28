<?php

namespace backend\modules\ezbuilder;

use Yii;
/**
 * ezbuilder module definition class
 */
class Modules extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'backend\modules\ezbuilder\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
            parent::init();
            if (!isset(Yii::$app->i18n->translations['ezform'])) {
                    Yii::$app->i18n->translations['ezform'] = [
                            'class' => 'yii\i18n\PhpMessageSource',
                            'sourceLanguage' => 'en',
                            'basePath' => '@backend/modules/ezforms2/messages'
                    ];
            }
    }
}
