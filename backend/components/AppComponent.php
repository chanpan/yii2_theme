<?php

namespace backend\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use yii\web\Request;

class AppComponent extends Component {

    public function init() {
        parent::init();
        Yii::$app->params['sidebar'] = [];
        
        //Yii::$app->params['profilefields'] = \backend\modules\core\classes\CoreQuery::getTableFields('profile');
        $params = \backend\modules\core\classes\CoreQuery::getOptionsParams();
        Yii::$app->params = \yii\helpers\ArrayHelper::merge(Yii::$app->params, $params);
    }

    public static function navbarMenu() {
	Yii::$app->params['navbar'] = [
	    ['label' => '<i class="fa fa-magic"></i> '.Yii::t('appmenu', 'EzForm'), 'encode' => FALSE, 'url' => ['//ezforms2/ezform/index']],
            ['label' => '<i class="fa fa-cube"></i> '.Yii::t('appmenu', 'Modules'), 'encode' => FALSE, 'url' => ['#']],
            ['label' => '<i class="glyphicon glyphicon-plus"></i> '.Yii::t('appmenu', 'Data Lists'), 'encode' => FALSE, 'url' => ['#']],
            ['label' => '<i class="glyphicon glyphicon-export"></i> '.Yii::t('appmenu', 'Export'), 'encode' => FALSE, 'url' => ['#']],
//	    ['label' => 'About', 'url' => ['/site/about']],
//	    ['label' => 'Contact', 'url' => ['/site/contact']],
	];
    }

    public static function navbarRightMenu() {
        if (Yii::$app->user->isGuest) {
            Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-user-plus"></i> '.Yii::t('appmenu', 'Sign up'), 'encode' => FALSE, 'url' => ['/user/registration/register']];
            Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-sign-in"></i> '.Yii::t('appmenu', 'Sign in'), 'encode' => FALSE, 'url' => ['/user/security/login']];
        } else {
            Yii::$app->params['navbarR'][] = ['label' => '<i class="fa fa-sliders"></i> '.Yii::t('appmenu', 'Account({name})', ['name'=>Yii::$app->user->identity->username]), 'encode' => FALSE, 'items' => [
                    ['label' => '<i class="fa fa-user"></i> '.Yii::t('appmenu', 'Profile'), 'encode' => FALSE, 'url' => ['/user/settings/profile']],
                    ['label' => '<i class="fa fa-key"></i> '.Yii::t('appmenu', 'Account'), 'encode' => FALSE, 'url' => ['/user/settings/account']],
                    ['label' => '<i class="fa fa-facebook-official"></i> '.Yii::t('appmenu', 'Networks'), 'encode' => FALSE, 'url' => ['/user/settings/networks']],
                    ['label' => '<i class="fa fa-users"></i> '.Yii::t('appmenu', 'Manage users'), 'encode' => FALSE, 'url' => ['/user/admin/index']],
                    ['label' => '<i class="fa fa-sign-out"></i> '.Yii::t('appmenu', 'Logout'), 'encode' => FALSE, 'url' => ['/user/security/logout'], 'linkOptions' => ['data-method' => 'post']],
            ]];
        }
    }

    public static function sidebarMenu($moduleID, $controllerID, $actionID) {
        $group = 'item';
        if (isset($_GET['group']) && in_array($_GET['group'], ['person', 'place', 'item'])) {
            $group = $_GET['group'];
        }
        
        $errors = \backend\modules\ezforms2\models\SystemError::find()->count();
        
	Yii::$app->params['sidebar'] = [
	    ['label' => Yii::t('appmenu', 'Dashboard'), 'icon' => 'glyphicon glyphicon-dashboard', 'url' => ['//site/index']],
   
	    ['label' => Yii::t('appmenu', 'EzForms Config'), 'icon' => 'fa fa-archive', 'url' => '#', 'active' => (in_array($moduleID, [
		    'ezforms2',
                    'ezform_builder',
		])), 'items' => [
		    ['label' => Yii::t('appmenu', 'EzForm'), 'icon' => 'fa fa-magic', 'url' => ['//ezforms2/ezform/index'], 'active' => $controllerID == 'ezform'],
                    ['label' => Yii::t('appmenu', 'EzModules'), 'icon' => 'fa fa-cube', 'url' => ['#'], ],
                    ['label' => Yii::t('appmenu', 'Activity Category'), 'icon' => 'fa fa-sitemap', 'url' => ['#'], ],
                    ['label' => Yii::t('appmenu', 'EzInput'), 'icon' => 'fa fa-wrench', 'url' => ['//ezforms2/ezform-input/index'], 'active' => $controllerID == 'ezform-input'],
                    ['label' => Yii::t('appmenu', 'System Errors'). ' <span class="badge">'.$errors.'</span>', 'encode'=>false, 'icon' => 'glyphicon glyphicon-warning-sign', 'url' => ['/ezforms2/system-error/index']],
		]
	    ],

	    ['label' => Yii::t('appmenu', 'System Config'), 'icon' => 'fa fa-cog', 'visible'=>(Yii::$app->user->can('administrator')), 'url' => '#', 'active' => (in_array($controllerID, [
		    'core-fields',
		    'core-generate',
		    'core-options',
		    'core-item-alias',
		    'tables-fields',
		    'tb-faculty',
		    'tb-department',
		]) || in_array($moduleID, [
		    'admin',
		])), 'items' => [
		    ['label' => Yii::t('appmenu', 'Enterprise information'), 'icon' => 'fa fa-location-arrow', 'url' => ['//core/core-options/config']],
		    ['label' => Yii::t('appmenu', 'Generate'), 'icon' => 'fa fa-puzzle-piece', 'active' => $controllerID == 'core-generate', 'url' => ['//core/core-generate']],
		    ['label' => Yii::t('appmenu', 'Options Config'), 'icon' => 'fa fa-sliders', 'active' => ($controllerID == 'core-options' && $actionID !== 'config'), 'url' => ['//core/core-options']],
		    ['label' => Yii::t('appmenu', 'Input Fields'), 'icon' => 'fa fa-plug', 'active' => $controllerID == 'core-fields', 'url' => ['//core/core-fields']],
		    ['label' => Yii::t('appmenu', 'Item Alias'), 'icon' => 'fa fa-share-alt', 'active' => $controllerID == 'core-item-alias', 'url' => ['//core/core-item-alias']],
		    ['label' => Yii::t('appmenu', 'Authentication'), 'icon' => 'fa fa-cogs', 'active' => in_array($moduleID, ['admin']), 'url' => ['//admin']],
		    ['label' => Yii::t('appmenu', 'Tables Fields'), 'icon' => 'fa fa-magic', 'active' => $controllerID == 'tables-fields', 'url' => '#', 'items' => [
			    ['label' => Yii::t('appmenu', 'Profile'), 'icon' => 'fa fa-chevron-right', 'active' => ($controllerID == 'tables-fields' && $_GET['table'] == 'profile'), 'url' => ['//core/tables-fields', 'table' => 'profile']],
			]],
		]
	    ],
	];
    }

}
