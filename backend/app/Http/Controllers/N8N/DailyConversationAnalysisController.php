<?php

namespace App\Http\Controllers\N8N;

use App\Http\Controllers\Controller;
use App\Services\N8N\DailyConversationAnalysisService;
use Illuminate\Http\Request;

class DailyConversationAnalysisController extends Controller {
    public function __construct(private DailyConversationAnalysisService $service) {}

    public function store(Request $request) {
        $payload = $request->json()->all();

        $rules = [
            'user_id'    => ['required','integer'],
            'thread_id'  => ['nullable','integer'],
            'day'        => ['required','date'],
            'summary'    => ['nullable','string'],
            'attributes' => ['nullable','array'],
            'raw'        => ['nullable','array'],
        ];

        if (is_array($payload) && array_is_list_polyfill($payload)) {
            foreach ($payload as $item) {
                validator($item, $rules)->validate();
            }
        } else {
            validator($payload, $rules)->validate();
        }

        $result = $this->service->storeMany($payload);
        return response()->json($result, 201);
    }
}

if (!function_exists('array_is_list_polyfill')) {
    function array_is_list_polyfill(array $array)  {
        if (function_exists('array_is_list')) return array_is_list($array);
        if ($array === []) return true;
        return array_keys($array) === range(0, count($array) - 1);
    }
}
