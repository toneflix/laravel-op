<?php

use App\Http\Controllers\v1\SearchController;
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

Route::get('/search', [SearchController::class, 'index'])->name('search');

Route::get('/playground', function () {
})->name('playground');