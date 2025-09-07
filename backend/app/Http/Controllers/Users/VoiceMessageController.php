<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\VoiceMessage;
use App\Jobs\ProcessVoiceMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class VoiceMessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'voice' => 'required|file|mimes:mp3,wav,m4a,ogg|max:10240',
        ]);

        $senderId = JWTAuth::parseToken()->authenticate()->id;

        // Save voice file to storage/app/voices
        $path = $request->file('voice')->store('voices');

        $voice = VoiceMessage::create([
            'sender_id'   => $senderId,
            'receiver_id' => $request->receiver_id,
            'file_path'   => $path,
        ]);

        // Run job immediately for testing (use dispatch() in production)
        ProcessVoiceMessage::dispatchSync($voice);

        return response()->json([
            'message' => 'Voice uploaded successfully, processing...',
            'voice_message' => $voice,
        ]);
    }

    public function index(Request $request, $otherUserId)
    {
        $userId = JWTAuth::parseToken()->authenticate()->id;

        $messages = VoiceMessage::with(['sender', 'receiver'])
            ->where(function ($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $userId)->where('receiver_id', $otherUserId);
            })
            ->orWhere(function ($q) use ($userId, $otherUserId) {
                $q->where('sender_id', $otherUserId)->where('receiver_id', $userId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    public function download($id)
    {
        $voice = VoiceMessage::findOrFail($id);

        if (!Storage::exists($voice->file_path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return Storage::download($voice->file_path);
    }
}
