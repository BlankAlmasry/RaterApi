<?php
use App\Http\Controllers\GameController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

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

Route::get('/login', [HomeController::class, 'login'])->name('login');
Route::post('/login', [AccessTokenController::class,'issueToken']);

Route::middleware(['client'])->group(function () {
    Route::get('/games',[GameController::class,'index']);
    Route::post('/games',[GameController::class,'store']);
    Route::get('/games/{game}',[GameController::class,'show']);
    Route::patch('/games/{game}',[GameController::class,'update']);
    Route::delete('/games/{game}',[GameController::class,'delete']);
    Route::get('/games/{game}/users',[GameController::class,'indexUsers']);
    Route::get('/games/{game}/users/{user}',[GameController::class,'showUser']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::delete('/users/{user}', [UserController::class, 'delete']);
    Route::get('/users/{user}/games', [UserController::class, 'indexGames']);
    Route::get('/users/{user}/matches', [UserController::class, 'indexMatches']);

    Route::get('/games/{game}/matches',[MatchController::class,'index']);
    Route::post('/games/{game}/matches',[MatchController::class,'store']);
    Route::get('/games/{game}/matches/{match}',[MatchController::class,'show']);
    Route::delete('/games/{game}/matches/{match}',[MatchController::class,'delete']);
});


