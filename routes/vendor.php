<?php

use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\LoginController;
use App\Http\Controllers\Vendor\ProductController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [LoginController::class, 'index']);
Route::post('register', [LoginController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::get('country', [LoginController::class, 'country']);
Route::get('marketing', [DashboardController::class, 'marketing']);
Route::post('forgotpassword', [LoginController::class, 'forgotpassword']);
Route::post('verifyotp', [LoginController::class, 'verifyotp']);
Route::post('updatepassword', [LoginController::class, 'updatepassword']);
Route::post('activation/{id}', [LoginController::class, 'activation']);

Route::group(['middleware' => ['vendorAuth']], function (){
    Route::get("profile", [LoginController::class, "profile"]);
    Route::post("logout", [LoginController::class, "logout"]);
    Route::post("profile", [LoginController::class, "updateprofile"]);
    Route::post("change-password", [LoginController::class, "changepassword"]);
    Route::get("dashboard", [DashboardController::class, "index"]);
    Route::get("history", [DashboardController::class, "history"]);
    Route::get('/history/{id}',[DashboardController::class,'exceldownload']);
    Route::get("invalid", [DashboardController::class, "invaliddiamond"]);
    Route::get("hold", [DashboardController::class, "hold"]);
    Route::get("confirm", [DashboardController::class, "confirm"]);
    // Route::get("dashboard", [DashboardController::class, "index"]);

    // Route::post('/import',[DashboardController::class,'import']);
    // Route::post('/import/{uuid}',[DashboardController::class,'updateImportData']);
    Route::group(['prefix' => 'product'], function (){
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/master', [ProductController::class, 'master']);
        Route::post('/create', [ProductController::class, 'store']);
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::post('/status/{id}', [ProductController::class, 'status']);
        Route::delete('/delete/{id}', [ProductController::class, 'delete']);
        Route::post('/import',[ProductController::class,'import']);
        Route::post('/import/{uuid}',[ProductController::class,'updateImportData']);
        Route::get('/reset/{type}',[ProductController::class,'resetdiamond']);

    });


});

