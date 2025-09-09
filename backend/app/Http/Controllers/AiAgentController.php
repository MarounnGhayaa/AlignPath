<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserPath;
use App\Models\Recommendation;
use App\Models\Path;
use App\Models\Quest;
use App\Models\Problem;
use App\Models\Skill;
use App\Models\LearningResource;
use App\Services\Users\AiAgentService;

class AiAgentController extends Controller {
    protected AiAgentService $ai;

    public function __construct(AiAgentService $ai) {
        $this->ai = $ai;
    }

    public function acceptPath(Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
        ]);

        $recommendation = Recommendation::findOrFail($request->input('recommendation_id'));

        $path = Path::create([
            'name' => $recommendation->career_name,
            'tag'  => $recommendation->description,
        ]);

        $exists = UserPath::where('user_id', $user->id)
            ->where('path_id', $path->id)
            ->exists();

        if (!$exists) {
            UserPath::create([
                'user_id'             => $user->id,
                'path_id'             => $path->id,
                'progress_percentage' => 0,
                'date_saved'          => now(),
            ]);
        }

        $recommendation->update(['status' => 'accepted']);

        return response()->json([
            'message'     => 'Path accepted and saved successfully',
            'path_id'     => $path->id,
            'career_name' => $recommendation->career_name
        ]);
    }

    public function recommendCareers(Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $prefs = $user->preference;
        if (!$prefs) return response()->json(['error' => 'No preferences found'], 404);

        $interests = implode(", ", [
            $prefs->skills,
            $prefs->interests,
            $prefs->values,
            $prefs->careers
        ]);

        try {
            $careerPaths = $this->ai->recommendCareers($interests);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'AI agent failed', 'details' => $e->getMessage()], 502);
        }

        foreach ($careerPaths as $career) {
            Recommendation::create([
                'user_id'     => $user->id,
                'career_name' => $career['title'],
                'description' => $career['description'] ?? null,
            ]);
        }

        return response()->json(['career_paths' => $careerPaths]);
    }

    public function generateQuestsAndProblems(Request $request) {
        $request->validate([
            'career'  => 'required|string',
            'path_id' => 'required|exists:paths,id',
        ]);

        $career = $request->input('career');
        $pathId = $request->input('path_id');

        try {
            $result = $this->ai->generateForPath($career);
        } catch (\Throwable $e) {
            return response()->json([
                'error'   => 'AI agent failed',
                'details' => $e->getMessage(),
            ], 502);
        }

        $quests    = $result['quests'];
        $problems  = $result['problems'];
        $skills    = $result['skills'];
        $resources = $result['resources'];

        foreach ($quests as $q) {
            Quest::create([
                'title'      => $q['title'],
                'subtitle'   => $q['subtitle'] ?? '',
                'path_id'    => $pathId,
                'difficulty' => $q['difficulty'] ?? null,
                'duration'   => $q['duration'] ?? null,
            ]);
        }

        foreach ($problems as $p) {
            Problem::create([
                'title'          => $p['title'],
                'subtitle'       => $p['subtitle'] ?? '',
                'path_id'        => $pathId,
                'question'       => $p['question'],
                'first_answer'   => $p['first_answer'],
                'second_answer'  => $p['second_answer'],
                'third_answer'   => $p['third_answer'],
                'correct_answer' => $p['correct_answer'],
                'points'         => $p['points'] ?? 0,
            ]);
        }

        foreach ($skills as $s) {
            Skill::create([
                'path_id' => $pathId,
                'name'    => $s['name'],
                'value'   => $s['value'],
            ]);
        }

        foreach ($resources as $r) {
            LearningResource::create([
                'path_id'     => $pathId,
                'name'        => $r['name'],
                'description' => $r['description'] ?? null,
                'type'        => $r['type'],
                'url'         => $r['url'],
            ]);
        }

        return response()->json([
            'quests'             => $quests,
            'problems'           => $problems,
            'skills'             => $skills,
            'learningResources'  => $resources,
        ]);
    }
}
