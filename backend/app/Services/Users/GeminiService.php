<?php

namespace App\Services\Users;

use App\Models\ChatThread;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GeminiService {
    public function reply(array $payload, ?ChatThread $thread, ?int $userId): array {
        $model  = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $apiKey = config('services.gemini.key', env('GEMINI_API_KEY'));

        if (!$thread) {
            $firstUserMsg = collect($payload['messages'] ?? [])->firstWhere('role', 'user');
            $thread = ChatThread::create([
                'user_id'  => $userId,
                'title'    => isset($firstUserMsg['content'])
                    ? mb_strimwidth(preg_replace('/\s+/', ' ', $firstUserMsg['content']), 0, 60, '…')
                    : 'New conversation',
                'metadata' => [
                    'model'               => $model,
                    'guardrails_version'  => 'v1',
                ],
            ]);
        }

        $endpoint = rtrim(config('services.gemini.endpoint', 'https://generativelanguage.googleapis.com/v1beta/models'), '/')
            . "/{$model}:generateContent?key={$apiKey}";

        $res = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($endpoint, [
                'contents' => array_map(function ($m) {
                    return [
                        'role'  => $m['role'],
                        'parts' => [['text' => (string) $m['content']]],
                    ];
                }, $payload['messages'] ?? []),
                'generationConfig' => array_filter([
                    'temperature' => Arr::get($payload, 'options.temperature'),
                    'maxOutputTokens' => Arr::get($payload, 'options.max_tokens'),
                ]),
                'safetySettings'   => Arr::get($payload, 'safety') ?? [],
            ]);

        if ($res->failed()) {
            abort($res->status(), $res->body());
        }

        $json      = $res->json();
        $replyText = trim((string) (Arr::get($json, 'candidates.0.content.parts.0.text') ?? ''));
        $blocked   = (bool) Arr::get($json, 'promptFeedback.blocked', false);
        $usage     = Arr::only($json, ['usageMetadata', 'candidates']);

        $thread->update(['last_message_at' => now()]);

        return compact('replyText', 'blocked', 'usage', 'thread', 'json');
    }
}
