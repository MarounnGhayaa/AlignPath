<?php

namespace App\Services\Users;

use App\Models\Quest;

class QuestService
{
    public function listByPath(int $pathId): array
    {
        return Quest::where('path_id', $pathId)
            ->get()
            ->toArray();
    }
}
