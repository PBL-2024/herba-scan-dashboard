<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    Route::controller(AuthController::class)->group(function(){
        //  Google
        Route::get('google/redirect', 'googleRedirect');
        Route::get('google/callback','googleCallback');
    });
});