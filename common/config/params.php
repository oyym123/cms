<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'OnlineDomain' => 'http://116.193.169.122:89', //线上yii前端访问地址
    'QiNiuAccessKey' => '88o_D4CZVwHBAG8xHCDxd_a6gHuMLCgPjcvTBqT5',
    'QiNiuSecretKey' => 'Q-juNSlonLCnBfvGpF9NJwaYUn7FS9y0MTKECIAr',
    'QiNiuHost' => 'http://img.thszxxdyw.org.cn/',
    'QiNiuBucketImg' => 'aks-img01',
    'YouDaoAppKey' => '3c405b325744c3f6',
    'YouDaoSecKey' => 'yoZ2a3k94QRzlXiFq8mvLjGxJStgquOB',
    'rabbitmqConfig' => [
        'host' => '127.0.0.1',
        'port' => 5672,
        'user' => 'guest',
        'pwd' => 'guest',
        'vhost' => '/',
    ],
    'MqConfig' => [
        'exchange' => 'kd_sms_send_ex',
        'queue' => 'kd_sms_send_q'
    ],
];
