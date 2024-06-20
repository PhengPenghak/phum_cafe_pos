<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

use \yii\web\Request;

$baseUrl = str_replace('/web', '', (new Request)->getBaseUrl());
$config = [
    'id' => 'gdmc-website',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Admin',
            'as beforeRequest' => [  //if guest user access site so, redirect to login page.
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ],
    ],
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js'
                    ]
                ],
                'yii\bootstrap4\BootstrapAsset' => [
                    'css' => [
                        YII_ENV_DEV ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap4\BootstrapPluginAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                    ]
                ]
            ]
        ],
        'reCaptcha' => [
            'class' => 'himiklab\yii2\recaptcha\ReCaptchaConfig',
            'siteKeyV2' => '6LdTk4MjAAAAAHE2XK9OGU71iyOq6Qv8Ya8WCmcH',
            'secretV2' => '6LdTk4MjAAAAAAR3rph_t_zdmZGp30AhmzyS_qtl',
            'siteKeyV3' => '6Ldlv1ceAAAAAMXESJDuxmabT-usI6YyqAux-iJc',
            'secretV3' => '6Ldlv1ceAAAAAFrvfw3gnkjZgcbGKTwiT8Cb0w47',
        ],
        'formater' => [
            'class' => 'app\components\Formater',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '+)UZVDt!*G$8EVXQgzxa',
            'baseUrl' => $baseUrl,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\admin\models\User',
            'enableAutoLogin' => false,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mandrillapp.com',
                'username' => 'Serva Travel',
                'password' => 'md-HM3rrzPXpH5ZGXOiQpzCXw',
                'port' => 587,
                'encryption' => 'tls',
                // 'dsn' => 'native://default',
            ],

        ],

        // 'mailer' => [
        //     'class' => 'yii\swiftmailer\Mailer',
        //     'useFileTransport' => false,

        //     'transport' => [
        //         'class' => 'Swift_SmtpTransport',
        //         'host' => 'smtp.hostinger.com',
        //         'username' => 'penghak@dernham.app',
        //         'password' => '3Kt3RzXF9vJPDqG@',
        //         'port' => '587',
        //         'encryption' => 'tls',
        //     ],
        // ],
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
            'baseUrl' => $baseUrl,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            // 'enableStrictParsing' => true,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:^a-zZ-A>' => '<controller>/<action>',

                [
                    'pattern' => 'destination',
                    'route' => 'destination/index',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'destination/<slug:[^/.]*>',
                    'route' => 'destination/view',
                    'suffix' => '',
                ],

                [
                    'pattern' => 'product-style',
                    'route' => 'product-style/index',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'product-style/<slug:[^/.]*>',
                    'route' => 'product-style/view',
                    'suffix' => '',
                ],

                [
                    'pattern' => 'service',
                    'route' => 'service/index',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'service/<slug:[^/.]*>',
                    'route' => 'service/view',
                    'suffix' => '',
                ],

                [
                    'pattern' => 'experience',
                    'route' => 'experience/index',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'experience/<slug:[^/.]*>',
                    'route' => 'experience/view',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'experience/contribute/<slug:[^/.]*>',
                    'route' => 'experience/contribute',
                    'suffix' => '',
                ],

                [
                    'pattern' => 'about',
                    'route' => 'about/index',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'contact',
                    'route' => 'contact/index',
                    'suffix' => '',
                ],

                [
                    'pattern' => 'privacy-policy',
                    'route' => 'site/privacy-policy',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'term-condition',
                    'route' => 'site/term-condition',
                    'suffix' => '',
                ],
                [
                    'pattern' => 'partner',
                    'route' => 'site/partner',
                    'suffix' => '',
                ],

            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    // $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}
$config['timezone'] = 'Asia/Phnom_Penh';

return $config;
