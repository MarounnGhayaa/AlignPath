<?php

namespace App\Services\Users;

use App\Exceptions\ServiceException;
use App\Models\ChatMessage;
use App\Models\ChatThread;
use App\Models\LearningResource;
use App\Models\Path;
use App\Models\Problem;
use App\Models\Quest;
use App\Models\Recommendation;
use App\Models\Skill;
use App\Models\User;
use App\Models\UserPath;
use Illuminate\Support\Facades\Http;

class GeminiChatService {
    public function chat(array $data, ?User $user) {
        $context    = $this->buildContext($user);
        $guardrails = $this->guardrailsText();

        $systemText = $guardrails . "\n\nUserContext (JSON):\n" . json_encode($context, JSON_PRETTY_PRINT);
        if (!empty($data['system'])) {
            $systemText .= "\n\nAdditional System Notes:\n" . $data['system'];
        }

        $thread = $this->resolveThread($data['thread_id'] ?? null, $user);
        if (!$thread) {
            $thread = $this->createThread($data['messages'], $user);
        }

        $userMessage = $this->persistUserMessage($thread, $data['messages'], $user);

        $model    = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $payload = [
            'contents' => $this->formatMessages($data['messages']),
            'system_instruction' => [
                'parts' => [['text' => $systemText]],
            ],
            'generationConfig' => array_filter([
                'temperature'     => $data['temperature']     ?? 0.7,
                'maxOutputTokens' => $data['maxOutputTokens'] ?? 1024,
            ]),
        ];

        $t0 = microtime(true);
        $response = Http::withHeaders(['x-goog-api-key' => env('GEMINI_API_KEY')])
            ->timeout(30)
            ->post($endpoint, $payload);
        $latencyMs = (int) round((microtime(true) - $t0) * 1000);

        if ($response->failed()) {
            $json    = $response->json();
            $message = $json['error']['message'] ?? 'Gemini API error';

            throw new ServiceException('Gemini API error', 500, [
                'error'   => true,
                'status'  => $response->status(),
                'message' => $message,
            ]);
        }

        $json      = $response->json();
        $replyText = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        $blocked   = $json['promptFeedback']['blockReason'] ?? null;
        $usage     = $json['usageMetadata'] ?? null;

        $assistantMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'user_id'   => $user?->id,
            'role'      => 'model',
            'content'   => $replyText ?? '⚠️ No response from AI.',
            'meta'      => [
                'blocked'    => $blocked,
                'usage'      => $usage,
                'latency_ms' => $latencyMs,
            ],
        ]);

        $thread->update(['last_message_at' => now()]);

        return [
            'reply'                => $replyText,
            'blocked'              => $blocked,
            'thread_id'            => $thread->id,
            'user_message_id'      => $userMessage?->id,
            'assistant_message_id' => $assistantMessage->id,
            'usage'                => $usage,
            'raw'                  => $json,
        ];
    }

    protected function resolveThread($threadId, ?User $user) {
        if (empty($threadId)) {
            return null;
        }

        return ChatThread::where('id', $threadId)
            ->when($user, fn($query) => $query->where('user_id', $user->id))
            ->first();
    }

    protected function createThread(array $messages, ?User $user) {
        $firstUserMessage = collect($messages)->firstWhere('role', 'user');

        return ChatThread::create([
            'user_id' => $user?->id,
            'title'   => isset($firstUserMessage['content'])
                ? mb_strimwidth(preg_replace('/\s+/', ' ', $firstUserMessage['content']), 0, 60, '…')
                : 'New conversation',
            'metadata' => [
                'model'             => config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash')),
                'guardrails_version' => 'v1',
            ],
            'started_at'      => now(),
            'last_message_at' => now(),
        ]);
    }

    protected function persistUserMessage(ChatThread $thread, array $messages, ?User $user) {
        $lastUserMessage = collect($messages)->reverse()->firstWhere('role', 'user');

        if (!$lastUserMessage) {
            return null;
        }

        return ChatMessage::create([
            'thread_id' => $thread->id,
            'user_id'   => $user?->id,
            'role'      => 'user',
            'content'   => $lastUserMessage['content'],
            'meta'      => [
                'client' => 'web',
            ],
        ]);
    }

    protected function formatMessages(array $messages) {
        return array_map(function ($message) {
            return [
                'role'  => $message['role'],
                'parts' => [['text' => $message['content']]],
            ];
        }, $messages);
    }

    protected function buildContext(?User $user) {
        if (!$user) {
            return [
                'user'            => null,
                'preferences'     => null,
                'paths'           => [],
                'recommendations' => [],
                'skills'          => [],
                'resources'       => [],
                'quests'          => [],
                'problems'        => [],
            ];
        }

        $context = [
            'user' => [
                'id'   => $user->id,
                'name' => $user->name ?? null,
            ],
            'preferences' => null,
        ];

        if (method_exists($user, 'preference') && $user->preference) {
            $context['preferences'] = [
                'skills'    => $user->preference->skills ?? null,
                'interests' => $user->preference->interests ?? null,
                'values'    => $user->preference->values ?? null,
                'careers'   => $user->preference->careers ?? null,
            ];
        }

        $pathIds = UserPath::where('user_id', $user->id)->pluck('path_id');

        $context['paths'] = Path::whereIn('id', $pathIds)
            ->select('id', 'name', 'tag')
            ->limit(10)
            ->get()
            ->toArray();

        $context['recommendations'] = Recommendation::where('user_id', $user->id)
            ->select('career_name', 'description', 'status')
            ->latest()
            ->limit(5)
            ->get()
            ->toArray();

        $context['skills'] = Skill::whereIn('path_id', $pathIds)
            ->select('path_id', 'name', 'value')
            ->limit(12)
            ->get()
            ->toArray();

        $context['resources'] = LearningResource::whereIn('path_id', $pathIds)
            ->select('path_id', 'name', 'type', 'url')
            ->limit(9)
            ->get()
            ->toArray();

        $context['quests'] = Quest::whereIn('path_id', $pathIds)
            ->select('path_id', 'title', 'difficulty', 'duration')
            ->latest()
            ->limit(10)
            ->get()
            ->toArray();

        $context['problems'] = Problem::whereIn('path_id', $pathIds)
            ->select('path_id', 'title', 'points')
            ->latest()
            ->limit(10)
            ->get()
            ->toArray();

        return $context;
    }

    protected function guardrailsText() {
        return <<<TXT
    You are "Career Copilot" for our app. Your scope is strictly LIMITED to:
    - The user's saved career paths and recommendations
    - Their quests, problems, skills, and resources
    - Clarifying questions and guidance about learning these paths/skills

    If a user asks for anything OUTSIDE this domain (e.g., weather, news, sports, politics, recipes, general trivia, programming help unrelated to their saved paths/skills), politely refuse and say:

    "I'm here to help with your learning and career paths (quests, problems, skills, and resources). Try asking about those!"

    Rules:
    - Use ONLY the information from the "UserContext" below and the conversation. Do not invent facts.
    - If something isn't in UserContext or the conversation, say you don't have that info and suggest how to add it.
    - Do NOT browse the web or provide external factual claims.
    - Keep answers concise and actionable for the user.
    TXT;
    }
}
