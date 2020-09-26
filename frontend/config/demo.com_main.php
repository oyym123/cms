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
                'home' => '/fan',
                'home/<id:\d+>.html' => '/fan/detail',            
                'home/index_<id:\d+>.html' => '/fan',
                
                'wen' => '/fan',
                'wen/<id:\d+>.html' => '/fan/detail',            
                'wen/index_<id:\d+>.html' => '/fan',
                
                'j' => '/fan',
                'j/<id:\d+>.html' => '/fan/detail',            
                'j/index_<id:\d+>.html' => '/fan',
                
                'mao' => '/fan',
                'mao/<id:\d+>.html' => '/fan/detail',            
                'mao/index_<id:\d+>.html' => '/fan',
                
                'label' => '/fan/tags-list',        
                'label/index_<id:\d+>.html' => '/fan/tags-list',
                
                'jaks<id:\d+>mq/' => '/fan/tags-detail',
                'customize_<id:\d+>.html' => '/site/customize',
                'jaks' => '/fan',
                'jaks/<id:\d+>.html' => '/fan/detail',            
                'jaks/index_<id:\d+>.html' => '/fan',
                
                'index_<id:\d+>.html' => '/site/index',
                'site.xml' => '/site/site-xml',
                'site.txt' => '/site/site-txt',
                'm_site.xml' => '/site/site-mxml',
                'm_site.txt' => '/site/site-mtxt',
                'favicon.ico' => '/site/favicon',
                //end 正则注释识别 勿删{"is_jump":0,"jump_url":"http:\/\/www.baidu.com"}  &end_url
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
