<?php

namespace App\Services\Users;

use App\Models\Problem;

class ProblemService {
    public function listByPath(int $pathId) {
        return Problem::where('path_id', $pathId)->get();
    }

    public function getById(int $problemId) {
        return Problem::find($problemId);
    }
}
