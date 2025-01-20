<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return [
        'api' => config('app.name'),
    ];
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/admin', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

$files = glob(__DIR__.'/api/*.php');
foreach ($files as $file) {
    Route::middleware('api')->group($file);
}
