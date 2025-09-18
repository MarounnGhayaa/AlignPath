<?php

namespace App\Services\Users;

use App\Exceptions\ServiceException;
use App\Models\ChatThread;
use App\Models\User;
use Illuminate\Support\Str;

class GeminiThreadService
{
    public function listThreads(User $user, int $limit): array
    {
        $threads = ChatThread::query()
            ->where('user_id', $user->id)
            ->withCount('messages')
            ->with(['messages' => fn($query) => $query->latest('created_at')->limit(1)])
            ->orderByDesc('last_message_at')
            ->paginate($limit);

        $items = $threads->getCollection()->map(function (ChatThread $thread) {
            $last = $thread->messages->first();

            return [
                'id'              => $thread->id,
                'title'           => $thread->title ?? 'Untitled',
                'last_message_at' => optional($thread->last_message_at)?->toIso8601String(),
                'messages_count'  => $thread->messages_count,
                'preview'         => $last ? Str::limit($last->content, 100) : null,
            ];
        })->values()->all();

        return [
            'items' => $items,
            'meta'  => [
                'current_page' => $threads->currentPage(),
                'per_page'     => $threads->perPage(),
                'total'        => $threads->total(),
                'last_page'    => $threads->lastPage(),
            ],
        ];
    }

    public function showThread(User $user, ChatThread $thread): array
    {
        $this->assertOwnership($user, $thread);

        $messages = $thread->messages()
            ->orderBy('created_at')
            ->get(['id', 'role', 'content', 'created_at'])
            ->map(fn($message) => [
                'id'         => $message->id,
                'role'       => $message->role,
                'content'    => $message->content,
                'created_at' => $message->created_at->toIso8601String(),
            ])
            ->values()
            ->all();

        return [
            'thread' => [
                'id'              => $thread->id,
                'title'           => $thread->title ?? 'Untitled',
                'last_message_at' => optional($thread->last_message_at)?->toIso8601String(),
            ],
            'messages' => $messages,
        ];
    }

    public function updateThread(User $user, ChatThread $thread, array $data): void
    {
        $this->assertOwnership($user, $thread);

        $thread->update([
            'title' => $data['title'] ?? $thread->title,
        ]);
    }

    public function deleteThread(User $user, ChatThread $thread): void
    {
        $this->assertOwnership($user, $thread);
        $thread->delete();
    }

    protected function assertOwnership(User $user, ChatThread $thread): void
    {
        if ($thread->user_id !== $user->id) {
            throw new ServiceException('Forbidden', 403, [
                'message' => 'Forbidden',
            ]);
        }
    }
}
