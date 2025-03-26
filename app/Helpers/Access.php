<?php

namespace App\Helpers;

use App\Enums\HttpStatus;
use App\Enums\Permission;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class Access
{
    /**
     * Check and authorise the admin
     *
     * @param  Permission|array<int,Permission>  $permission
     * @param  ?User  $admin
     * @param  mixed  ...$params
     * @return void
     */
    public static function authorize(Permission|array $permission, ?User $admin = null, ...$params)
    {
        $admin ??= auth('sanctum')->user();

        if (is_array($permission)) {
            Gate::forUser($admin)->authorize('any-permission', [$permission, ...$params]);
        } else {
            Gate::forUser($admin)->authorize($permission->value, $params);
        }
    }

    /**
     * Check and authorise the admin
     *
     * @param  Permission|array<int,Permission>  $permission
     * @param  ?User  $admin
     * @param  mixed  ...$params
     * @return void
     */
    public static function authorizeForm(Permission|array $permission, ?User $admin = null, ...$params)
    {
        $admin ??= auth('sanctum')->user();

        Gate::forUser($admin)->authorize('form-permission', [
            is_array($permission) ? $permission : [$permission],
            ...$params,
        ]);
    }

    /**
     * Defines gated for admin authorization
     */
    public static function adminGateCrasher(): void
    {
        $permissions = Permission::cases();
        $canReadOnly = static function (User $admin) {
            if (
                str(request()->url())->contains('/forms') &&
                (request()->isMethod('GET') || request()->isMethod('OPTIONS')) &&
                $admin->checkPermissionTo('form.readonly')
            ) {
                return true;
            }
        };

        foreach ($permissions as $permission) {
            Gate::define($permission->value, function (
                ?User $admin,
                Model $model = null
            ) use ($permission, $canReadOnly) {
                if (! dbconfig('enable_admin_permission_middleware', true) || (app()->runningInConsole() && ! app()->isProduction())) {
                    return Response::allow();
                }

                return $admin && ($admin->hasAnyPermission($permission->value) || $canReadOnly($admin))
                    ? Response::allow()
                    : Response::deny('Access denied. Insufficient permissions.', HttpStatus::FORBIDDEN->value);
            });
        }

        Gate::define('any-permission', function (
            ?User $admin,
            array $permissions = []
        ) use ($canReadOnly) {
            /** @var \App\Enums\Permission[] $permissions */
            if (! dbconfig('enable_admin_permission_middleware') || (app()->runningInConsole() && ! app()->isProduction())) {
                return Response::allow();
            }

            return $admin && (
                $admin->hasAnyPermission(collect($permissions)->map(fn($e) => $e->value)->toArray()) ||
                $canReadOnly($admin))
                ? Response::allow()
                : Response::deny('Access denied. Insufficient permissions.', HttpStatus::FORBIDDEN->value);
        });

        if (! str(request()->url())->contains('api/v1')) {
            Gate::define('usable', function (User $admin, $permission) use ($canReadOnly) {
                $permissions = is_array($permission) ? $permission : [$permission];

                $pname = str(collect($permissions)->join(', '))->replace('.', ' ')->headline()->lower();

                return $admin->hasAllPermissions($permission) || $canReadOnly($admin)
                    ? Response::allow()
                    : Response::deny(__('You do not have the ":0" permission.', [$pname]));
            });

            Gate::define('can-do', function (User $admin, $permission, $item = null) use ($canReadOnly) {
                $permissions = is_array($permission) ? $permission : [$permission];

                $pname = str(collect($permissions)->join(', '))->replace('.', ' ')->headline()->lower();

                return $admin->hasAllPermissions($permission) || $canReadOnly($admin)
                    ? Response::allow()
                    : Response::deny(__('You do not have the ":0" permission.', [$pname]));
            });
        }

        Gate::before(function (User $admin) use ($canReadOnly) {
            if ($admin->hasRole(config('permission-defs.super-admin-role')) || $canReadOnly($admin)) {
                return true;
            }
        });
    }
}
