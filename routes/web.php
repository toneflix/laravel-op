<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use ToneflixCode\LaravelFileable\Media;
use ToneflixCode\LaravelVisualConsole\Controllers\ManagementController;
use ToneflixCode\LaravelVisualConsole\Middleware\RedirectIfAuthenticated;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (request()->isXmlHttpRequest()) {
    Route::get('/', function () {
        return ['api_welcome' => [
            __('Welcome to :0 v1', [env('APP_NAME', 'Laravel OP')]) => AppInfo::basic(),
        ]];
    })->name('index');
} else {
    Route::get('/', [ManagementController::class, 'login'])
        ->middleware(['web', RedirectIfAuthenticated::class])
        ->name('index');
}

Route::get('get/images/{file}', function ($file) {
    return (new Media)->privateFile($file);
})->middleware(['window_auth'])->name('get.image');

Route::get('downloads/secure/{filename?}', function ($filename = '') {
    if (Storage::disk('protected')->exists('backup/'.$filename)) {
        return Storage::disk('protected')->download('backup/'.$filename);
    }

    return abort(404, 'File not found');
})
->middleware(['web', 'auth', 'admin'])
->name('secure.download');