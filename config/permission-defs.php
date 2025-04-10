<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | When syncing roles and permissions using the app:sync-roles command, this
    | model will be considered as the user model and used to associte roles and
    | permissions to the indicated users.
    */
    'user-model' => \App\Models\User::class,
    /*
    |--------------------------------------------------------------------------
    | Super Admin Role
    |--------------------------------------------------------------------------
    |
    | This is the role that would be considered as the super admin role.
    */
    'super-admin-role' => 'super-admin',
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
        'super-admin',
    ],
    /*
    |--------------------------------------------------------------------------
    | Elevated Role List
    |--------------------------------------------------------------------------
    |
    | Users with any of the roles listed here are considered to have elevated access
    | Listed roles should already be defined in [roles] above.
    */
    'elevated-roles' => [
        'admin',
        'super-admin',
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
        'dashboard',
        'transactions',
        'manage-users',
        'manage-admins',
        'configuration',
        'manage-configuration',

        'users.user',
        'users.list',
        'users.create',
        'users.update',
        'users.delete',

        'notifications-temps',
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
        'admin' => ['manage-admins'],
        'manager' => ['manage-admins'],
    ],
    /*
    |--------------------------------------------------------------------------
    | Admin Roles
    |--------------------------------------------------------------------------
    |
    | Users with any of the following roles are considered admins in general contexts.
    */
    'admin_roles' => [
        'super-admin',
        'admin',
        'manager',
        'support',
        'user',
    ],
];
