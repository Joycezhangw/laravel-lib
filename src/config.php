<?php

return [
    'passport' => [
        'check_captcha_cache_key' => 'captcha_uniqid',
        'password_salt' => env('LANDAO_PASSPORT_PASSWORD_SALT', env('APP_KEY'))
    ],
    'crypt' => [
        'screct_key' => env('LANDAO_CRYPT_SCRECT_KEY', env('APP_KEY'))
    ],
    'captcha' => [
        'charset' => 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789',
        'codelen' => 4,
        'width' => 130,
        'height' => 50,
        // 为空为默认字体
        'font' => '',
        'fontsize' => 20,
        'cachetime' => 300,
    ],
    'paginate' => [
        'page_size' => 20
    ],
    'generator' => [
        'basePath' => app()->path(),
        'rootNamespace' => 'App\\',
        'paths' => [
            'models' => 'Services\\Models',
            'repositories' => 'Services\\Repositories',
            'interfaces' => 'Services\\Repositories',
            'enums' => 'Services\\Enums',
        ]
    ]
];
