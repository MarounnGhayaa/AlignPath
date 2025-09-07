<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\GeminiController;
use App\Http\Controllers\Users\UserPreferenceController;
use App\Http\Controllers\Users\VoiceMessageController;

Route::group(["prefix" =>"v0.1"], function(){
    Route::group(["middleware" => "auth:api"], function(){
        Route::group(["prefix" => "user"], function(){
            
            Route::get('/getInfo/{id}', [ProfileController::class, 'getUserInfo']);
            Route::put('/updateInfo/{id}', [ProfileController::class, 'updateUserInfo']);

            Route::post('/preferences', [UserPreferenceController::class, 'storeUserPreferences']);

            Route::post('/chat', [GeminiController::class, 'chat']);

            Route::post('/voice-messages', [VoiceMessageController::class, 'store']);
            Route::get('/voice-messages/{otherUserId}', [VoiceMessageController::class, 'index']);
            Route::get('/voice-messages/download/{id}', [VoiceMessageController::class, 'download']);
        });
    });
    Route::group(["prefix" => "guest"], function(){
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/register", [AuthController::class, "register"]);
    });
});