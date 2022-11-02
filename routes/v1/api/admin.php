<?php

use App\Http\Controllers\v1\Admin\AdminController;
use App\Http\Controllers\v1\Admin\ConfigurationController;
use App\Http\Controllers\v1\PageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::get('stats', [AdminController::class, 'loadStats']);
    Route::prefix('pages')->group(function () {
        Route::apiResource('homepage', PageController::class);
    });

    Route::post('configuration', [ConfigurationController::class, 'saveSettings']);
});