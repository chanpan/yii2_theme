<?php

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'aliases' => [
	'@appxq/sdii' => '@common/lib/yii2-sdii',
	'@appxq/admin' => '@common/lib/yii2-admin',
        '@dee/angular' => '@common/lib/yii2-angular',
    ],
    'bootstrap' => [
        'queue', // The component registers own console commands
    ],
    'modules' => [],
    'components' => [
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'as log' => \yii\queue\LogBehavior::class,
            'db' => 'db', // DB connection component or its config 
            'tableName' => '{{%queue}}', // Table name
            'channel' => 'default', // Queue channel key
            'mutex' => \yii\mutex\MysqlMutex::class, // Mutex that used to sync queries
        ],
        'cache' => [
	    'class' => 'yii\caching\FileCache',
	],
	'urlManager' => [
	    'class' => 'yii\web\UrlManager',
	    'showScriptName' => false, // Disable index.php
	    'enablePrettyUrl' => true, // Disable r= routes
	    'rules' => [
		'<controller:\w+>/<id:\d+>' => '<controller>',
		'<controller:\w+>/<action:\w+>/<*:*>' => '<controller>/<action>/<*>',
		'<module:\w+>/<controller:\w+>/<id:\d+>' => '<module>/<controller>',
		'<module:\w+>/<controller:\w+>/<action:\w+>/<*:*>' => '<module>/<controller>/<action>/<*>',
	    ],
	],
	'authManager' => [
	    'class' => 'yii\rbac\DbManager',
	    'defaultRoles' => ['guest', 'user'],
	    'cache' => 'yii\caching\FileCache'
	],
    ],
];
