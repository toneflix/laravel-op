<?php

use App\Http\Controllers\v1\Account;
use App\Http\Controllers\v1\NotificationController;
use App\Http\Controllers\v1\TransactionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::name('account.')->prefix('account')->group(function () {
        Route::get('/', [Account::class, 'index'])->name('index');
        Route::get('/profile/{user}', [Account::class, 'profile'])->name('profile');
        Route::get('/wallet', [Account::class, 'wallet'])->name('wallet');
        Route::post('/wallet/withdrawal', [Account::class, 'withdrawal'])->name('withdrawal');
        Route::put('update', [Account::class, 'update'])->name('update');
        Route::put('update-password', [Account::class, 'updatePassword'])->name('update.password');
        Route::put('update-profile-picture', [Account::class, 'updateProfilePicture'])->name('update.profile.picture');
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

        Route::get('transactions/{status?}', [TransactionController::class, 'index'])->name('index');
        Route::get('transactions/{reference}/invoice', [TransactionController::class, 'invoice'])->name('invoice');
        Route::apiResource('transactions', TransactionController::class)->except('index');
    });
});
