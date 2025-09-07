<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\UserPath;
use App\Models\Recommendation;
use App\Models\Path;
use App\Models\Quest;

class AiAgentController extends Controller
{
    protected $fastApiBase;
    protected $fastApiToken;

    public function __construct() {
        $this->fastApiBase = env("FASTAPI_AGENT_URL");
        $this->fastApiToken = env("FASTAPI_AGENT_SHARED_SECRET");
    }

    public function acceptPath(Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
        ]);

        $recommendationId = $request->input('recommendation_id');
        $recommendation = Recommendation::findOrFail($recommendationId);

        $path = Path::create([
            'name' => $recommendation->career_name,
            'tag' => $recommendation->description,
        ]);

        $exists = UserPath::where('user_id', $user->id)
            ->where('path_id', $path->id)
            ->exists();

        if (!$exists) {
            UserPath::create([
                'user_id' => $user->id,
                'path_id' => $path->id,
                'progress_percentage' => 0,
                'date_saved' => now(),
            ]);
        }

        $recommendation->update(['status' => 'accepted']);

        return response()->json([
            'message' => 'Path accepted and saved successfully'
        ]);
    }

    public function dismissPath(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
        ]);

        $recommendationId = $request->input('recommendation_id');
        $recommendation = Recommendation::findOrFail($recommendationId);

        $recommendation->update(['status' => 'dismissed']);

        return response()->json([
            'message' => 'Recommendation dismissed successfully'
        ]);
    }

    public function recommendCareers(Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $prefs = $user->preference;
        if (!$prefs) {
            return response()->json(['error' => 'No preferences found'], 404);
        }

        $interests = implode(", ", [
            $prefs->skills,
            $prefs->interests,
            $prefs->values,
            $prefs->careers
        ]);

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$this->fastApiToken}"
        ])->post("{$this->fastApiBase}/recommend-careers", [
            "interests" => $interests
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
        }

        $recommendedCareers = $response->json();

        foreach ($recommendedCareers['career_paths'] as $career) {
            Recommendation::create([
                'user_id' => $user->id,
                'career_name' => $career['title'],
                'description' => $career['description'] ?? null,
            ]);
        }

        return response()->json($recommendedCareers);
    }

    public function generateQuests(Request $request)
    {
        $request->validate([
            'career' => 'required|string',
            'path_id' => 'required|exists:paths,id',
        ]);

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$this->fastApiToken}"
        ])->post("{$this->fastApiBase}/generate-quests", [
            "career" => $request->input('career')
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
        }

        $generatedQuests = $response->json();

        foreach ($generatedQuests['quests'] as $questData) {
            Quest::create([
                'title' => $questData['title'],
                'subtitle' => $questData['subtitle'] ?? '',
                'path_id' => $request->input('path_id'),
                'difficulty' => $questData['difficulty'] ?? null,
                'duration' => $questData['duration'] ?? null,
            ]);
        }

        return response()->json($generatedQuests);
    }
}
