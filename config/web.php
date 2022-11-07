<?php

use kartik\datecontrol\Module;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';
$config = [
    'id' => 'basic',
    'name' => 'Личный кабинет МИП',
    'language' => 'ru', // Set the language here
    'timeZone' => 'Europe/Moscow',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'issues/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',

        '@frontend' => dirname(dirname(__FILE__))

    ],
    'components' => [


        'i18n' => [
            'translations' => [
                'yii2mod.comments' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@yii2mod/comments/messages',
                ],
                // ...
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
         'formatter' => [
             'dateFormat'     => 'php:dd-mm-yyyy',
             'datetimeFormat' => 'php:d.m.Y в H:i',
             'timeFormat'     => 'php:H:i:s',
             'defaultTimeZone'=>'Europe/Moscow',
             'timeZone'=>'Europe/Moscow',
         ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '8qBcwwOjP2JV-hdzRgEtcwrUY-GNBX-L',
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
        ],
        'user' => [
            'identityClass' => 'dektrium\user\models\User',
            'enableAutoLogin' => true,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'messageConfig' => [
                'charset' => 'UTF-8',
            ],
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.yandex.com',
                'username' => 'info@advokat-malov.ru',
                'password' => '918020',
                'port' => '465',
                'encryption' => 'ssl',
            ],
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
        'db' => $db,

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => '@app/assets',   // do not publish the bundle
                    'js' => [
                        'js/jquery-3.2.1.min.js',
                        //'js/initexport.js'
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => '@app/assets',   // do not publish the bundle
                    'js' => ['js/popper.min.js', 'js/bootstrap.min.js']
                ],
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
            ],
            'linkAssets' => true
        ],

    ],
    'as beforeRequest' => [
        'class' => 'yii\filters\AccessControl',
        'rules' => [
            [
                'actions' => ['login', 'error','request'],
                'allow' => true,
            ],
            [
                'allow' => true,
                'roles' => ['@'],
            ],
        ],
        'denyCallback' => function(){
            return Yii::$app->response->redirect(['user/security/login']);
        },
    ],
    'modules' => [
        'treemanager' => [
            'class' => '\kartik\tree\Module',
            // other module settings, refer detailed documentation
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => true,
            'controllerMap' => [
                'admin' => 'app\controllers\AdminController',
                'settings' => 'app\controllers\SettingsController',
                'security' => [
                    'class' => 'dektrium\user\controllers\SecurityController',
                    'layout' => '@app/views/layouts/main-login.php',
                ],
                'recovery' => [
                    'class' => 'dektrium\user\controllers\RecoveryController',
                    'layout' => '@app/views/layouts/main-login.php',
                ],
            ],
            'urlPrefix' => 'user',
            'urlRules' => [
                '<action:(login|logout|auth)>' => 'security/<action>',
                '<action:(account)>' => 'settings/<action>',
                '<action:(request|reset)>' => 'recovery/<action>',
                '<action:(register|connect|confirm|resend)>' => 'registration/<action>',
            ],
            'confirmWithin' => 21600,
            'cost' => 12,
            'admins' => ['Чак Норрис'], //супер пользователь по умолчанию
        ],
        'languages' => [
            'class' => 'klisl\languages\Module',
            'default_language' => 'ru', //основной язык (по-умолчанию)
            'show_default' => false, //true - показывать в URL основной язык, false - нет
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {

    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '*.*.*.*']
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '*.*.*.*']
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
