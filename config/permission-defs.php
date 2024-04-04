<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role List
    |--------------------------------------------------------------------------
    |
    | These are roles that will be made available  to the user.
    | Feel free to add or remove as per your requirements.
    */
    'roles' => [
        'admin',
        'manager',
        'super-admin'
    ],
    /*
    |--------------------------------------------------------------------------
    | Permission List
    |--------------------------------------------------------------------------
    |
    | These are permissions will be attached to all roles unless they appear in
    | the exclusionlist.
    | Feel free to add or remove as per your requirements.
    */
    'permissions' => [
        'manage-users',
        'manage-admins',
        'manage-configuration',
    ],
    /*
    |--------------------------------------------------------------------------
    | Exclusion List
    |--------------------------------------------------------------------------
    |
    | If there are permisions you do not want to attach to a particlular role
    | you can add them here using the role name as key.
    */
    'exclusions' => [
        'admin' =>  ['manage-admins'],
        'manager' =>  ['manage-admins'],
    ]
];
