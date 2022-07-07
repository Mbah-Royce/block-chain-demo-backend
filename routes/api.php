<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\LandCertificateController;
use App\Http\Controllers\PartionTitleTransactionController;
use App\Http\Controllers\PartitionsController;
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
Route::GET("/blocks",[BlockController::class,'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::GET("/user/info",[UserController::class,'index']);
    Route::GET("/certificates",[LandCertificateController::class,'getCertificatesFeature']);

    
});

Route::group(['middleware'=>['auth:sanctum'],'prefix'=>'user'],function(){
    Route::GET("/lands",[UserController::class,'getLand']); //display geojson of all user land
    Route::GET("/partitions",[UserController::class,'getPartitions']); //display geojson of all user partitions
    Route::GET("/partitions/info",[UserController::class,'getPortions']); //display data for a partition 
    Route::GET("/lands/info",[UserController::class,'getCertificates']); //display data for all land 
    Route::GET("/trans-stats",[UserController::class,'getTransStarts']);
    Route::GET("/user-trans",[UserController::class,'getLastTrans']);
    Route::POST("/transaction",[TransactionController::class,'create']);

});

Route::group(['middleware'=>['auth:sanctum'],'prefix'=>'transaction'],function(){
    Route::GET("/partition/{id}",[PartitionsController::class,'show']); //display geojson for a paricular partition
    Route::GET("/land/{id}",[LandCertificateController::class,'showLand']); //display geojson for a paricular land

    Route::POST("/certificate",[LandCertificateController::class,'create']); //create land certificate
    Route::POST("/partition-title",[PartionTitleTransactionController::class,'create']); 

});

Route::get('user/test',[BlockController::class,'fireEvents']);