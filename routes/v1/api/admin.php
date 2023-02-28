<?php

use App\Http\Controllers\v1\Admin\AdvertController;
use App\Http\Controllers\v1\Admin\ConfigurationController;
use App\Http\Controllers\v1\Admin\Home\HomepageContentController;
use App\Http\Controllers\v1\Admin\Home\HomepageController;
use App\Http\Controllers\v1\Admin\Home\HomepageServicesController;
use App\Http\Controllers\v1\Admin\Home\HomepageSlidesController;
use App\Http\Controllers\v1\Admin\Home\NavigationController;
use App\Http\Controllers\v1\Admin\PlanController;
use App\Http\Controllers\v1\Admin\SubscriptionController;
use App\Http\Controllers\v1\Admin\SystemController;
use App\Http\Controllers\v1\Admin\TransactionController;
use App\Http\Controllers\v1\Admin\UsersController;
use App\Http\Controllers\v1\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('stats', [SystemController::class, 'loadStats']);
    Route::get('stats/{type}', [SystemController::class, 'loadChartPlus']);
    Route::post('tests/{type}', [SystemController::class, 'testService']);

    Route::prefix('website')->group(function () {
        Route::put('homepage/{homepage}/reorder', [HomepageController::class, 'reorder'])->name('reorder');
        Route::apiResource('homepage', HomepageController::class);
        Route::apiResource('{homepage}/content', HomepageContentController::class);
        Route::apiResource('{homepage}/slides', HomepageSlidesController::class);
        Route::apiResource('services', HomepageServicesController::class);
        Route::put('navigations/{navigation}/reorder', [NavigationController::class, 'reorder'])->name('reorder');
        Route::apiResource('navigations', NavigationController::class);
    });

    Route::apiResource('advertisements', AdvertController::class);
    Route::apiResource('subscriptions', SubscriptionController::class);

    Route::name('transactions.')->group(function () {
        Route::get('/transactions/{reference}/invoice', [TransactionController::class, 'invoice'])->name('invoice');
        Route::apiResource('/transactions', TransactionController::class);
    });

    Route::name('wallets.')->prefix('wallets')->controller(WalletController::class)->group(function () {
        Route::get('/withdrawals', 'withdrawals')->name('withdrawals');
        Route::post('/withdrawals/{wallet}/status', 'setStatus')->name('set.status');
    });

    Route::name('users.')->controller(UsersController::class)->group(function () {
        Route::apiResource('users', UsersController::class)->except(['store', 'update']);
        Route::patch('users/action/{action?}', [UsersController::class, 'action'])->name('action');
    });

    Route::post('configuration', [ConfigurationController::class, 'saveSettings']);

    Route::apiResource('plans', PlanController::class);
});
