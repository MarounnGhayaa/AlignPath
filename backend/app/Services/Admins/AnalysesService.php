<?php

namespace App\Services\Admins;

use App\Models\DailyConversationAnalysis;

class AnalysesService {
    public function list() {
        return DailyConversationAnalysis::query()
            ->select('id','user_id','thread_id','day','summary','attributes')
            ->whereNotNull('summary')
            ->where('summary', '!=', '')
            ->with(['user:id,username,email'])
            ->orderByDesc('day')->orderByDesc('updated_at')
            ->get();
    }
}
