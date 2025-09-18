<?php

namespace App\Services\Users;

use App\Models\LearningResource;

class LearningResourceService
{
    public function listByPath(int $pathId): array
    {
        return LearningResource::where('path_id', $pathId)
            ->get()
            ->toArray();
    }
}
