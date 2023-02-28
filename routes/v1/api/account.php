<?php

use App\Http\Controllers\v1\User\GenericRequestController;
use App\Http\Controllers\v1\User\Account;
use App\Http\Controllers\v1\NotificationController;
use App\Http\Controllers\v1\TransactionController;
use App\Http\Controllers\v1\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::name('account.')->prefix('account')->group(function () {
        Route::get('/', [Account::class, 'index'])->name('index');
        Route::get('/wallet', [Account::class, 'wallet'])->name('wallet');
        Route::post('update/profile', [Account::class, 'updateProfile'])->name('update.profile');
        Route::put('update/{field?}', [Account::class, 'update'])->name('update');
        Route::put('update-password', [Account::class, 'updatePassword'])->name('update.password');
        Route::put('update-profile-picture', [Account::class, 'updateProfilePicture'])->name('update.profile.picture');

        Route::get('transactions/{status?}', [TransactionController::class, 'index'])->name('index');
        Route::get('transactions/{reference}/invoice', [TransactionController::class, 'invoice'])->name('invoice');
        Route::apiResource('transactions', TransactionController::class)->except('index');
        Route::apiResource('generic/requests', GenericRequestController::class);

        Route::name('notifications.')
            ->prefix('notifications')
            ->controller(NotificationController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/mark/{id}', 'markAsRead')->name('read');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::name('profile.')
            ->prefix('profile')
            ->controller(ProfileController::class)->group(function () {
            Route::get('relationships/{relationship}', 'relationships')->name('relationships');
        });
    });
});
