<?php

namespace App\Http\Controllers\Users;

use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Services\Users\GeminiChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GeminiController extends Controller {
    protected GeminiChatService $geminiChat;

    public function __construct(GeminiChatService $geminiChat) {
        $this->geminiChat = $geminiChat;
    }

    public function chat(Request $request){
        $data = $request->validate([
            'thread_id'            => ['nullable','integer'],
            'messages'             => 'required|array|min:1',
            'messages.*.role'      => ['required','string', Rule::in(['user','model'])],
            'messages.*.content'   => 'required|string',
            'system'               => 'nullable|string',
            'temperature'          => 'nullable|numeric',
            'maxOutputTokens'      => 'nullable|integer',
        ]);

        $user = Auth::user();

        try {
            $result = $this->geminiChat->chat($data, $user);
            return response()->json($result);
        } catch (ServiceException $exception) {
            return response()->json($exception->getPayload(), $exception->getStatus());
        }
    }
}
