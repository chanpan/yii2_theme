<?php
namespace common\modules\user\models;

use dektrium\user\models\Profile as BaseProfile;
use Yii;
use backend\modules\core\classes\CoreQuery;
use backend\modules\core\classes\CoreFunc;
use yii\helpers\ArrayHelper;

class Profile extends BaseProfile
{
    public $dynamicFields;
    public $blocked_at;
    public $flags;
    public $flags_id;
    
    public function init() {
	parent::init();
	$this->dynamicFields = isset(Yii::$app->params['profilefields'])?Yii::$app->params['profilefields']:[];
    }
    
    public function rules()
    {
	$rules = [
            'bioString' => ['bio', 'string'],
            'publicEmailPattern' => ['public_email', 'email'],
            'gravatarEmailPattern' => ['gravatar_email', 'email'],
            'websiteUrl' => ['website', 'url'],
            'nameLength' => ['name', 'string', 'max' => 255],
            'publicEmailLength' => ['public_email', 'string', 'max' => 255],
            'gravatarEmailLength' => ['gravatar_email', 'string', 'max' => 255],
            'locationLength' => ['location', 'string', 'max' => 255],
            'websiteLength' => ['website', 'string', 'max' => 255],
        ];
	
        return ArrayHelper::merge($rules, CoreFunc::getTableRules('profile'));
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
	$labels = [
            'name'           => Yii::t('user', 'Nickname'),
            'public_email'   => Yii::t('user', 'Email (public)'),
            'gravatar_email' => Yii::t('user', 'Gravatar email'),
            'location'       => Yii::t('user', 'Location'),
            'website'        => Yii::t('user', 'Website'),
            'bio'            => Yii::t('user', 'Bio'),
        ];
	
	$dynamicFields = $this->dynamicFields;
	foreach ($dynamicFields as $key => $value) {
            $labels["{$value['table_varname']}"] = isset($value['input_label']) ? Yii::t('user', $value['input_label']) : Yii::t('user', $value['table_varname']);
	}
	
        return $labels;
    }
    
    
}