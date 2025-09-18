<?php

namespace App\Services\Users;

use App\Models\User;

class MentorService {
    public function search(string $query, int $limit = 100) {
        $search = trim($query);

        return User::query()
            ->with('expertise')
            ->where('role', 'mentor')
            ->when($search !== '', function ($builder) use ($search) {
                $builder->where(function ($nested) use ($search) {
                    $nested->where('username', 'like', "%{$search}%")
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
