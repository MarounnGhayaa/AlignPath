<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        return response()->json($payload);
    }

    public function store(Request $request, User $person) {
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

        return response()->json([
            'id'           => $msg->id,
            'message'      => $msg->body,
            'sender_id'    => $msg->sender_id,
            'isFromMentor' => strtolower((string)$me->role) === 'mentor',
            'timestamp'    => optional($msg->created_at)?->toISOString(),
        ], 201);
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
