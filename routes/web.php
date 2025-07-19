<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\VetBotConseilController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VetBotController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
});

Route::get('menu',[HomeController::class, 'menu'])->name('menu');
Route::get('diagnostic',[HomeController::class, 'diagnostic'])->name('diagnostic');
Route::get('conseil',[HomeController::class, 'conseil'])->name('conseil');


/******************************DIAGNOSTIC *******************************/
Route::get('/vetbot/diagnostic', [VetBotController::class, 'showInitForm'])->name('vetbot.init');
Route::post('/vetbot/diagnostic/start', [VetBotController::class, 'startConversation'])->name('vetbot.start');
Route::get('/vetbot/diagnostic/{conversation}', [VetBotController::class, 'getConversation'])->name('vetbot.chat');
Route::post('/vetbot/diagnostic/{conversation}/send', [VetBotController::class, 'sendMessage'])->name('vetbot.send');



/******************************Conseils*******************************/
Route::get('/vetbot/conseil', [VetBotConseilController::class, 'showInitForm'])->name('vetbotconseil.init');
Route::post('/vetbot/conseil/start', [VetBotConseilController::class, 'startConversation'])->name('vetbotconseil.start');
Route::get('/vetbot/conseil/{conversation}', [VetBotConseilController::class, 'chatInterface'])->name('vetbotconseil.chat');
Route::post('/vetbot/conseil/{conversation}/send', [VetBotConseilController::class, 'sendMessage'])->name('vetbotconseil.send');
