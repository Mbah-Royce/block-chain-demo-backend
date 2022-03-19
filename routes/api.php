<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::GET("/user/all",[UserController::class,'allUsers']);
Route::POST("user/area/update",[TransactionController::class,'updateArea']);
Route::POST("/user/register",[RegisterController::class,'register']);
Route::POST("/user/login",[LoginController::class,'login']);
Route::POST("/user/transaction",[TransactionController::class,'create']);
Route::GET("/blocks",[BlockController::class,'index']);
Route::middleware(['auth:sanctum'])->group(function () {
    Route::GET("/user/info",[UserController::class,'index']);
});