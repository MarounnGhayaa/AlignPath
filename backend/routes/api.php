<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\AuthController;
use App\Http\Controllers\Users\ProfileController;
use App\Http\Controllers\Users\GeminiController;
use App\Http\Controllers\Users\UserPreferenceController;
use App\Http\Controllers\Users\AiAgentController;
use App\Http\Controllers\Users\RecommendationController;
use App\Http\Controllers\Users\QuestController;
use App\Http\Controllers\Users\ProblemController;
use App\Http\Controllers\Users\SkillController;
use App\Http\Controllers\Users\LearningResourceController;
use App\Http\Controllers\Users\GeminiThreadController;
use App\Http\Controllers\Users\MentorController;
use App\Http\Controllers\Users\ChatController;
use App\Http\Controllers\Users\UserDirectoryController;

Route::group(["prefix" => "v0.1"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::group(["prefix" => "user"], function () {
            Route::get('/getInfo/{id}', [ProfileController::class, 'getUserInfo']);
            Route::get('/paths', [ProfileController::class, 'getUserPaths']);
            Route::put('/updateInfo/{id}', [ProfileController::class, 'updateUserInfo']);

            Route::post('/preferences', [UserPreferenceController::class, 'storeUserPreferences']);

            Route::post('/chat', [GeminiController::class, 'chat']);

            Route::get('/chat/threads', [GeminiThreadController::class, 'index']);
            Route::get('/chat/threads/{thread}', [GeminiThreadController::class, 'show'])
                ->whereNumber('thread');

            Route::post('/accept-path', [AiAgentController::class, 'acceptPath']);
            Route::post('/recommend-careers', [AiAgentController::class, 'recommendCareers']);
            Route::post('/generate-quests-and-problems', [AiAgentController::class, 'generateQuestsAndProblems']);

            Route::get('/quests/{pathId}', [QuestController::class, 'getQuestsByPath']);

            Route::get('/problems/{pathId}', [ProblemController::class, 'getProblemsByPath']);
            Route::get('/problem/{problemId}', [ProblemController::class, 'getProblemById']);

            Route::get('/skills/{pathId}', [SkillController::class, 'getSkillsByPath']);
            Route::put('/skills/{skill}', [SkillController::class, 'update'])
                ->whereNumber('skill');

            Route::get('/resources/{pathId}', [LearningResourceController::class, 'getResourcesByPath']);

            Route::get('/recommendations', [RecommendationController::class, 'getUserRecommendations']);

            Route::get('/mentors', [MentorController::class, 'index']);

            Route::get('/users', [UserDirectoryController::class, 'index']);

            Route::get('/mentors/{person}/chats',   [ChatController::class, 'show']);
            Route::post('/mentors/{person}/messages', [ChatController::class, 'store']);
            Route::get('/users/{person}/chats',     [ChatController::class, 'show']);
            Route::post('/users/{person}/messages',   [ChatController::class, 'store']);
            Route::post('/transcribe', [ChatController::class, 'transcribe'])
                ->withoutMiddleware('throttle:api');
        });
    });

    Route::group(["prefix" => "guest"], function () {
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/register", [AuthController::class, "register"]);
    });
});
