<?php

namespace App\Jobs;

use App\Models\VoiceMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Storage;

class ProcessVoiceMessage implements ShouldQueue
{
    use Dispatchable, Queueable;

    protected VoiceMessage $voice;

    public function __construct(VoiceMessage $voice)
    {
        $this->voice = $voice;
    }

    public function handle()
    {
        try {
            $filePath = $this->voice->file_path;
            Log::info("Processing voice file: $filePath");

            if (!Storage::exists($filePath)) {
                throw new \Exception("Voice file not found via Storage: $filePath");
            }

            // Get absolute path
            $localPath = Storage::path($filePath);

            // 1️⃣ Transcribe audio
            $transcript = $this->transcribeAudio($localPath);
            $this->voice->update(['transcript' => $transcript]);

            // 2️⃣ Gemini NLP
            $geminiApiKey = config('services.gemini.key');
            $prompt = "Analyze the following text and return a JSON with keys: summary, sentiment, keywords:\n\n$transcript";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $geminiApiKey,
            ])->post('https://gemini.googleapis.com/v1/your-gemini-model-endpoint', [
                'prompt' => $prompt,
                'max_tokens' => 300,
            ]);

            $nlpDataRaw = $response->json()['choices'][0]['message']['content'] ?? '{}';
            $nlpData = json_decode($nlpDataRaw, true) ?: [];
            $this->voice->update(['nlp' => $nlpData]);

            // 3️⃣ Broadcast to Express server
            Http::post('http://localhost:4000/events', [
                'type' => 'voice_nlp_ready',
                'payload' => [
                    'voice_id'   => $this->voice->id,
                    'sender_id'  => $this->voice->sender_id,
                    'receiver_id'=> $this->voice->receiver_id,
                    'transcript' => $transcript,
                    'nlp'        => $nlpData,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error("ProcessVoiceMessage failed: " . $e->getMessage());
            throw $e;
        }
    }

    protected function transcribeAudio(string $localPath): string
{
    $hfApiKey = config('services.huggingface.key');

    // Ensure file exists
    if (!Storage::exists($this->voice->file_path)) {
        Log::error("Voice file not found via Storage: {$this->voice->file_path}");
        return 'Transcription failed: file not found';
    }

    try {
        // Send audio file directly to Hugging Face ASR (supports MP3, WAV, etc.)
        $response = Http::withHeaders([
    'Authorization' => 'Bearer ' . $hfApiKey,
])->attach(
    'file', fopen($localPath, 'r'), basename($localPath)
)->post('https://api-inference.huggingface.co/models/facebook/wav2vec2-large-960h-lv60-self');


        $text = $response->json()['text'] ?? null;

        if (!$text) {
            Log::error("Hugging Face ASR returned empty or invalid response", [
                'status' => $response->status(),
                'body' => $response->body(),
                'response' => $response->json(),
            ]);
            return 'Transcription failed: API error';
        }

        return $text;

    } catch (\Exception $e) {
        Log::error("Hugging Face ASR failed: " . $e->getMessage());
        return 'Transcription failed: API error';
    }
}

}
