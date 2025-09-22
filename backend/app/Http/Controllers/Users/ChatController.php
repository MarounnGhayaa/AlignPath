<?php

namespace App\Http\Controllers\Users;

use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Users\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller {
    protected ChatService $chatService;

    public function __construct(ChatService $chatService) {
        $this->chatService = $chatService;
    }

    public function show(Request $request, User $person) {
        $me = Auth::user();
        abort_unless($me !== null, 401);
        abort_if($me->id === $person->id, 400, 'Cannot open chat with yourself.');

        $messages = $this->chatService->getConversationMessages($me, $person);

        return response()->json($messages);
    }

    public function store(Request $request, User $person){
        $me = Auth::user();
        abort_unless($me !== null, 401);

        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        abort_if($me->id === $person->id, 400, 'Cannot message yourself.');

        $message = $this->chatService->storeMessage($me, $person, $data['message']);

        return response()->json($message, 201);
    }

    public function transcribe(Request $request) {
        $request->validate([
            'audio'    => [
                'required',
                'file',
                'mimetypes:audio/mpeg,audio/mp3,audio/webm,audio/ogg,audio/wav,video/mp4,video/webm',
                'max:30000',
            ],
            'language' => ['sometimes', 'string'],
        ]);

        try {
            $result = $this->chatService->transcribe(
                $request->file('audio'),
                $request->input('language')
            );

            return response()->json($result);
        } catch (ServiceException $exception) {
            return response()->json($exception->getPayload(), $exception->getStatus());
        }
    }
}
