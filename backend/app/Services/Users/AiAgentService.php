<?php

namespace App\Services\Users;

use Illuminate\Support\Facades\Http;

class AiAgentService {
    protected string $geminiApiKey;
    protected string $model;
    protected string $endpoint;

    public function __construct() {
        $this->geminiApiKey = env('GEMINI_API_KEY');
        $this->model        = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $this->endpoint     = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent";
    }


    public function recommendCareers(string $interests): array {
        $prompt = "Based on the following interests: {$interests}, 
                   suggest exactly 3 career paths with a title and a short description for each. Respond in valid JSON like this:
                   {
                     \"career_paths\": [
                        {\"title\": \"string\", \"description\": \"string\"}
                     ]
                   }
                   - Arrays MUST have exactly this length: career_paths=3.
                   - Do NOT include any explanations, markdown, or extra keys outside the schema.";

        $raw = $this->callGemini($prompt);
        $parsed = $this->extractJson($raw) ?? [];

        return $parsed['career_paths'] ?? [
            ['title' => 'Software Engineer', 'description' => 'Designs and builds software systems.'],
            ['title' => 'Data Scientist',    'description' => 'Analyzes data for insights and predictions.'],
            ['title' => 'Product Manager',   'description' => 'Coordinates teams to deliver products.'],
        ];
    }

    public function generateForPath(string $career): array {
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

        $raw = $this->callGemini($prompt);
        $parsed = $this->extractJson($raw) ?? [];

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
            ['name' => 'Teamwork', 'value' => 0]
        ];

        $resources = $parsed['resources'] ?? [
            [
                'name' => 'laravel documentation',
                'description' => 'used this documentation to provide info',
                'type' => 'documentation',
                'url' => 'https://laravel.com/docs/12.x'
            ]
        ];

        return compact('quests', 'problems', 'skills', 'resources');
    }


    protected function callGemini(string $prompt): string {
        $response = Http::timeout(120)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post("{$this->endpoint}?key={$this->geminiApiKey}", [
                'contents' => [[ 'parts' => [[ 'text' => $prompt ]] ]]
            ]);

        if ($response->failed()) {
            $details = $response->json();
            throw new \RuntimeException('Gemini request failed: ' . json_encode($details));
        }

        $aiResult = $response->json();
        return $aiResult['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }

    protected function extractJson(string $text): ?array {
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
