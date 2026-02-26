<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GameSessionController;
use App\Http\Controllers\Api\NetworkController;
use App\Http\Controllers\Api\PlayerPositionController;
use App\Http\Controllers\Api\ProgressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Unity / dekan.pro)
|--------------------------------------------------------------------------
*/

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/connect', [AuthController::class, 'connect']);
Route::get('/network/my-ip', [NetworkController::class, 'myIp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/progress', [ProgressController::class, 'show']);
    Route::put('/progress', [ProgressController::class, 'update']);
    Route::get('/player/position', [PlayerPositionController::class, 'show']);
    Route::put('/player/position', [PlayerPositionController::class, 'update']);
    Route::post('/game/join', [GameSessionController::class, 'join']);
    Route::post('/game/leave', [GameSessionController::class, 'leave']);
    Route::get('/game/players', [GameSessionController::class, 'players']);
});
