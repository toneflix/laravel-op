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
    'site_name' => 'Laravel OP',
    'site_slogan' => 'Explore contents in the palm of your hands',
    'currency_symbol' => '$',
    'currency' => 'USD',
    'use_queue' => true,
    'prefered_notification_channels' => ['mail'], //['sms', 'mail'],
    'keep_successful_queue_logs' => true,
    'rich_stats' => true,
    'slack_debug' => false,
    'slack_logger' => false,
    'force_https' => true,
    'verify_email' => false,
    'verify_phone' => false,
    'token_lifespan' => 1,
    'frontend_link' => 'http://localhost',
    'payment_verify_url' => env('PAYMENT_VERIFY_URL', 'http://localhost:8080/payment/verify'),
    'default_banner' => null,
    'auth_banner' => null,
    'welcome_banner' => null,
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