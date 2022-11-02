<?php

use App\Http\Controllers\v1\PageController;
use Illuminate\Support\Facades\Route;

Route::name('home.')->controller(PageController::class)->group(function () {
    Route::get('/get/settings', 'settings')->name('settings');

    Route::prefix('home')->group(function () {
        Route::get('/', 'index')->name('list');
        Route::get('index', 'page')->name('index');
        Route::get('/{id}', 'page')->name('page');
    });
});