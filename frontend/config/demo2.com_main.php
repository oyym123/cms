<?php

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
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
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'animals' => '/fan',
                'animals/<id:\d+>.html' => '/fan/detail',
                'animals/index_<id:\d+>.html' => '/fan',
                
                'flowers' => '/fan',
                'flowers/<id:\d+>.html' => '/fan/detail',
                'flowers/index_<id:\d+>.html' => '/fan',
                
                'water' => '/fan',
                'water/<id:\d+>.html' => '/fan/detail',
                'water/index_<id:\d+>.html' => '/fan',
                
                'home' => '/fan',
                'home/<id:\d+>.html' => '/fan/detail',
                'home/index_<id:\d+>.html' => '/fan',
                
                'index_<id:\d+>.html' => '/site/index',
                //end 正则注释识别 勿删
            ],
        ],

        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
