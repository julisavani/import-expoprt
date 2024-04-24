<?php

use App\Http\Controllers\Admin\CartController;
use App\Http\Controllers\Admin\ClarityController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ConfirmController;
use App\Http\Controllers\Admin\FancyColorController;
use App\Http\Controllers\Admin\FinishController;
use App\Http\Controllers\Admin\FluorescenceController;
use App\Http\Controllers\Admin\HoldController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\MarketingController;
use App\Http\Controllers\Admin\MerchantController;
use App\Http\Controllers\Admin\PolicyController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ShapeController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\StarMeleeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
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

Route::get('/', [LoginController::class, 'login ']);
// Route::post('register', [LoginController::class, 'register']);
Route::post('login', [LoginController::class, 'login']);
Route::get('release', [HoldController::class, 'holdrelease']);

Route::group(['middleware' => ['adminAuth']], function (){
    Route::get("profile", [LoginController::class, "profile"]);
    Route::post("logout", [LoginController::class, "logout"]);
    Route::post("profile", [LoginController::class, "updateprofile"]);
    Route::post("change-password", [LoginController::class, "change-password"]);
    Route::get('country', [UserController::class, 'country']);

    Route::post('forgotpassword', [LoginController::class, 'forgotpassword']);
    Route::post('verifyotp', [LoginController::class, 'verifyotp']);
    Route::post('updatepassword', [LoginController::class, 'updatepassword']);
    Route::get('/dashboard', [SettingController::class, 'dashboard']);
    Route::group(['prefix' => 'clarity'], function (){
        Route::get('/', [ClarityController::class, 'index']);
        Route::post('/create', [ClarityController::class, 'store']);
        Route::post('/update/{id}', [ClarityController::class, 'update']);
        Route::post('/status/{id}', [ClarityController::class, 'status']);
        Route::delete('/delete/{id}', [ClarityController::class, 'delete']);
    });

    Route::group(['prefix' => 'color'], function (){
        Route::get('/', [ColorController::class, 'index']);
        Route::post('/create', [ColorController::class, 'store']);
        Route::post('/update/{id}', [ColorController::class, 'update']);
        Route::post('/status/{id}', [ColorController::class, 'status']);
        Route::delete('/delete/{id}', [ColorController::class, 'delete']);
    });

    Route::group(['prefix' => 'size'], function (){
        Route::get('/', [SizeController::class, 'index']);
        Route::post('/create', [SizeController::class, 'store']);
        Route::post('/update/{id}', [SizeController::class, 'update']);
        Route::post('/status/{id}', [SizeController::class, 'status']);
        Route::delete('/delete/{id}', [SizeController::class, 'delete']);
    });

    Route::group(['prefix' => 'shape'], function (){
        Route::get('/', [ShapeController::class, 'index']);
        Route::post('/create', [ShapeController::class, 'store']);
        Route::post('/update/{id}', [ShapeController::class, 'update']);
        Route::post('/status/{id}', [ShapeController::class, 'status']);
        Route::delete('/delete/{id}', [ShapeController::class, 'delete']);
    });

    Route::group(['prefix' => 'fancy-color'], function (){
        Route::get('/', [FancyColorController::class, 'index']);
        Route::post('/create', [FancyColorController::class, 'store']);
        Route::post('/update/{id}', [FancyColorController::class, 'update']);
        Route::post('/status/{id}', [FancyColorController::class, 'status']);
        Route::delete('/delete/{id}', [FancyColorController::class, 'delete']);
    });

    Route::group(['prefix' => 'user'], function (){
        Route::get('/', [UserController::class, 'index']);
        Route::post('/create', [UserController::class, 'store']);
        Route::post('/update/{id}', [UserController::class, 'update']);
        Route::post('/status/{id}', [UserController::class, 'status']);
        Route::delete('/delete/{id}', [UserController::class, 'delete']);
        Route::delete('/password/{id}', [UserController::class, 'password']);
    });

    Route::group(['prefix' => 'finish'], function (){
        Route::get('/', [FinishController::class, 'index']);
        Route::post('/create', [FinishController::class, 'store']);
        Route::post('/update/{id}', [FinishController::class, 'update']);
        Route::post('/status/{id}', [FinishController::class, 'status']);
        Route::delete('/delete/{id}', [FinishController::class, 'delete']);
    });

    Route::group(['prefix' => 'fluorescence'], function (){
        Route::get('/', [FluorescenceController::class, 'index']);
        Route::post('/create', [FluorescenceController::class, 'store']);
        Route::post('/update/{id}', [FluorescenceController::class, 'update']);
        Route::post('/status/{id}', [FluorescenceController::class, 'status']);
        Route::delete('/delete/{id}', [FluorescenceController::class, 'delete']);
    });

    Route::group(['prefix' => 'product'], function (){
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/master', [ProductController::class, 'master']);
        Route::post('/create', [ProductController::class, 'store']);
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::post('/status/{id}', [ProductController::class, 'status']);
        Route::delete('/delete/{id}', [ProductController::class, 'delete']);
        Route::post('/import',[ProductController::class,'import']);
        // Route::post('/import/{uuid}',[ProductController::class,'importstore']);
        Route::post('/import/{uuid}',[ProductController::class,'updateImportData']);
        Route::post('/invalid',[ProductController::class,'invaliddiamond']);
        Route::get('/history',[ProductController::class,'history']);
        Route::get('/history/{id}',[ProductController::class,'exceldownload']);
        Route::get('/reset/{type}',[ProductController::class,'resetdiamond']);
    });

    Route::group(['prefix' => 'policy'], function (){
        Route::get('/', [PolicyController::class, 'index']);
        Route::post('/create', [PolicyController::class, 'store']);
        Route::post('/status/{id}', [PolicyController::class, 'status']);
        Route::delete('/delete/{id}', [PolicyController::class, 'delete']);
    });

    Route::group(['prefix' => 'marketing'], function (){
        Route::get('/', [MarketingController::class, 'index']);
        Route::post('/create', [MarketingController::class, 'store']);
        Route::post('/status/{id}', [MarketingController::class, 'status']);
        Route::delete('/delete/{id}', [MarketingController::class, 'delete']);
    });

    // Route::get('/request', [RequestController::class, 'RequestList']);
    Route::group(['prefix' => 'slot'], function (){
        Route::get('/', [RequestController::class, 'index']);
        Route::post('/create', [RequestController::class, 'store']);
        Route::post('/update/{id}', [RequestController::class, 'update']);
        Route::post('/status/{id}', [RequestController::class, 'status']);
        Route::delete('/delete/{id}', [RequestController::class, 'delete']);
    });

    Route::group(['prefix' => 'request'], function (){
        Route::get('/', [RequestController::class, 'RequestList']);
        Route::post('/status/{id}', [RequestController::class, 'Requeststatus']);
        Route::post('/delete', [RequestController::class, 'Requestdelete']);
    });
    Route::group(['prefix' => 'hold'], function (){
        Route::get('/', [HoldController::class, 'index']);
        Route::get('/user/{id}', [HoldController::class, 'user']);
        Route::post('/status/{id}', [HoldController::class, 'status']);
        Route::post('/delete', [HoldController::class, 'delete']);
    });
    Route::group(['prefix' => 'confirm'], function (){
        Route::get('/', [ConfirmController::class, 'index']);
        Route::get('/user/{id}', [ConfirmController::class, 'user']);
        Route::post('/status/{id}', [ConfirmController::class, 'status']);
        Route::post('/delete', [ConfirmController::class, 'delete']);
        Route::post('/release', [ConfirmController::class, 'release']);
    });
    Route::group(['prefix' => 'cart'], function (){
        Route::get('/', [CartController::class, 'index']);
        Route::get('/user/{id}', [CartController::class, 'user']);
        Route::post('/status/{id}', [CartController::class, 'status']);
        Route::post('/delete', [CartController::class, 'delete']);
    });
    Route::group(['prefix' => 'inquiry'], function (){
        Route::get('/', [InquiryController::class, 'index']);
        Route::get('/user/{id}', [InquiryController::class, 'user']);
        Route::post('/status/{id}', [InquiryController::class, 'status']);
        Route::post('/delete', [InquiryController::class, 'delete']);
    });
    Route::group(['prefix' => 'appointment'], function (){
        Route::get('/', [InquiryController::class, 'appointmentindex']);
        Route::post('/status/{id}', [InquiryController::class, 'appointmentstatus']);
        Route::delete('/delete/{id}', [InquiryController::class, 'appointmentdelete']);
    });
    Route::group(['prefix' => 'demand'], function (){
        Route::get('/', [InquiryController::class, 'demandindex']);
        Route::post('/status/{id}', [InquiryController::class, 'demandstatus']);
        Route::delete('/delete/{id}', [InquiryController::class, 'demanddelete']);
    });

    Route::group(['prefix' => 'starmelee'], function (){
        Route::get('/', [StarMeleeController::class, 'index']);
        Route::post('/create', [StarMeleeController::class, 'store']);
        Route::post('/update/{id}', [StarMeleeController::class, 'update']);
        Route::post('/status/{id}', [StarMeleeController::class, 'status']);
        Route::delete('/delete/{id}', [StarMeleeController::class, 'delete']);
        Route::post('/import',[StarMeleeController::class,'import']);
        Route::get('/inquiry',[StarMeleeController::class,'inquiry']);
        // Route::post('/import/{uuid}',[StarMeleeController::class,'importstore']);
    });

    Route::group(['prefix' => 'vendor'], function (){
        Route::get('/', [VendorController::class, 'index']);
        Route::post('/create', [VendorController::class, 'store']);
        Route::post('/update/{id}', [VendorController::class, 'update']);
        Route::post('/status/{id}', [VendorController::class, 'status']);
        Route::delete('/delete/{id}', [VendorController::class, 'delete']);
        // Route::delete('/password/{id}', [VendorController::class, 'password']);
    });
    Route::group(['prefix' => 'merchant'], function (){
        Route::get('/', [MerchantController::class, 'index']);
        Route::post('/create', [MerchantController::class, 'store']);
        Route::post('/update/{id}', [MerchantController::class, 'update']);
        Route::post('/status/{id}', [MerchantController::class, 'status']);
        Route::delete('/delete/{id}', [MerchantController::class, 'delete']);
        // Route::delete('/password/{id}', [MerchantController::class, 'password']);
    });

});
Route::group(['prefix' => 'export'], function (){
    Route::get('/starmelee', [StarMeleeController::class, 'export']);
});
