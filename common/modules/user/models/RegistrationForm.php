<?php
namespace common\modules\user\models;

use Yii;
use dektrium\user\models\RegistrationForm as BaseRegistrationForm;
use backend\modules\core\components\CoreFunc;
use backend\modules\core\components\CoreQuery;
use yii\helpers\ArrayHelper;
use common\modules\user\models\Profile;

class RegistrationForm extends BaseRegistrationForm
{
    /**
    * @var string
    */
    public $captcha;
    public $name;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
	$rules = parent::rules();
	
	$rules[] = ['name', 'required'];
        $rules[] = ['name', 'string', 'max' => 255];
//	$rules[] = ['captcha', 'required'];
//      $rules[] = ['captcha', 'captcha'];
	
	return $rules;
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['name'] = Yii::t('user', 'Nickname');
	
        return $labels;
    }
    
    /**
     * Registers a new user account.
     * @return bool
     */
    public function register()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = Yii::createObject(User::className());
        $user->setScenario('register');
        
        $user->setAttributes([
            'email'    => $this->email,
            'username' => $this->username,
            'password' => $this->password
        ]);

	/** @var Profile $profile */
        $profile = \Yii::createObject(Profile::className());
        $profile->setAttributes([
	    'name' => $this->name,
	    'public_email' => $this->email,
	    'gravatar_email' => $this->email,
        ]);
	$user->modelProfile = $profile;
	
        return $user->register();
    }
}