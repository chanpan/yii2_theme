<?php

$params = array_merge(
	require(__DIR__ . '/../../common/config/params.php'), require(__DIR__ . '/../../common/config/params-local.php'), require(__DIR__ . '/params.php'), require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'language' => 'th',
    'bootstrap' => ['log', 'backend\components\AppComponent', 'languagepicker', 'admin'],
    'modules' => [
		'core' => [
			'class' => 'backend\modules\core\Module',
		],
                'ezforms2' => [
			'class' => 'backend\modules\ezforms2\Module',
		],
                'ezbuilder' => [
                    'class' => 'backend\modules\ezbuilder\Modules',
                ],
                'treemanager' =>  [
                    'class' => '\kartik\tree\Module',
                    // other module settings, refer detailed documentation
                ],
                'eztest' => [
                    'class' => 'backend\modules\eztest\Module',
                ],
		'admin' => [
			'class' => 'appxq\admin\Module',
			//'layout' => 'left-menu', // defaults to null, using the application's layou
			'layout' => '@app/views/layouts/main.php',
			'controllerMap' => [
			'assignment' => [
				'class' => 'appxq\admin\controllers\AssignmentController',
			//'userClassName' => 'common\modules\user\models\User', // fully qualified class name of your User model
			// Usually you don't need to specify it explicitly, since the module will detect it automatically
			//'idField' => 'user_id',        // id field of your User model that corresponds to Yii::$app->user->id
			//'usernameField' => 'username', // username field of your User model
			//'searchClass' => 'app\models\UserSearch'    // fully qualified class name of your User model for searching
			]
			],
		],
		'user' => [
			'class' => 'dektrium\user\Module',
			'enableUnconfirmedLogin' => true,
			'confirmWithin' => 21600,
			'cost' => 12,
			'admins' => ['admin'],
			'modelMap' => [
			'User' => 'common\modules\user\models\User',
			'Profile' => 'common\modules\user\models\Profile',
			'RegistrationForm' => 'common\modules\user\models\RegistrationForm',
			],
			'controllerMap' => [
			'admin' => 'common\modules\user\controllers\AdminController',
			'settings' => 'common\modules\user\controllers\SettingsController',
			'registration' => 'common\modules\user\controllers\RegistrationController',
			],
		],		
    ],
    'components' => [
	'user' => [
	    'identityClass' => 'dektrium\user\models\User',
	    'enableAutoLogin' => true,
	],
	'log' => [
	    'traceLevel' => YII_DEBUG ? 3 : 0,
	    'targets' => [
		[
		    'class' => 'yii\log\FileTarget',
		    'levels' => ['error', 'warning'],
		],
	    ],
	],
	'errorHandler' => [
	    'errorAction' => 'site/error',
	],
	'view' => [
	    'theme' => [
		'pathMap' => [
		    '@backend/views' => '@backend/themes/admin/views',
		    '@dektrium/user/views' => '@common/modules/user/views',
		],
	    ],
	],
	'i18n' => [
	    'translations' => [
		'*' => [
		    'class' => 'yii\i18n\PhpMessageSource',
		    'basePath' => '@backend/messages', // if advanced application, set @frontend/messages
		    'sourceLanguage' => 'en-US',
		    'fileMap' => [
			'app' => 'app.php',
		    ],
		],
	    ],
	],
	'languagepicker' => [
	    'class' => 'lajax\languagepicker\Component',
	    'languages' => ['en-US', 'th'], // List of available languages (icons only)
	    'cookieName' => 'language', // Name of the cookie.
	    'expireDays' => 64, // The expiration time of the cookie is 64 days.
	    'callback' => function() {
		if (!\Yii::$app->user->isGuest) {
		    //		    $user = \Yii::$app->user->identity;
		    //		    $user->language = \Yii::$app->language;
		    //		    $user->save();
		}
	    }
	]
    ],
	'as access' => [
			'class' => 'appxq\admin\classes\AccessControl',
			'allowActions' => [
				'site/*',
				//'admin/*',
				//'admin/*',
				'user/*',
			//'some-controller/some-action',
			// The actions listed here will be allowed to everyone including guests.
			// So, 'admin/*' should not appear here in the production, of course.
			// But in the earlier stages of your development, you may probably want to
			// add a lot of actions here until you finally completed setting up rbac,
			// otherwise you may not even take a first step.
			]
    ],
    'params' => $params,
];
