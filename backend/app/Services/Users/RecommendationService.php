<?php

namespace App\Services\Users;

use App\Models\Recommendation;

class RecommendationService {
    public function listForUser(int $userId) {
        return Recommendation::where('user_id', $userId)
            ->select('id', 'career_name', 'description')
            ->get()
            ->toArray();
    }
}
