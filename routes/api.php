<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
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

Route::get('/', function () {
    return "API ok";
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['jwt.verify'])->group(function () {
    Route::get('me', [AuthController::class, 'profile']);
    Route::get('trip', [TripController::class, 'index']);
    Route::post('trip', [TripController::class, 'store']);
    Route::put('trip/{id}',  [TripController::class, 'update']);
    Route::delete('trip/{id}',  [TripController::class, 'destroy']);
});
