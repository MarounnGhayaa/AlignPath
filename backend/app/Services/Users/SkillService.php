<?php

namespace App\Services\Users;

use App\Models\Skill;
use App\Models\UserPath;
use Illuminate\Validation\ValidationException;

class SkillService {
    public function listByPath(int $pathId) {
        return Skill::where('path_id', $pathId)->get();
    }

    /**
     * Validate and update a skill value, then recompute the user's path progress (avg of path skills).
     *
     * @throws ValidationException
     */
    public function updateSkillForUser(int $userId, Skill $skill, array $data){
        $value = $data['value'] ?? null;

        if (!is_numeric($value) || $value < 0 || $value > 100) {
            throw ValidationException::withMessages(['value' => 'The value must be between 0 and 100.']);
        }

        $skill->value = (int) $value;
        $skill->save();

        try {
            $avg = Skill::where('path_id', $skill->path_id)->avg('value');
            if ($avg !== null) {
                UserPath::where('user_id', $userId)
                    ->where('path_id', $skill->path_id)
                    ->update(['progress_percentage' => (int) round($avg)]);
            }
        } catch (\Throwable $e) {
            // non-fatal
        }

        return $skill;
    }
}
