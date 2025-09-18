<?php

namespace App\Services\Users;

use App\Models\Skill;
use App\Models\UserPath;

class SkillService
{
    public function listByPath(int $pathId): array
    {
        return Skill::where('path_id', $pathId)
            ->get()
            ->toArray();
    }

    public function updateValue(Skill $skill, int $value, int $userId): Skill
    {
        $skill->value = $value;
        $skill->save();

        try {
            $average = Skill::where('path_id', $skill->path_id)->avg('value');
            if ($average !== null) {
                UserPath::where('user_id', $userId)
                    ->where('path_id', $skill->path_id)
                    ->update(['progress_percentage' => (int) round($average)]);
            }
        } catch (\Throwable $e) {
            // Intentionally swallow progress update errors to not block the main mutation.
        }

        return $skill;
    }
}
