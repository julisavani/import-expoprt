<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\DemandController;
use App\Http\Controllers\Api\DiamondController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\StarMeleeController;
use App\Http\Controllers\Merchant\DashboardController as MerchantDashboardController;
use Illuminate\Http\Request;
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

Route::group(['prefix' => 'starmelee'], function (){
    Route::get("/shape", [StarMeleeController::class, "shape"]);
    // Route::post("/size", [StarMeleeController::class, "size"]);
    // Route::post("/color", [StarMeleeController::class, "color"]);
    Route::post("/carat", [StarMeleeController::class, "carat"]);
    Route::post("/price", [StarMeleeController::class, "price"]);
    Route::post("/inquiry", [StarMeleeController::class, "inquiry"]);
});

Route::group(['middleware' => ['apiAuth']], function (){

    Route::get("/starmelee/inquiry", [StarMeleeController::class, "inquirylist"]);
    Route::get("profile", [LoginController::class, "profile"]);
    Route::post("logout", [LoginController::class, "logout"]);
    Route::post("profile", [LoginController::class, "updateprofile"]);
    Route::post("change-password", [LoginController::class, "changepassword"]);
    Route::post("terms", [LoginController::class, "terms"]);
    Route::get("policy", [LoginController::class, "Policy"]);
    Route::post("changediamond", [LoginController::class, "changediamond"]);

    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/pair', [DashboardController::class, 'pair']);
    Route::get('/master', [SearchController::class, 'master']);
    Route::get('/newarrival', [SearchController::class, 'newarrival']);

    Route::post('mainsearch', [SearchController::class, 'mainsearch']);
    Route::group(['prefix' => 'search'], function (){
        Route::post('/', [SearchController::class, 'search']);
        Route::post('/store', [SearchController::class, 'savesearch']);
        Route::get('/list', [SearchController::class, 'list']);
        Route::post('delete', [SearchController::class, 'delete']);
    });
    Route::get('details/{id}', [DiamondController::class, 'details']);

    Route::group(['prefix' => 'demand'], function (){
        Route::get('/', [DemandController::class, 'index']);
        Route::post('/store', [DemandController::class, 'store']);
        Route::post('/delete', [DemandController::class, 'delete']);
    });
    Route::get('slot', [DiamondController::class, 'slot']);
    Route::group(['prefix' => 'request'], function (){
        Route::get('/', [DiamondController::class, 'RequestList']);
        Route::post('/store', [DiamondController::class, 'RequestStore']);
        Route::post('/delete', [DiamondController::class, 'RequestDelete']);
    });

    Route::group(['prefix' => 'cart'], function (){
        Route::get('/', [DiamondController::class, 'CartList']);
        Route::post('/store', [DiamondController::class, 'CartStore']);
        Route::post('/delete', [DiamondController::class, 'CartDelete']);
    });

    Route::group(['prefix' => 'hold'], function (){
        Route::get('/', [DiamondController::class, 'HoldList']);
        Route::post('/store', [DiamondController::class, 'HoldStore']);
        Route::post('/delete', [DiamondController::class, 'HoldDelete']);
    });

    Route::group(['prefix' => 'confirm'], function (){
        Route::get('/', [DiamondController::class, 'ConfirmList']);
        Route::post('/store', [DiamondController::class, 'ConfirmStore']);
        Route::post('/delete', [DiamondController::class, 'ConfirmDelete']);
    });

    Route::group(['prefix' => 'inquiry'], function (){
        Route::get('/', [DiamondController::class, 'InquiryList']);
        Route::post('/store', [DiamondController::class, 'InquiryStore']);
        Route::post('/delete', [DiamondController::class, 'InquiryDelete']);
    });
    Route::group(['prefix' => 'appointment'], function (){
        Route::get('/', [AppointmentController::class, 'index']);
        Route::post('/store', [AppointmentController::class, 'store']);
        Route::post('/delete', [AppointmentController::class, 'delete']);
    });

});
Route::group(['prefix' => 'export'], function (){
    Route::post('/excel', [DiamondController::class, 'exportExcel']);
    Route::post('/pdf', [DiamondController::class, 'exportPdf']);
    Route::post('/email', [DiamondController::class, 'emailExcel']);
});


Route::group(['prefix' => 'merchant'], function (){
    Route::post("/register", [MerchantDashboardController::class, "register"]);
    Route::post("/activation/{id}", [MerchantDashboardController::class, "activation"]);
    Route::get("/country", [MerchantDashboardController::class, "country"]);
    Route::group(['middleware' => ['checkHeader']], function (){
        Route::get("/dashboard", [MerchantDashboardController::class, "dashboard"]);
        Route::get("/list", [MerchantDashboardController::class, "list"]);
        Route::group(['prefix' => 'hold'], function (){
            Route::get('/', [MerchantDashboardController::class, 'HoldList']);
            Route::post('/store', [MerchantDashboardController::class, 'HoldStore']);
            Route::post('/delete', [MerchantDashboardController::class, 'HoldDelete']);
        });

        Route::group(['prefix' => 'confirm'], function (){
            Route::get('/', [MerchantDashboardController::class, 'ConfirmList']);
            Route::post('/store', [MerchantDashboardController::class, 'ConfirmStore']);
        });
    });

});
