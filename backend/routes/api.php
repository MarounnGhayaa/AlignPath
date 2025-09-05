<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\Users\UserPreferenceController;
use App\Http\Controllers\AiAgentController;

Route::group(["prefix" => "v0.1"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::group(["prefix" => "user"], function () {
            Route::get('/getInfo/{id}', [ProfileController::class, 'getUserInfo']);
            Route::put('/updateInfo/{id}', [ProfileController::class, 'updateUserInfo']);

            Route::post('/preferences', [UserPreferenceController::class, 'storeUserPreferences']);

            Route::post('/chat', [GeminiController::class, 'chat']);

            Route::prefix('ai')->group(function () {
                Route::post('/accept-path', [AiAgentController::class, 'acceptPath']);
                Route::post('/dismiss-path', [AiAgentController::class, 'dismissPath']);
                Route::post('/recommend-careers', [AiAgentController::class, 'recommendCareers']);
                Route::post('/generate-quests', [AiAgentController::class, 'generateQuests']);
            });
        });
    });

    Route::group(["prefix" => "guest"], function () {
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/register", [AuthController::class, "register"]);
    });
});
