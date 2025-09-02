<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\GeminiController;

Route::group(["prefix" =>"v0.1"], function(){
    Route::group(["middleware" => "auth:api"], function(){
        Route::group(["prefix" => "user"], function(){
            
            Route::get('/getInfo/{id}', [ProfileController::class, 'getUserInfo']);
            Route::put('/updateInfo/{id}', [ProfileController::class, 'updateUserInfo']);

            Route::post('/chat', [GeminiController::class, 'chat']);
        });
    });
    Route::group(["prefix" => "guest"], function(){
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/register", [AuthController::class, "register"]);
    });
});