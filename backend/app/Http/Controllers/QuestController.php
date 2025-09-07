<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Quest;

class QuestController extends Controller
{
    public function getQuestsByPath(Request $request, $pathId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $quests = Quest::where('path_id', $pathId)->get();

        return response()->json($quests);
    }
}
