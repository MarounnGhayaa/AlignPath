<?php

namespace App\Services\Users;

use App\Models\ChatThread;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class GeminiThreadService {
    public function listForUser(int $userId, int $limit = 20): LengthAwarePaginator {
        return ChatThread::query()
            ->where('user_id', $userId)
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->paginate($limit);
    }

    public function show(ChatThread $thread, int $userId): ChatThread {
        abort_unless($thread->user_id === $userId, 403);
        return $thread;
    }

    public function rename(ChatThread $thread, int $userId, string $title): void {
        abort_unless($thread->user_id === $userId, 403);
        $thread->update(['title' => $title]);
    }

    public function delete(ChatThread $thread, int $userId): void {
        abort_unless($thread->user_id === $userId, 403);
        $thread->delete();
    }
}
