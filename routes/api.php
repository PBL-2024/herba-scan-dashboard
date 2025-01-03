<?php

use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\FaqController;
use App\Http\Controllers\API\TanamanController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('v1')->group(function () {
    // Article
    Route::group(['prefix' => 'article'], function () {
        Route::get('{id}', [ArticleController::class, 'show']);
        Route::get('/search/{keyword}', [ArticleController::class, 'search']);
    });

    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout')->middleware('auth:sanctum');
            Route::post('login', 'login');
            Route::post('register', 'register');
            Route::post('google/callback', 'googleCallback');
            Route::post('otp/send-authenticated-user', 'sendOTPAuthenticatedUser')->middleware('auth:sanctum');
            Route::post('otp/send', 'sendOTP');
            Route::post('otp/signup/send', 'sendOTPSignUp');
            Route::post('otp/verify', 'verifyOTP');
            Route::post('change-password', 'changePassword');
        });
    });

    // Tanaman
    Route::group(['prefix' => 'plant'], function () {
        Route::get('{id}', [TanamanController::class, 'show']);
        Route::get('/name/{name}', [TanamanController::class, 'getByName']);
        Route::get('/search/{keyword}', [TanamanController::class, 'search']);
    });



    // Authenticated
    Route::middleware(['auth:sanctum'])->group(function () {
        // User
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [UserController::class, 'user']);
            Route::put('/', [UserController::class, 'update']);
            Route::post('avatar', [UserController::class, 'update_avatar']);
            Route::get('favorites', [UserController::class, 'favorites']);
            Route::put('change-password', [UserController::class, 'changePassword']);
            Route::post('otp/send', [UserController::class, 'sendOTP']);
        });


        // Tanaman
        Route::group(['prefix' => 'plant'], function () {
            Route::post('favorite', [TanamanController::class, 'setFavorite']);
            Route::post('is-favorite', [TanamanController::class, 'isFavorite']);
        });

        // Tanaman belum terdaftar
        Route::get('unclassified-plants', [TanamanController::class, 'myUnclassifiedPlants']);
        Route::group(['prefix' => 'unclassified-plant'], function () {
            Route::post('/', [TanamanController::class, 'sendUnclassifiedPlant']);
            Route::get('/list', [TanamanController::class, 'getListNameUnclassifiedPlant']);
            Route::delete('{id}', [TanamanController::class, 'deleteUnclassifiedPlant']);
        });

        // Article
        Route::group(['prefix' => 'article'], function () {
            Route::post('favorite', [ArticleController::class, 'setFavorite']);
            Route::post('is-favorite', [ArticleController::class, 'isFavorite']);

            // Comment
            Route::group(['prefix' => 'comment'], function () {
                Route::post('/', [CommentController::class, 'comment']);
                Route::get('{article_id}', [CommentController::class, 'getComments']);
                Route::delete('{article_id}/{comment_id}', [CommentController::class, 'deleteComment']);
            });
        });
    });

    // Public
    Route::get('plants', [TanamanController::class, 'index']);
    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('faq', [FaqController::class, 'index']);

});