<?php


return [

    'default' => env('MAIL_MAILER', 'smtp'), // use this instead of MAIL_DRIVER

    'mailers' => [

        'smtp' => [
            // 'transport' => 'smtp',*
            // 'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            // 'port' => env('MAIL_PORT', 587),
            // 'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            // 'username' => env('MAIL_USERNAME'),
            // 'password' => env('MAIL_PASSWORD'),
            // 'timeout' => null,
            'transport' => 'smtp',
            'host' => env('MAIL_HUAWEI_HOST'),
            'port' => env('MAIL_HUAWEI_PORT'),
            'encryption' => env('MAIL_HUAWEI_ENCRYPTION'),
            'username' => env('MAIL_HUAWEI_USERNAME'),
            'password' => env('MAIL_HUAWEI_PASSWORD'),
            'timeout' => null,
        ],

        'mail2' => [
            'transport' => 'smtp',
            'host' => env('MAIL2_HOST'),
            'port' => env('MAIL2_PORT'),
            'encryption' => env('MAIL2_ENCRYPTION'),
            'username' => env('MAIL2_USERNAME'),
            'password' => env('MAIL2_PASSWORD'),
            'timeout' => null,
        ],
        'huawei_email' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HUAWEI_HOST'),
            'port' => env('MAIL_HUAWEI_PORT'),
            'encryption' => env('MAIL_HUAWEI_ENCRYPTION'),
            'username' => env('MAIL_HUAWEI_USERNAME'),
            'password' => env('MAIL_HUAWEI_PASSWORD'),
            'timeout' => null,
        ],

        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        'array' => [
            'transport' => 'array',
        ],
    ],

    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'info@slgtrax.com'),
        'name' => env('MAIL_FROM_NAME', 'TRAX'),
    ],

    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],

];
