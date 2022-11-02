<?php

use App\Http\Controllers\WebUser;
use App\Services\AppInfo;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use ToneflixCode\LaravelFileable\Media;

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

Route::get('/', function () {
    return view('login', ['api_welcome' => [
        'Welcome to GreyFlix v1' => AppInfo::basic(),
    ],
    ]);
});

Route::prefix('console')->name('console.')->group(function () {
    Route::get('/login', [WebUser::class, 'login'])
        ->middleware('guest')
        ->name('login');

    Route::post('/login', [WebUser::class, 'store'])
    ->middleware('guest');

    Route::post('/logout', [WebUser::class, 'destroy'])
    ->middleware(['web', 'auth'])
    ->name('logout');

    Route::get('/user', [WebUser::class, 'index'])
    ->middleware(['web', 'auth', 'admin'])
    ->name('user');
});

Route::get('/artisan/backup/action/{action?}', [WebUser::class, 'backup'])->middleware(['web', 'auth', 'admin']);
Route::get('/artisan/{command}/{params?}', [WebUser::class, 'artisan'])->middleware(['web', 'auth', 'admin']);

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
