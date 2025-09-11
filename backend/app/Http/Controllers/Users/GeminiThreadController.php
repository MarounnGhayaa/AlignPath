<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ChatThread;
use Illuminate\Support\Str;

class GeminiThreadController extends Controller {
    public function index(Request $request) {
        $user = Auth::user();
        $limit = min(max((int) $request->get('limit', 20), 1), 100);

        $threads = ChatThread::query()
            ->where('user_id', $user->id)
            ->withCount('messages')
            ->with(['messages' => fn($q) => $q->latest('created_at')->limit(1)])
            ->orderByDesc('last_message_at')
            ->paginate($limit);

        $items = $threads->getCollection()->map(function($t){
            $last = $t->messages->first();
            return [
                'id' => $t->id,
                'title' => $t->title ?? 'Untitled',
                'last_message_at' => optional($t->last_message_at)->toIso8601String(),
                'messages_count' => $t->messages_count,
                'preview' => $last ? Str::limit($last->content, 100) : null,
            ];
        });

        return response()->json([
            'items' => $items,
            'meta' => [
                'current_page' => $threads->currentPage(),
                'per_page'     => $threads->perPage(),
                'total'        => $threads->total(),
                'last_page'    => $threads->lastPage(),
            ],
        ]);
    }

    public function show(Request $request, ChatThread $thread) {
        $user = Auth::user();
        abort_unless($thread->user_id === $user->id, 403);

        $messages = $thread->messages()
            ->orderBy('created_at')
            ->get(['id','role','content','created_at'])
            ->map(fn($m) => [
                'id' => $m->id,
                'role' => $m->role,
                'content' => $m->content,
                'created_at' => $m->created_at->toIso8601String(),
            ]);

        return response()->json([
            'thread' => [
                'id' => $thread->id,
                'title' => $thread->title ?? 'Untitled',
                'last_message_at' => optional($thread->last_message_at)->toIso8601String(),
            ],
            'messages' => $messages,
        ]);
    }

    public function update(Request $request, ChatThread $thread) {
        $user = Auth::user();
        abort_unless($thread->user_id === $user->id, 403);

        $data = $request->validate([
            'title' => 'nullable|string|max:120',
        ]);

        $thread->update(['title' => $data['title'] ?? $thread->title]);

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, ChatThread $thread) {
        $user = Auth::user();
        abort_unless($thread->user_id === $user->id, 403);

        $thread->delete();
        return response()->json(['ok' => true]);
    }
}
