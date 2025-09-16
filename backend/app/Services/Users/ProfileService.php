<?php

namespace App\Services\Users;

use App\Models\User;
use App\Models\UserPath;

class ProfileService {
    public function getUser(int $id) {
        return User::find($id);
    }

    public function listUserPaths(int $userId) {
        return UserPath::where('user_id', $userId)
            ->join('paths', 'users_paths.path_id', '=', 'paths.id')
            ->select(
                'paths.id as id',
                'paths.name as title',
                'paths.tag as tag',
                'users_paths.progress_percentage',
                'users_paths.date_saved'
            )
            ->get();
    }
}
