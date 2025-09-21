<?php

namespace App\Services\N8N;

use App\Models\ChatMessage;
use Illuminate\Support\Carbon;

class ChatMessageService {
    public function listForDay($start, $tz = 'UTC', $threadId = null, $limit = 500, $cursor = null) {
        $tz = $tz ?: 'UTC';

        $startLocal = Carbon::parse($start, $tz);
        $endLocal   = (clone $startLocal)->addDay();

        $startUtc = $startLocal->copy()->timezone('UTC');
        $endUtc   = $endLocal->copy()->timezone('UTC');

        $query = ChatMessage::query()
            ->when($threadId, fn ($q) => $q->where('thread_id', $threadId))
            ->whereBetween('created_at', [$startUtc, $endUtc])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if ($cursor) {
            [$cCreatedAt, $cId] = explode('|', base64_decode($cursor));
            $query->where(function ($q) use ($cCreatedAt, $cId) {
                $q->where('created_at', '<', $cCreatedAt)
                  ->orWhere(function ($q2) use ($cCreatedAt, $cId) {
                      $q2->where('created_at', $cCreatedAt)->where('id', '<', $cId);
                  });
            });
        }

        $rows = $query->limit($limit + 1)->get();

        $nextCursor = null;
        if ($rows->count() > $limit) {
            $last = $rows[$limit - 1];
            $nextCursor = base64_encode($last->created_at->toIso8601String() . '|' . $last->id);
            $rows = $rows->take($limit);
        }

        return [
            'meta' => [
                'start_local' => $startLocal->toIso8601String(),
                'end_local'   => $endLocal->toIso8601String(),
                'tz'          => $tz,
                'count'       => $rows->count(),
                'next_cursor' => $nextCursor,
            ],
            'data' => $rows,
        ];
    }
}
