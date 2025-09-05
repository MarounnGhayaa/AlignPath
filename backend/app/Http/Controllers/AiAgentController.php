<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\UserPath;
use App\Models\AIRecommendation;

class AiAgentController extends Controller
{
    protected $fastApiBase;
    protected $fastApiToken;

    public function __construct()
    {
        $this->fastApiBase = env("FASTAPI_AGENT_URL");
        $this->fastApiToken = env("FASTAPI_AGENT_SHARED_SECRET");
    }

    /**
     * Accept a recommended path
     */
    public function acceptPath(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'path_id' => 'required|exists:paths,id',
        ]);

        $pathId = $request->input('path_id');

        // Insert into users_paths if not already saved
        $exists = UserPath::where('user_id', $user->id)
            ->where('path_id', $pathId)
            ->exists();

        if (!$exists) {
            UserPath::create([
                'user_id' => $user->id,
                'path_id' => $pathId,
                'progress_percentage' => 0,
                'date_saved' => now(),
            ]);
        }

        // Update AIRecommendations status
        AIRecommendation::where('user_id', $user->id)
            ->where('path_id', $pathId)
            ->update(['status' => 'accepted']);

        return response()->json([
            'message' => 'Path accepted and saved successfully'
        ]);
    }

    /**
     * Dismiss a recommended path
     */
    public function dismissPath(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'path_id' => 'required|exists:paths,id',
        ]);

        $pathId = $request->input('path_id');

        // Update AIRecommendations status
        AIRecommendation::where('user_id', $user->id)
            ->where('path_id', $pathId)
            ->update(['status' => 'dismissed']);

        return response()->json([
            'message' => 'Path dismissed successfully'
        ]);
    }

    /**
     * Call FastAPI to recommend careers based on user preferences
     */
    public function recommendCareers(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Pull user preferences from DB
        $prefs = $user->preference; // relation User → UserPreference
        if (!$prefs) {
            return response()->json(['error' => 'No preferences found'], 404);
        }

        // Build interests string from skills, interests, values, careers
        $interests = implode(", ", [
            $prefs->skills,
            $prefs->interests,
            $prefs->values,
            $prefs->careers
        ]);

        // Call FastAPI
        $response = Http::withHeaders([
            "Authorization" => "Bearer super_secret_between_laravel_and_ai"
        ])->post("{$this->fastApiBase}/recommend-careers", [
            "interests" => $interests
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
        }

        return $response->json();
    }

    /**
     * Call FastAPI to generate quests for a given career
     */
    public function generateQuests(Request $request)
    {
        $request->validate([
            'career' => 'required|string',
        ]);

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$this->fastApiToken}"
        ])->post("{$this->fastApiBase}/generate-quests", [
            "career" => $request->input('career')
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
        }

        return $response->json();
    }
}
