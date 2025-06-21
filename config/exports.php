<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Export List
    |--------------------------------------------------------------------------
    |
    | This option determines what can be exported from the application.
    | You can specify models and the corresponding columns that should
    | be included in the export process.
    |
    */

    'set' => [
        [
            'id' => 'users', // String
            'model' => \App\Models\User::class, // class-string
            'model_id' => null, // integer|string|null
            'name' => 'User Data', // string
            'keywords' => 'data,user data,exports,laravel op', // string
            'columns' => [
                // 'id',
                'firstname',
                'lastname',
                'email',
                'created_at',
            ] // string[]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Data Transformers
    |--------------------------------------------------------------------------
    |
    | This section allows defining value transformers for specific columns.
    | Transformers are Helpers created in the App\Helpers\Transformer class.
    | The first parameter is the name of the transformer (Helper) and the second
    | is an array of arguments to send pass to the transformer method.
    |
    */

    'transformers' => [
        'created_at' => ['formatDate', ['MMM DD, YYYY']],
        'phone' => ['stringReplace', ['+', ' +']],
    ]

];
