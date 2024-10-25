<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TanamanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    Route::get('user', function (Request $request) {
        return $request->user();
    })->middleware(['auth:sanctum']);

    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('login', 'login');
            Route::post('register', 'register');
            Route::post('google/callback', 'googleCallback');
            Route::post('otp/send', 'sendOTP');
            Route::post('otp/verify','verifyOTP');
            Route::post('change-password','changePassword');
        });
    });

    Route::resource('tanaman-togas', TanamanController::class)->middleware('auth:sanctum');

});