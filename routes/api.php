<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

/******************************DIAGNOSTIC *******************************/
Route::post('/vetbot/diagnostic/start', [VetBotController::class, 'startConversation']);
Route::get('/vetbot/diagnostic/{conversation}', [VetBotController::class, 'getConversation']);
Route::post('/vetbot/diagnostic/{conversation}/send', [VetBotController::class, 'sendMessage']);



/******************************Conseils*******************************/
Route::post('/vetbot/conseil/start', [VetBotConseilController::class, 'startConversation']);
Route::get('/vetbot/conseil/{conversation}', [VetBotConseilController::class, 'getConversation']);
Route::post('/vetbot/conseil/{conversation}/send', [VetBotConseilController::class, 'sendMessage']);
