<?php

namespace App\Services\N8N;

use App\Models\DailyConversationAnalysis;
use Illuminate\Support\Carbon;

class DailyConversationAnalysisService {
    public function storeMany($payload) {
        $items = (is_array($payload) && array_is_list_polyfill($payload)) ? $payload : [$payload];

        $saved = collect();
        foreach ($items as $item) {
            $day = Carbon::parse($item['day'])->toDateString();

            $row = DailyConversationAnalysis::updateOrCreate(
                [
                    'user_id'   => $item['user_id'],
                    'thread_id' => $item['thread_id'] ?? null,
                    'day'       => $day,
                ],
                [
                    'summary'    => $item['summary'] ?? '',
                    'attributes' => $item['attributes'] ?? [],
                    'raw'        => $item['raw'] ?? [],
                ]
            );

            $saved->push($row);
        }

        return ['count' => $saved->count(), 'data' => $saved];
    }
}

if (!function_exists('array_is_list_polyfill')) {
    function array_is_list_polyfill(array $array): bool {
        if (function_exists('array_is_list')) return array_is_list($array);
        if ($array === []) return true;
        return array_keys($array) === range(0, count($array) - 1);
    }
}
