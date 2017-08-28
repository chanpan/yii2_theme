<?php
namespace common\modules\user\models;

use dektrium\user\models\User as BaseUser;
use dektrium\user\helpers\Password;
use yii\log\Logger;
use Yii;
use dektrium\user\models\Token;
use yii\db\AfterSaveEvent;

class User extends BaseUser
{
    public $modelProfile;
    
    public function register()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        if ($this->module->enableConfirmation == false) {
            $this->confirmed_at = time();
        }

        if ($this->module->enableGeneratingPassword) {
            $this->password = Password::generate(8);
        }

        $this->trigger(self::BEFORE_REGISTER);
        //$this->trigger(self::USER_REGISTER_INIT);

        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        
        if ($this->save()) {
            //$this->trigger(self::USER_REGISTER_DONE);
            $this->trigger(self::AFTER_REGISTER);
            if ($this->module->enableConfirmation) {
                $token = \Yii::createObject([
                    'class' => Token::className(),
                    'type'  => Token::TYPE_CONFIRMATION,
                ]);
                $token->link('user', $this);
                $this->mailer->sendConfirmationMessage($this, $token);
            } else {
                \Yii::$app->user->login($this);
            }
            if ($this->module->enableGeneratingPassword) {
                $this->mailer->sendWelcomeMessage($this);
            }
            
            \Yii::$app->session->setFlash('info', \Yii::t('user', 'User has been registered'));
            \Yii::getLogger()->log('User has been registered', Logger::LEVEL_INFO);
	    
            if ($this->module->enableConfirmation) {
                /** @var Token $token */
                $token = Yii::createObject(['class' => Token::className(), 'type' => Token::TYPE_CONFIRMATION]);
                $token->link('user', $this);
            }

	    // the following three lines were added:
	    $auth = Yii::$app->authManager;
	    $authorRole = $auth->getRole('user');
	    $auth->assign($authorRole, $this->getId());

            return true;
        }

        \Yii::getLogger()->log('An error occurred while registering user account', Logger::LEVEL_ERROR);

        return false;
    }
    
    public function create()
    {
        if ($this->getIsNewRecord() == false) {
            throw new \RuntimeException('Calling "' . __CLASS__ . '::' . __METHOD__ . '" on existing user');
        }

        $this->confirmed_at = time();

        if ($this->password == null) {
            $this->password = Password::generate(8);
        }
        
        if ($this->username === null) {
            $this->generateUsername();
        }

        //$this->trigger(self::USER_CREATE_INIT);
        $this->trigger(self::BEFORE_CREATE);
        $this->id = \appxq\sdii\utils\SDUtility::getMillisecTime();
        
        if ($this->save()) {
            //$this->trigger(self::USER_CREATE_DONE);
            $this->trigger(self::AFTER_CREATE);
            
            $this->mailer->sendWelcomeMessage($this);
            \Yii::getLogger()->log('User has been created', Logger::LEVEL_INFO);
	    
	    // the following three lines were added:
	    $auth = Yii::$app->authManager;
	    $authorRole = $auth->getRole('user');
	    $auth->assign($authorRole, $this->getId());
	    
            return true;
        }

        \Yii::getLogger()->log('An error occurred while creating user account', Logger::LEVEL_ERROR);

        return false;
    }
    
    /** @inheritdoc */
    public function afterSave($insert, $changedAttributes)
    {
//        parent::afterSave($insert, $changedAttributes);
//        if ($insert) {
//            if ($this->modelProfile == null) {
//                $this->modelProfile = Yii::createObject(Profile::className());
//            }
//            $this->modelProfile->link('user', $this);
//        }
            if ($insert) {
                if ($this->modelProfile == null) {
                    $this->modelProfile = \Yii::createObject(Profile::className());
                    
                    $this->modelProfile->public_email = $this->email;
                    $this->modelProfile->gravatar_email = $this->email;
                   
                }
                $this->modelProfile->user_id = $this->id;
                
                $this->modelProfile->save(false);
            }

            $this->trigger($insert ? self::EVENT_AFTER_INSERT : self::EVENT_AFTER_UPDATE, new AfterSaveEvent([
                'changedAttributes' => $changedAttributes
            ]));
    }
    
}