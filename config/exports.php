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
    | You can use closures to format or transform data before export.
    |
    */

    'transformers' => [
        'created_at' => static fn(\Illuminate\Support\Carbon $date) => $date->isoFormat('MMM DD, YYYY'),
        'phone' => static fn(string $value) => str($value)->replace('+', ' +'),
    ]

];
