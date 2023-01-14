<?php

use App\Http\Controllers\v1\Admin\ConfigurationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::post('configuration', [ConfigurationController::class, 'saveSettings']);
});