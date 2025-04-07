<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Signature
    |--------------------------------------------------------------------------
    |
    | This is required and will be attached to the end of every message
    |
    */
    'signature' => 'Regards,<br />',

    /*
    |--------------------------------------------------------------------------
    | Footnote
    |--------------------------------------------------------------------------
    |
    | This is required and will be attached to the end of every message
    |
    */
    'footnote' => 'You are recieving this message because you are registered on :app_name',

    /*
    |--------------------------------------------------------------------------
    | Copyright
    |--------------------------------------------------------------------------
    |
    | This is required and will be attached to the end of every message
    |
    */
    'copyright' => '©:year :app_name, All Rights Reserved.',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    |
    | The message body is made up of lines, each line
    | represents a new line in the message sent, inline html is also supported
    | If a line is required to be a button it should be an array in the
    | following format: ['link' => 'https://tech4all.greysoft.ng/login', 'title' => 'Get Started', 'color' => '#fff']
    | the color property is optional.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | SendCode messages
    |--------------------------------------------------------------------------
    |
    | These are messages sent during account or request verification.
    |
    */
    'send_code::reset' => [
        'subject' => 'Reset your :app_name password.',
        'lines' => [
            'Hello :firstname,',
            'You are receiving this email because we received a password reset request for your account on :app_name.',
            'Use the code or link below to recover your account.',
            '<h3 style="text-align: center;">:code</h3>',
            [
                'link' => ':app_url/reset/password?token=:token',
                'title' => 'Reset Password',
            ],
            'This password reset code will expire in :duration.',
            'If you did not request a password reset, no further action is required.',
        ],
    ],
    'send_code::verify' => [
        'subject' => 'Verify your account on :app_name.',
        'lines' => [
            'Hello :firstname,',
            'You are receiving this email because you created an account on <b>:app_name</b> and we needed to verify that you own this :label. <br />Use the code or link below to verify your :label.',
            '<h3 style="text-align: center;">:code</h3>',
            [
                'link' => ':app_url/account/verify/:type?token=:token',
                'title' => 'Verify Account',
            ],
            'This verification code will expire in :duration.',
            'If you do not recognize this activity, no further action is required as the associated account will be deleted in few days if left unverified.',
        ],
        'allowed' => ['html'],
    ],
    'send_code::verify_phone' => [
        'subject' => 'Verify your phone number on :app_name.',
        'lines' => [
            'use this code :code to verify your :app_name phone number, It expires in :duration.',
        ],
        'allowed' => ['plain'],
    ],
    'send_code::otp' => [
        'subject' => 'Your One Time Password.',
        'lines' => [
            'Use the code below to verify your request.',
            '<h3 style="text-align: center;">:code</h3>',
            'This OTP will expire in :duration.',
            'If you do not recognize this request no further action is required or you can take steps to secure your account.',
        ],
    ],
    'send_report' => [
        'subject' => ':form_name Report is Ready.',
        'lines' => [
            'Your :period report report for :form_name is ready!',
            [
                'link' => ':link',
                'title' => 'Download Report',
            ],
            'For security and privacy concerns this link expires in :ttl and is only usable once',
            'If you have any concerns please mail <a href="mailto::mailto">:mailto</a> for support.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | SendVerified messages
    |--------------------------------------------------------------------------
    |
    | These are messages sent after an account is verified.
    |
    */
    'send_verified' => [
        'subject' => 'Welcome to the :app_name community.',
        'lines' => [
            'Hello :firstname,',
            'Your :app_name account :label has been verified sucessfully and we want to use this opportunity to welcome you to our community.',
            [
                'link' => ':app_url/login',
                'title' => 'Get Started',
            ],
        ],
    ],
    'send_verified:sms' => [
        'subject' => 'Welcome to the :app_name community.',
        'lines' => [
            'Hello :firstname,',
            'Your :app_name account :label has been verified sucessfully, welcome to our community.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Other messages
    |--------------------------------------------------------------------------
    |
    |
    */
    'email_verification' => [
        'subject' => 'Please verify your :label.',
        'lines' => [
            'Hello :fullname,',
            'You initiated an account opening proccess at :app_name, please use the code below to complete your request',
            '<h3 style="text-align: center;">:code</h3>',
            'If you need any further assistance please reachout to support.',
        ],
    ],
    'welcome' => [
        'subject' => ':firstname, welcome to :app_name.',
        'lines' => [
            'Hello :fullname,',
            "We are happy to have you onboard :app_name, your registration was successfull and we can't wait to see what you do next.",
            "If you do need any assistance please don't fail to reachout to one of our numerous support channels.",
        ],
    ],
];
