<?php

namespace App\Services\Users;

use App\Models\Quest;

class QuestService {
    public function listByPath(int $pathId) {
        return Quest::where('path_id', $pathId)->get();
    }
}
