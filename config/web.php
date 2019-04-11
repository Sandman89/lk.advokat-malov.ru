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
    'modules' => [
       /* 'comment' => [
            'class' => 'yii2mod\comments\Module',
            'enableInlineEdit' => true,
            'commentModelClass'=>'app\models\CommentModel'

        ],*/
        'treemanager' => [
            'class' => '\kartik\tree\Module',
            // other module settings, refer detailed documentation
        ],
        'user' => [
            'class' => 'dektrium\user\Module',
            'enableUnconfirmedLogin' => true,
            'controllerMap' => [
                'admin' => 'app\controllers\AdminController',
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
            'admins' => ['Иванов иван Иванович'], //супер пользователь по умолчанию
        ],
        'languages' => [
            'class' => 'klisl\languages\Module',
            'default_language' => 'ru', //основной язык (по-умолчанию)
            'show_default' => false, //true - показывать в URL основной язык, false - нет
        ],
        'datecontrol' => [
            'class' => 'kartik\datecontrol\Module',

            // format settings for displaying each date attribute (ICU format example)
            'displaySettings' => [
                Module::FORMAT_DATE => 'd-F-Y',
                Module::FORMAT_TIME => 'HH:mm',
                Module::FORMAT_DATETIME => 'php:d-M-Y h:i',
            ],

            // format settings for saving each date attribute (PHP format example)
            'saveSettings' => [
                Module::FORMAT_DATE => 'php:Y-m-d', // saves as unix timestamp
                Module::FORMAT_TIME => 'php:H:i:s',
                Module::FORMAT_DATETIME => 'php:Y-m-d H:i:s',
            ],

            // set your display timezone
            'displayTimezone' => 'UTC',

            // set your timezone for date saved to db
            'saveTimezone' => 'UTC',

            // automatically use kartik\widgets for each of the above formats
            'autoWidget' => true,

            // use ajax conversion for processing dates from display format to save format.
            'ajaxConversion' => true,

            // default settings for each widget from kartik\widgets used when autoWidget is true
            'autoWidgetSettings' => [
                Module::FORMAT_DATE => ['layout' => '{picker}{input}{remove}', 'removeButton' => ['position' => 'append'],'pluginOptions' => ['autoclose' => true]], // example
                Module::FORMAT_DATETIME => ['layout' => '{picker}{input}{remove}',
                    'pickerIcon'=>'<i class="glyphicon glyphicon-calendar kv-dp-icon"></i>',
                    'removeIcon'=>'<i class="glyphicon glyphicon-remove" aria-hidden="true"></i>','removeButton' => ['position' => 'append'],'pluginOptions' => ['autoclose' => true]], // setup if needed
                Module::FORMAT_TIME => [], // setup if needed
            ],

            // custom widget settings that will be used to render the date input instead of kartik\widgets,
            // this will be used when autoWidget is set to false at module or widget level.
            'widgetSettings' => [
                Module::FORMAT_DATETIME => [
                    'class' => '\bs\Flatpickr\FlatpickrWidget', // example
                    'options' => [
                        'locale' => 'ru',
                        'groupBtnShow' => true,
                        'options' => [
                            'class' => 'form-control',
                            'autocomplete' => 'off'
                        ],
                        'plugins' => [
                            'confirmDate' => [
                                'confirmIcon'=> "<i class='fa fa-check'></i>",
                                'confirmText' => 'OK',
                                'showAlways' => false,
                                'theme' => 'light',
                            ],
                        ],
                        'clientOptions' => [
                          //  'dateFormat' => 'd-M-Y',
                            'allowInput' => true,
                            'dateFormat'=> "d-M-Y H:i",
                            'defaultDate' =>  null,
                            'enableTime' => true,
                            'time_24hr' => true,
                        ]

                    ]
                ]
            ]
            // other settings
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {

    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1','5.166.244.9'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '5.164.4.55','176.212.224.30','5.164.4.19','5.166.244.9']
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
