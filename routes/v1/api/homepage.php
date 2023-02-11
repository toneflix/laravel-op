<?php

use App\Http\Controllers\v1\Advert;
use App\Http\Controllers\v1\HomeController;
use Illuminate\Support\Facades\Route;

Route::name('home.')->controller(HomeController::class)->group(function () {
    Route::get('/get/settings', 'settings')->name('settings');
    Route::get('/get/navigations', 'navigations')->name('navigations');

    Route::prefix('home')->group(function () {
        Route::get('/', 'index')->name('list');
        Route::get('index', 'page')->name('index');
        Route::get('/{id}', 'page')->name('page');
    });

    Route::get('/content/placement', [Advert::class, 'index'])->middleware(['auth:sanctum'])->name('ad.placement');
    Route::get('/content/placement/guest', [Advert::class, 'index'])->name('ad.placement');
});