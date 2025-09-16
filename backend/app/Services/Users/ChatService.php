<?php

namespace App\Services\Users;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ChatService {
    public function getOrCreateConversation(int $userA, int $userB): Conversation
    {
        $ids = [$userA, $userB];
        sort($ids);

        $existingId = DB::table('conversations as c')
            ->join('conversation_participants as p1', 'p1.conversation_id', '=', 'c.id')
            ->join('conversation_participants as p2', 'p2.conversation_id', '=', 'c.id')
            ->where('p1.user_id', $ids[0])
            ->where('p2.user_id', $ids[1])
            ->value('c.id');

        if ($existingId) {
            return Conversation::findOrFail($existingId);
        }

        $conv = new Conversation();
        $conv->save();

        DB::table('conversation_participants')->insert([
            ['conversation_id' => $conv->id, 'user_id' => $ids[0]],
            ['conversation_id' => $conv->id, 'user_id' => $ids[1]],
        ]);

        return $conv;
    }

    public function sendMessage(Conversation $conv, int $senderId, string $text): Message {
        return Message::create([
            'conversation_id' => $conv->id,
            'user_id'         => $senderId,
            'content'         => $text,
        ]);
    }

    public function transcribe(UploadedFile $audio, ?string $lang = null): string {
        $res = Http::attach('audio', file_get_contents($audio->getRealPath()), $audio->getClientOriginalName())
            ->post(config('services.local_stt.url', 'http://localhost:3030/transcribe'), array_filter([
                'language' => $lang,
            ]));

        if ($res->failed()) {
            abort($res->status(), json_encode([
                'source' => 'local-stt',
                'error'  => $res->json() ?: $res->body(),
            ]));
        }

        return (string) ($res->json('text') ?? '');
    }
}
