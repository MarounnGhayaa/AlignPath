<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPath;
use App\Models\Path;
use App\Models\Recommendation;
use App\Models\Quest;
use App\Models\Problem;
use App\Models\Skill;
use App\Models\LearningResource;
use App\Models\ChatThread;
use App\Models\ChatMessage;
use Illuminate\Validation\Rule;

class GeminiController extends Controller {
    public function chat(Request $request){
        $data = $request->validate([
            'thread_id'            => ['nullable','integer','exists:chat_threads,id'],
            'messages'             => 'required|array|min:1',
            'messages.*.role'      => ['required','string', Rule::in(['user','model'])],
            'messages.*.content'   => 'required|string',
            'system'               => 'nullable|string',
            'temperature'          => 'nullable|numeric',
            'maxOutputTokens'      => 'nullable|integer',
        ]);

        $user = Auth::user();

        // ----- UserContext snapshot (for reproducibility in analytics) -----
        $context = [
            'user' => $user ? [
                'id'   => $user->id,
                'name' => $user->name ?? null,
            ] : null,
            'preferences' => $user && method_exists($user, 'preference') && $user->preference
                ? [
                    'skills'     => $user->preference->skills ?? null,
                    'interests'  => $user->preference->interests ?? null,
                    'values'     => $user->preference->values ?? null,
                    'careers'    => $user->preference->careers ?? null,
                ]
                : null,
        ];

        if ($user) {
            $pathIds = UserPath::where('user_id', $user->id)->pluck('path_id');
            $paths   = Path::whereIn('id', $pathIds)->select('id','name','tag')->limit(10)->get()->toArray();
            $context['paths'] = $paths;

            $context['recommendations'] = Recommendation::where('user_id', $user->id)
                ->select('career_name','description','status')
                ->latest()->limit(5)->get()->toArray();

            $context['skills'] = Skill::whereIn('path_id', $pathIds)
                ->select('path_id','name','value')
                ->limit(12)->get()->toArray();

            $context['resources'] = LearningResource::whereIn('path_id', $pathIds)
                ->select('path_id','name','type','url')
                ->limit(9)->get()->toArray();

            $context['quests'] = Quest::whereIn('path_id', $pathIds)
                ->select('path_id','title','difficulty','duration')
                ->latest()->limit(10)->get()->toArray();

            $context['problems'] = Problem::whereIn('path_id', $pathIds)
                ->select('path_id','title','points')
                ->latest()->limit(10)->get()->toArray();
        }

        $guardrails = <<<TXT
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

        $systemText  = $guardrails . "\n\nUserContext (JSON):\n" . json_encode($context, JSON_PRETTY_PRINT);
        if (!empty($data['system'])) {
            $systemText .= "\n\nAdditional System Notes:\n" . $data['system'];
        }

        // ----- Ensure a thread exists & belongs to the user -----
        $thread = null;
        if (!empty($data['thread_id'])) {
            $thread = ChatThread::where('id', $data['thread_id'])
                ->when($user, fn($q) => $q->where('user_id', $user->id))
                ->first();
        }
        if (!$thread) {
            $firstUserMsg = collect($data['messages'])->firstWhere('role', 'user');
            $thread = ChatThread::create([
                'user_id' => $user?->id,
                'title'   => isset($firstUserMsg['content'])
                    ? mb_strimwidth(preg_replace('/\s+/', ' ', $firstUserMsg['content']), 0, 60, '…')
                    : 'New conversation',
                'metadata' => [
                    'model' => config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash')),
                    'guardrails_version' => 'v1',
                ],
                'started_at'      => now(),
                'last_message_at' => now(),
            ]);
        }

        // ----- Persist the latest user message (only the new turn) -----
        // Frontend always appends; we capture the last user message in this request.
        $lastUserMessage = collect($data['messages'])->reverse()->firstWhere('role','user');
        $userMessage = null;
        if ($lastUserMessage) {
            $userMessage = ChatMessage::create([
                'thread_id' => $thread->id,
                'user_id'   => $user?->id,
                'role'      => 'user',
                'content'   => $lastUserMessage['content'],
                'meta'      => [
                    'client' => 'web',
                ],
            ]);
        }

        // ----- Call Gemini -----
        $model    = config('services.gemini.model', env('GEMINI_MODEL', 'gemini-2.5-flash'));
        $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";

        $contents = array_map(function ($m) {
            return [
                'role'  => $m['role'],          // 'user' or 'model'
                'parts' => [['text' => $m['content']]]
            ];
        }, $data['messages']);

        $payload = [
            'contents' => $contents,
            'system_instruction' => [
                'parts' => [['text' => $systemText]]
            ],
            'generationConfig' => array_filter([
                'temperature'     => $data['temperature']     ?? 0.7,
                'maxOutputTokens' => $data['maxOutputTokens'] ?? 1024,
                // You may add 'response_mime_type' => 'text/markdown' for consistency
            ]),
        ];

        $t0 = microtime(true);
        $response = Http::withHeaders(['x-goog-api-key' => env('GEMINI_API_KEY')])
            ->timeout(30)
            ->post($endpoint, $payload);
        $latencyMs = (int) round((microtime(true) - $t0) * 1000);

        if ($response->failed()) {
            return response()->json([
                'error'   => true,
                'status'  => $response->status(),
                'message' => $response->json()['error']['message'] ?? 'Gemini API error',
            ], 500);
        }

        $json      = $response->json();
        $replyText = $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
        $blocked   = $json['promptFeedback']['blockReason'] ?? null;
        $usage     = $json['usageMetadata'] ?? null;

        // ----- Persist assistant reply -----
        $assistantMessage = ChatMessage::create([
            'thread_id' => $thread->id,
            'user_id'   => $user?->id,
            'role'      => 'model',
            'content'   => $replyText ?? '⚠️ No response from AI.',
            'meta'      => [
                'blocked' => $blocked,
                'usage'   => $usage,
                'latency_ms' => $latencyMs,
            ],
        ]);

        $thread->update(['last_message_at' => now()]);

        return response()->json([
            'reply'                 => $replyText,
            'blocked'               => $blocked,
            'thread_id'             => $thread->id,
            'user_message_id'       => $userMessage?->id,
            'assistant_message_id'  => $assistantMessage->id,
            'usage'                 => $usage,
            // Keep raw for debugging during development; consider removing in prod
            'raw'                   => $json,
        ]);
    }
}
