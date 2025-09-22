<?php

namespace App\Services\Users;

use App\Models\User;

class UserDirectoryService {
    public function searchStudents(string $query, int $limit) {
        $search = trim($query);
        $limit = $limit > 0 ? min($limit, 200) : 100;

        return User::query()
            ->with('expertise')
            ->where('role', 'student')
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($nested) use ($search) {
                    $nested->where('username', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhereHas('expertise', fn($expertise) => $expertise->where('name', 'like', "%{$search}%"));
                });
            })
            ->orderBy('username')
            ->limit($limit)
            ->get()
            ->map(function (User $user) {
                return [
                    'id'        => $user->id,
                    'name'      => $user->username,
                    'position'  => $user->position
                        ? ($user->company ? "{$user->position} at {$user->company}" : $user->position)
                        : ($user->company ?? ''),
                    'company'   => $user->company,
                    'expertise' => $user->expertise->pluck('name')->values(),
                    'avatar'    => $user->avatar_url,
                    'isOnline'  => $user->isOnline(),
                    'lastSeen'  => optional($user->last_seen)?->toISOString(),
                ];
            })
            ->values()
            ->all();
    }
}
