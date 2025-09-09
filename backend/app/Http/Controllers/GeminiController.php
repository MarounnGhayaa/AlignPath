<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GeminiController extends Controller {
    public function chat(Request $request) {
        $data = $request->validate([
            'messages' => 'required|array|min:1',
            'messages.*.role' => 'required|string|in:user,model',
            'messages.*.content' => 'required|string',
            'system' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'maxOutputTokens' => 'nullable|integer',
        ]);

        $model = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $contents = array_map(function ($m) {
            return [
                'role' => $m['role'],
                'parts' => [['text' => $m['content']]]
            ];
        }, $data['messages']);

        $payload = [
            'contents' => $contents,
            
            'system_instruction' => isset($data['system']) ? [
                'parts' => [['text' => $data['system']]]
            ] : null,
            'generationConfig' => array_filter([
                'temperature' => $data['temperature'] ?? 0.7,
                'maxOutputTokens' => $data['maxOutputTokens'] ?? 1024,
            ]),
        ];

        $payload = array_filter($payload, fn($v) => !is_null($v));

        $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-goog-api-key' => env('GEMINI_API_KEY'),
            ])
            ->timeout(30)
            ->post($endpoint, $payload);

        if ($response->failed()) {
            return response()->json([
                'error' => true,
                'status' => $response->status(),
                'message' => $response->json()['error']['message'] ?? 'Gemini API error',
            ], 500);
        }

        $json = $response->json();

        $replyText = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        $blocked = $json['promptFeedback']['blockReason'] ?? null;

        return response()->json([
            'reply' => $replyText,
            'blocked' => $blocked,
            'raw' => $json,
        ]);
    }
}
