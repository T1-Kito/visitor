<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'kiosk' => [
        'company_name' => env('KIOSK_COMPANY_NAME', env('APP_NAME', 'Công ty ABC')),
        'system_name' => env('KIOSK_SYSTEM_NAME', 'VMS Kiosk'),
        'subtitle' => env('KIOSK_SUBTITLE', 'Giao diện tự động cho khách đến công ty'),
        'welcome_title' => env('KIOSK_WELCOME_TITLE', null),
        'welcome_description' => env('KIOSK_WELCOME_DESCRIPTION', 'Vui lòng đăng ký thông tin hoặc check-in bằng QR để được hỗ trợ nhanh chóng.'),
        'hotline' => env('KIOSK_RECEPTION_HOTLINE', '1900 0000'),
        'working_hours' => env('KIOSK_WORKING_HOURS', '07:30 - 18:00'),
        'logo_url' => env('KIOSK_LOGO_URL', null),
        'background_url' => env('KIOSK_BACKGROUND_URL', null),
        'primary_color' => env('KIOSK_PRIMARY_COLOR', '#146bd7'),
    ],

];