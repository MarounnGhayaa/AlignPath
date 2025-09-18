<?php

namespace App\Http\Controllers\Users;

use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Models\ChatThread;
use App\Services\Users\GeminiThreadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GeminiThreadController extends Controller {
    protected GeminiThreadService $threads;

    public function __construct(GeminiThreadService $threads) {
        $this->threads = $threads;
    }

    public function index(Request $request) {
        $user = Auth::user();
        $limit = min(max((int) $request->get('limit', 20), 1), 100);

        $payload = $this->threads->listThreads($user, $limit);

        return response()->json($payload);
    }

    public function show(Request $request, ChatThread $thread) {
        $user = Auth::user();

        try {
            $payload = $this->threads->showThread($user, $thread);
            return response()->json($payload);
        } catch (ServiceException $exception) {
            return response()->json($exception->getPayload(), $exception->getStatus());
        }
    }

    public function update(Request $request, ChatThread $thread) {
        $user = Auth::user();
        $data = $request->validate([
            'title' => 'nullable|string|max:120',
        ]);

        try {
            $this->threads->updateThread($user, $thread, $data);
            return response()->json(['ok' => true]);
        } catch (ServiceException $exception) {
            return response()->json($exception->getPayload(), $exception->getStatus());
        }
    }

    public function destroy(Request $request, ChatThread $thread) {
        $user = Auth::user();

        try {
            $this->threads->deleteThread($user, $thread);
            return response()->json(['ok' => true]);
        } catch (ServiceException $exception) {
            return response()->json($exception->getPayload(), $exception->getStatus());
        }
    }
}
