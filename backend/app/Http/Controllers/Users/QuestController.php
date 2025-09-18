<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\QuestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuestController extends Controller {
    protected QuestService $quests;

    public function __construct(QuestService $quests) {
        $this->quests = $quests;
    }

    public function getQuestsByPath(Request $request, $pathId) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $quests = $this->quests->listByPath((int) $pathId);

        return response()->json($quests);
    }
}
