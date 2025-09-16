<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\DailyConversationAnalysis;
use Illuminate\Http\Request;

class AnalysesController extends Controller {
    public function getAnalyses() {
        return DailyConversationAnalysis::query()
            ->select('id','user_id','thread_id','day','summary','attributes')
            // include user's email alongside username
            ->with(['user:id,username,email'])
            ->orderByDesc('day')->orderByDesc('updated_at')
            ->get();
    }
}
