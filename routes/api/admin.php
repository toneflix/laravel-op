<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\NotificationTemplateController;
use Illuminate\Support\Facades\Route;

$permissionMiddlewares = 'role:' . join("|", config('permission-defs.roles', []));

Route::middleware(['auth:sanctum', $permissionMiddlewares])->prefix('admin')->group(function () {
    Route::apiResource('users', UserController::class);

    Route::apiResource('configurations', ConfigurationController::class)->only(['index', 'show', 'store']);

    // Notifications Templates
    Route::apiResource('configurations/notifications/templates', NotificationTemplateController::class)
        ->except(['store', 'destroy']);
});