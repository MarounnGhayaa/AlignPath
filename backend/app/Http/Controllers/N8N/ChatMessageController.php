<?php

namespace App\Http\Controllers\N8N;

use App\Http\Controllers\Controller;
use App\Services\N8N\ChatMessageService;
use Illuminate\Http\Request;
class ChatMessageController extends Controller {
    public function __construct(private ChatMessageService $service) {}

    public function index(Request $request) {
        $v = $request->validate([
            'start'     => ['required','date'],
            'tz'        => ['nullable','timezone'],
            'thread_id' => ['nullable','integer'],
            'limit'     => ['nullable','integer','min:1','max:1000'],
            'cursor'    => ['nullable','string'],
        ]);

        $res = $this->service->listForDay(
            $v['start'],
            $v['tz']        ?? 'UTC',
            $v['thread_id'] ?? null,
            $v['limit']     ?? 500,
            $v['cursor']    ?? null
        );

        return response()->json($res);
    }
}
