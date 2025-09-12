<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller {
    public function show(Request $request, User $person) {
        $me = Auth::user();
        abort_unless($me !== null, 401);
        abort_if($me->id === $person->id, 400, 'Cannot open chat with yourself.');

        $conversation = $this->getOrCreateConversation($me->id, $person->id);

        $messages = Message::query()
            ->where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $payload = $messages->map(function (Message $m) use ($me, $person) {
            $senderIsMentor = $m->sender_id === $me->id
                ? (strtolower((string)$me->role) === 'mentor')
                : (strtolower((string)$person->role) === 'mentor');

            return [
                'id'           => $m->id,
                'message'      => $m->body,
                'sender_id'    => $m->sender_id,
                'isFromMentor' => $senderIsMentor,
                'timestamp'    => optional($m->created_at)?->toISOString(),
            ];
        });

        return response()->json($payload->values());
    }

    public function store(Request $request, User $person){
        $me = Auth::user();
        abort_unless($me !== null, 401);

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        abort_if($me->id === $person->id, 400, 'Cannot message yourself.');

        $conversation = $this->getOrCreateConversation($me->id, $person->id);

        $msg = new Message();
        $msg->conversation_id = $conversation->id;
        $msg->sender_id = $me->id;
        $msg->body = $data['message'];
        $msg->save();

        $ui = [
            'id'               => $msg->id,
            'message'          => $msg->body,
            'sender_id'        => $msg->sender_id,
            'sender_role'      => strtolower((string)$me->role),
            'isFromMentor'     => strtolower((string)$me->role) === 'mentor',
            'timestamp'        => optional($msg->created_at)?->toISOString(),
            'conversation_id'  => $conversation->id,
            'peer_id'          => $me->id,
        ];

        $recipientIds = DB::table('conversation_participants')
            ->where('conversation_id', $conversation->id)
            ->where('user_id', '!=', $me->id)
            ->pluck('user_id')
            ->unique()
            ->values()
            ->all();

        $this->notifySocket($recipientIds, $ui);

        return response()->json($ui, 201);
    }

    protected function notifySocket(array $recipientIds, array $payload): void {
        if (empty($recipientIds)) {
            return;
        }

        $endpoint = rtrim(config('services.socket.endpoint', env('SOCKET_ENDPOINT', 'http://localhost:4000/hooks/message-created')), '/');
        $secret   = config('services.socket.secret',   env('SOCKET_SECRET',   'dev_secret'));

        try {
            Http::withHeaders(['X-Webhook-Secret' => $secret])
                ->post($endpoint, [
                    'recipientIds' => $recipientIds,
                    'payload'      => $payload,
                ]);
        } catch (\Throwable $e) {
            Log::warning('Socket webhook failed: '.$e->getMessage());
        }
    }

    protected function getOrCreateConversation(int $userA, int $userB): Conversation {
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
}
