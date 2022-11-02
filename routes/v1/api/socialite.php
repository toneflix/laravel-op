<?php

use App\Http\Controllers\v1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\v1\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::name('auth.')->prefix('auth')->group(function () {
    Route::get('/google/redirect', function () {
        return Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
    })->name('google.redirect');

    Route::post('/google/register', [RegisteredUserController::class, 'socialCreateAccount'])->middleware('guest');
    Route::post('/google/login', [AuthenticatedSessionController::class, 'socialLogin'])->middleware('guest');

    Route::get('/google/callback', function () {
    })->name('google.callback');
});
