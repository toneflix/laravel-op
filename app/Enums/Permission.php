<?php

namespace App\Enums;

/**
 * Admin Permissions.
 */
enum Permission: string
{
    case MANAGE_USERS = 'manage-users';
    case MANAGE_ADMINS = 'manage-admins';
    case CONFIGURATION = 'configuration';
    case MANAGE_CONFIGURATION = 'manage-configuration';
    case NOTIFICATIONS_TEMPS = 'notifications-temps';

    case USERS_LIST = 'users.list';
    case USERS_CREATE = 'users.create';
    case USERS_USER = 'users.user';
    case USERS_UPDATE = 'users.update';
    case USERS_DELETE = 'users.delete';

    case DASHBOARD = 'dashboard';
    case TRANSACTIONS = 'transactions';

    /**
     * Check and authorise the admin on the current permission
     *
     * @return void
     */
    public function authorize()
    {
        \App\Helpers\Access::authorize($this);
    }

    /**
     * Check and authorise the admin on muliptle permissions
     *
     * @param  self|array<int,self>  $permission
     * @return void
     */
    public static function authorizeAll(self|array $permission)
    {
        \App\Helpers\Access::authorize($permission);
    }
}
