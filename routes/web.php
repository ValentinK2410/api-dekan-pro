<?php
header('Access-Control-Allow-Origin: *');

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/greeting', function () {
    return 'Hello World';
});

//Route::get('/posts', [PostController::class, 'index']);
Route::resource('/posts', 'App\Http\Controllers\PostController');

Route::get('/test', function () {
    return response()->json(['message' => 'api.dekan.pro/test работает!']);
});


