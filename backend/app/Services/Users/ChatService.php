<?php

namespace App\Services\Users;

use App\Exceptions\ServiceException;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ChatService {
    public function getConversationMessages(User $me, User $person) {
        $conversation = $this->getOrCreateConversation($me->id, $person->id);

        return Message::query()
            ->where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn(Message $message) => $this->formatMessage($message, $me, $person))
            ->toArray();
    }

    public function storeMessage(User $me, User $person, string $body) {
        $conversation = $this->getOrCreateConversation($me->id, $person->id);

        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->sender_id = $me->id;
        $message->body = $body;
        $message->save();

        return $this->formatMessage($message, $me, $person);
    }

    public function transcribe(UploadedFile $file, ?string $language = null) {
        $sttUrl = rtrim((string) env('STT_FALLBACK_URL', ''), '/');
        if ($sttUrl === '') {
            throw new ServiceException('STT_FALLBACK_URL is not set', 500, [
                'error' => 'STT_FALLBACK_URL is not set',
            ]);
        }

        $request = Http::asMultipart()
            ->attach('audio', fopen($file->getRealPath(), 'r'), $file->getClientOriginalName());

        if ($language !== null) {
            $request->attach('language', $language);
        }

        $response = $request->post($sttUrl);

        if (!$response->successful()) {
            $payload = [
                'source' => 'local-stt',
                'error'  => $response->json() ?: $response->body(),
            ];

            throw new ServiceException('Transcription failed', $response->status(), $payload);
        }

        return [
            'text' => (string) ($response->json('text') ?? ''),
        ];
    }

    protected function getOrCreateConversation(int $userA, int $userB) {
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

        $conversation = new Conversation();
        $conversation->save();

        DB::table('conversation_participants')->insert([
            ['conversation_id' => $conversation->id, 'user_id' => $ids[0]],
            ['conversation_id' => $conversation->id, 'user_id' => $ids[1]],
        ]);

        return $conversation;
    }

    protected function formatMessage(Message $message, User $me, User $person) {
        $senderIsMentor = $message->sender_id === $me->id
            ? (strtolower((string) $me->role) === 'mentor')
            : (strtolower((string) $person->role) === 'mentor');

        return [
            'id'           => $message->id,
            'message'      => $message->body,
            'sender_id'    => $message->sender_id,
            'isFromMentor' => $senderIsMentor,
            'timestamp'    => optional($message->created_at)?->toISOString(),
        ];
    }
}
