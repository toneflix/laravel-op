<?php

use App\Http\Controllers\v1\SearchController;
use App\Http\Controllers\v1\UsersController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Load Extra Routes
if (file_exists(base_path('routes/v1/api'))) {
    array_filter(File::files(base_path('routes/v1/api')), function ($file) {
        if ($file->getExtension() === 'php') {
            require_once $file->getPathName();
        }
    });
}

// Users
Route::name('user.')->prefix('users')->middleware(['auth:sanctum'])->controller(UsersController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::prefix('{user}')->group(function () {
        Route::get('/', 'show')->name('profile');
        Route::get('reviews', 'reviews')->name('reviews');
        Route::get('relationships/{relationship}', 'relationships')->name('relationships');
        Route::post('reviews', 'writeReview');
        Route::post('do/relationship/{action}', 'doFollow');
    });
});

// Users for guests
Route::name('guest.user.')->prefix('guest/users')->controller(UsersController::class)->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{user}', 'show')->name('profile');
    Route::get('/{user}/reviews', 'reviews')->name('reviews');
});

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/playground', function () {
})->name('playground');