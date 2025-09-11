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

Route::group(["prefix" => "v0.1"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::group(["prefix" => "user"], function () {
            Route::get('/getInfo/{id}', [ProfileController::class, 'getUserInfo']);
            Route::get('/paths', [ProfileController::class, 'getUserPaths']);
            Route::put('/updateInfo/{id}', [ProfileController::class, 'updateUserInfo']);

            Route::post('/preferences', [UserPreferenceController::class, 'storeUserPreferences']);

            Route::post('/chat', [GeminiController::class, 'chat']);

            Route::get('/chat/threads', [GeminiThreadController::class, 'index']); // list
            Route::get('/chat/threads/{thread}', [GeminiThreadController::class, 'show']) // open
                ->whereNumber('thread');
            Route::patch('/chat/threads/{thread}', [GeminiThreadController::class, 'update']) // rename (optional)
                ->whereNumber('thread');
            Route::delete('/chat/threads/{thread}', [GeminiThreadController::class, 'destroy']) // delete (optional)
                ->whereNumber('thread');

            Route::post('/accept-path', [AiAgentController::class, 'acceptPath']);
            Route::post('/recommend-careers', [AiAgentController::class, 'recommendCareers']);
            Route::post('/generate-quests-and-problems', [AiAgentController::class, 'generateQuestsAndProblems']);

            Route::get('/quests/{pathId}', [QuestController::class, 'getQuestsByPath']);

            Route::get('/problems/{pathId}', [ProblemController::class, 'getProblemsByPath']);
            Route::get('/problem/{problemId}', [ProblemController::class, 'getProblemById']);

            Route::get('/skills/{pathId}', [SkillController::class, 'getSkillsByPath']);

            Route::get('/resources/{pathId}', [LearningResourceController::class, 'getResourcesByPath']);

            Route::get('/recommendations', [RecommendationController::class, 'getUserRecommendations']);
        });
    });

    Route::group(["prefix" => "guest"], function () {
        Route::post("/login", [AuthController::class, "login"]);
        Route::post("/register", [AuthController::class, "register"]);
    });
});
