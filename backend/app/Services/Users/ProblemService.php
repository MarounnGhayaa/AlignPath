<?php

namespace App\Services\Users;

use App\Exceptions\ServiceException;
use App\Models\Problem;

class ProblemService {
    public function listByPath(int $pathId) {
        return Problem::where('path_id', $pathId)
            ->get()
            ->toArray();
    }

    public function findById(int $problemId) {
        $problem = Problem::find($problemId);

        if (!$problem) {
            throw new ServiceException('Problem not found', 404, [
                'error' => 'Problem not found',
            ]);
        }

        return $problem;
    }
}
