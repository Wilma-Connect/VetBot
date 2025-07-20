<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VetBotController;
use App\Http\Controllers\VetBotConseilController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


/******************************Authentification*******************************/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


/******************************DIAGNOSTIC *******************************/
Route::middleware('auth:sanctum')->prefix('/vetbot/diagnostic')->group(function () {
    Route::post('/start', [VetBotController::class, 'startConversation']);
    Route::get('/user-conversations', [VetBotController::class, 'getUserConversations']);
    Route::get('/{conversation}', [VetBotController::class, 'getConversation']);
    Route::post('/{conversation}/send', [VetBotController::class, 'sendMessage']);
});


/******************************Conseils*******************************/
Route::middleware('auth:sanctum')->prefix('/vetbot/conseil')->group(function () {
    Route::post('/start', [VetBotConseilController::class, 'startConversation']);
    Route::get('/user-conversations', [VetBotConseilController::class, 'getUserConversations']);
    Route::get('/{conversation}', [VetBotConseilController::class, 'getConversation']);
    Route::post('/{conversation}/send', [VetBotConseilController::class, 'sendMessage']);
});
