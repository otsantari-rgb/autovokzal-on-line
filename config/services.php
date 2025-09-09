<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'yandex_maps' => [
        'api_key' => env('YANDEX_MAPS_API_KEY'),
    ],

    'yookassa' => [
        'shopId' => env('YOOKASSA_SHOP_ID', ''),
        'secretKey' => env('YOOKASSA_SECRET_KEY', ''),
    ],

    'biletavto' => [
        'url' => env('BILETAVTO_API_URL'),
        'username' => env('BILETAVTO_API_USERNAME'),
        'password' => env('BILETAVTO_API_PASSWORD'),
        'inn' => env('BILETAVTO_INN', ''),
    ],
    'atol' => [
        'api_version' => env('ATOL_API_VERSION', 'v4'),
        'group_code' => env('BILETAVTO_ATOL_GROUP_CODE', ''),
        'api_url' => env('ATOL_API_URL', ''),
        'login' => env('BILETAVTO_ATOL_LOGIN', ''),
        'password' => env('BILETAVTO_ATOL_PASSWORD', ''),
    ]
];
