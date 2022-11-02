<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site specific Configuration
    |--------------------------------------------------------------------------
    |
    | These settings determine how the site is to be run or managed
    |
    */
    'site_name' => 'Greyflix.io',
    'site_slogan' => 'Explore contents and earn on the go!',
    'currency_symbol' => '$',
    'currency' => 'USD',
    'use_queue' => true,
    'prefered_notification_channels' => ['mail'], //['sms', 'mail'],
    'keep_successful_queue_logs' => true,
    'strict_mode' => false, // Setting to true will prevent the Vcard Engine from generating Vcards with repeated content
    'rich_stats' => true,
    'slack_debug' => false,
    'slack_logger' => false,
    'force_https' => true,
    'verify_email' => false,
    'verify_phone' => false,
    'token_lifespan' => 1,
    'frontend_link' => 'https://greyflix.qreysoft.com.ng',
    'payment_verify_url' => env('PAYMENT_VERIFY_URL', 'http://localhost:8080/payment/verify'),
    'default_banner' => 'http://127.0.0.1:8000/media/images/1930222852_329608130.jpg',
    'auth_banner' => 'http://127.0.0.1:8000/media/images/474001152_1896452044.jpg',
    'welcome_banner' => 'http://127.0.0.1:8000/media/images/1454582469_1019897966.jpg',
    'paystack_public_key' => env('PAYSTACK_PUBLIC_KEY', 'pk_'),
    'trx_prefix' => 'TRX-',
    'contact_address' => '31 Gwari Avenue, Barnawa, Kaduna',

    'system' => [
        'paystack' => [
            'secret_key' => env('PAYSTACK_SECRET_KEY', 'sk_'),
        ],
        'ipinfo' => [
            'access_token' => env('IPINFO_ACCESS_TOKEN'),
        ],
    ],
];
