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
    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('logout', 'logout')->middleware('auth:sanctum');
            Route::post('login', 'login');
            Route::post('register', 'register');
            Route::post('google/callback', 'googleCallback');
            Route::post('otp/send', 'sendOTP');
            Route::post('otp/verify', 'verifyOTP');
            Route::post('change-password', 'changePassword');
        });
    });

    // Authenticated
    Route::middleware(['auth:sanctum'])->group(function () {
        // User
        Route::get('user', [UserController::class, 'user']);
        Route::put('user', [UserController::class, 'update']);
        Route::post('user/avatar', [UserController::class, 'update_avatar']);
        Route::get('user/favorites', [UserController::class, 'favorites']);

        // Tanaman
        Route::group(['prefix' => 'plant'], function () {
            Route::post('favorite', [TanamanController::class, 'setFavorite']);
            Route::post('is-favorite', [TanamanController::class, 'isFavorite']);
            Route::get('{id}', [TanamanController::class, 'show']);
        });

        // Tanaman belum terdaftar
        Route::get('unclassified-plants', [TanamanController::class, 'myUnclassifiedPlants']);
        Route::group(['prefix' => 'unclassified-plant'], function () {
            Route::post('/', [TanamanController::class, 'sendUnclassifiedPlant']);
            Route::delete('{id}', [TanamanController::class, 'deleteUnclassifiedPlant']);
        });

        // Article
        Route::group(['prefix' => 'article'], function () {
            Route::post('favorite', [ArticleController::class, 'setFavorite']);
            Route::post('is-favorite', [ArticleController::class, 'isFavorite']);
            Route::get('{id}', [ArticleController::class, 'show']);

            // Comment
            Route::group(['prefix' => 'comment'], function () {
                Route::post('/', [CommentController::class, 'comment']);
                Route::get('{article_id}', [CommentController::class, 'getComments']);
                Route::delete('{article_id}', [CommentController::class, 'deleteComment']);
            });
        });
    });

    // Public
    Route::get('plants', [TanamanController::class, 'index']);
    Route::get('articles', [ArticleController::class, 'index']);
    Route::get('faq', [FaqController::class, 'index']);

});