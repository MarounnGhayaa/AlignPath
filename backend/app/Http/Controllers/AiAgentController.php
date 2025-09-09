<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\UserPath;
use App\Models\Recommendation;
use App\Models\Path;
use App\Models\Quest;
use App\Models\Problem;
use App\Models\Skill;
use App\Models\LearningResource;


class AiAgentController extends Controller {
    protected $geminiApiKey;
    protected $model;
    protected $endpoint;

    public function __construct() {
        $this->geminiApiKey = env("GEMINI_API_KEY");
        $this->model = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }

    public function health() {
        return response()->json([
            'status'  => 'ok',
            'message' => 'AI Agent is running in Laravel',
            'model'   => $this->model,
            'time'    => now()->toDateTimeString(),
        ]);
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

    public function dismissPath(Request $request) {
        $user = $request->user();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
        ]);

        $recommendation = Recommendation::findOrFail($request->input('recommendation_id'));
        $recommendation->update(['status' => 'dismissed']);

        return response()->json(['message' => 'Recommendation dismissed successfully']);
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

        $prompt = "Based on the following interests: {$interests}, 
                   suggest exactly 3 career paths with a title and a short description for each. Respond in valid JSON like this:
                   {
                     \"career_paths\": [
                        {\"title\": \"string\", \"description\": \"string\"},
                     ]
                   }
                - Arrays MUST have exactly this length: career_paths=3.
                - Do NOT include any explanations, markdown, or extra keys outside the schema.";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post("{$this->endpoint}?key={$this->geminiApiKey}", [
            'contents' => [[
                'parts' => [['text' => $prompt]]
            ]]
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
        }

        $aiResult = $response->json();
        $careerText = $aiResult['candidates'][0]['content']['parts'][0]['text'] ?? '';

        $careerPaths = $this->extractJson($careerText)['career_paths'] ?? [
            ['title' => 'Software Engineer', 'description' => 'Designs and builds software systems.'],
            ['title' => 'Data Scientist', 'description' => 'Analyzes data for insights and predictions.'],
            ['title' => 'Product Manager', 'description' => 'Coordinates teams to deliver products.'],
        ];

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

        $prompt = "You are a JSON-only API. Output ONLY valid JSON (no markdown fences, no prose, no comments).

        Generate content for the career '{$career}' with the following EXACT counts and schema:

        - Exactly 10 beginner-friendly quests, each with:
        - title (string)
        - subtitle (string)
        - difficulty (one of: easy, medium, hard)
        - duration (human string like '1 week', '2 hours')

        - Exactly 10 multiple-choice problems (1 per quest is fine), each with:
        - title (string)
        - subtitle (string)
        - question (string)
        - first_answer (string)
        - second_answer (string)
        - third_answer (string)
        - correct_answer (string; MUST be exactly one of first_answer, second_answer, or third_answer)
        - points (integer)

        - Exactly 6 skills, each with:
        - name (string)
        - value (integer from 0 to 100 inclusive)

        - Exactly 3 resources (authoritative and beginner-friendly), each with:
        - name (string)
        - description (string)
        - type (one of: documentation, video, community)
        - url (valid absolute URL starting with https://)

        Hard requirements:
        - Return ONLY a single JSON object matching this schema:
        {
        \"quests\": [
            {\"title\":\"string\",\"subtitle\":\"string\",\"difficulty\":\"easy|medium|hard\",\"duration\":\"string\"}
        ],
        \"problems\": [
            {\"title\":\"string\",\"subtitle\":\"string\",\"question\":\"string\",\"first_answer\":\"string\",\"second_answer\":\"string\",\"third_answer\":\"string\",\"correct_answer\":\"string\",\"points\":1}
        ],
        \"skills\": [
            {\"name\":\"string\",\"value\":0}
        ],
        \"resources\": [
            {\"name\":\"string\",\"description\":\"string\",\"type\":\"documentation\",\"url\":\"https://...\"}
        ]
        }
        - Arrays MUST have exactly these lengths: quests=10, problems=10, skills=6, resources=3.
        - \"value\" MUST be 0.
        - \"type\" MUST be exactly one of: documentation, video, community.
        - Do NOT include any explanations, markdown, or extra keys outside the schema.";


        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->endpoint}?key={$this->geminiApiKey}", [
                    'contents' => [[
                        'parts' => [['text' => $prompt]]
                    ]]
                ]);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            return response()->json([
                'error'   => 'AI agent connection failed',
                'message' => $e->getMessage()
            ], 504);
        }

        if ($response->failed()) {
            return response()->json([
                'error'   => 'AI agent failed',
                'details' => $response->json()
            ], 502);
        }

        $aiResult = $response->json();
        $rawText = $aiResult['candidates'][0]['content']['parts'][0]['text'] ?? '';
        $parsed  = $this->extractJson($rawText);

        $quests = $parsed['quests'] ?? [
            ['title' => 'Learn sorting algorithms', 'subtitle' => 'Start with Bubble and Merge sort', 'difficulty' => 'easy', 'duration' => '1 week']
        ];

        $problems = $parsed['problems'] ?? [
            [
                'title'          => 'Implement Bubble Sort',
                'subtitle'       => 'Sort an array of integers',
                'question'       => 'Given an array of integers, implement bubble sort.',
                'first_answer'   => 'Bubble sort',
                'second_answer'  => 'Merge sort',
                'third_answer'   => 'Quick sort',
                'correct_answer' => 'Bubble sort',
                'points'         => 10
            ]
        ];

        $skills = $parsed['skills'] ?? [
            'name' => 'Teamwork',
            'value' => 0
        ];

        $resources = $parsed['resources'] ?? [
            'name' => 'laravel documentation',
            'description' => 'used this documentation to provide info',
            'type' => 'documentation',
            'url' => 'https://laravel.com/docs/12.x'
        ];

        foreach ($quests as $questData) {
            Quest::create([
                'title'      => $questData['title'],
                'subtitle'   => $questData['subtitle'] ?? '',
                'path_id'    => $pathId,
                'difficulty' => $questData['difficulty'] ?? null,
                'duration'   => $questData['duration'] ?? null,
            ]);
        }

        foreach ($problems as $problemData) {
            Problem::create([
                'title'          => $problemData['title'],
                'subtitle'       => $problemData['subtitle'] ?? '',
                'path_id'        => $pathId,
                'question'       => $problemData['question'],
                'first_answer'   => $problemData['first_answer'],
                'second_answer'  => $problemData['second_answer'],
                'third_answer'   => $problemData['third_answer'],
                'correct_answer' => $problemData['correct_answer'],
                'points'         => $problemData['points'] ?? 0,
            ]);
        }

        foreach ($skills as $skillData) {
            Skill::create([
                'path_id' => $pathId,
                'name'    => $skillData['name'],
                'value'   => $skillData['value'],
            ]);
        }

        foreach ($resources as $resData) {
            LearningResource::create([
                'path_id'     => $pathId,
                'name'        => $resData['name'],
                'description' => $resData['description'] ?? null,
                'type'        => $resData['type'],
                'url'         => $resData['url'],
            ]);
        }

        return response()->json([
            'quests'   => $quests,
            'problems' => $problems,
            'skills' => $skills,
            'learningResources' => $resources,
        ]);
    }


    private function extractJson(string $text) {
        $data = json_decode($text, true);
        if (json_last_error() === JSON_ERROR_NONE) return $data;

        if (preg_match('/```json(.*?)```/s', $text, $matches)) {
            $data = json_decode(trim($matches[1]), true);
            if (json_last_error() === JSON_ERROR_NONE) return $data;
        }

        if (preg_match('/(\{.*\})/s', $text, $matches)) {
            $data = json_decode($matches[1], true);
            if (json_last_error() === JSON_ERROR_NONE) return $data;
        }

        return null;
    }
}
