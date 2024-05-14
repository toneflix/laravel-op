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
    'signature' => "Isah Raphael<br />CEO<br />Greysoft Technologies Limited",

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
    'new_message' => [
        "subject" => "New Message Received from :username",
        "lines" => [
            ":fullname sent you a message.",
            [
                'link' => 'https://example.com/read',
                'title' => 'Read It',
                'color' => '#fff'
            ],
            "You can choose to read it or ignore it.",
        ],
    ],
];
