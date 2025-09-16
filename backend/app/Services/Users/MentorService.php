<?php

namespace App\Services\Users;

use App\Models\User;
use Illuminate\Support\Collection;

class MentorService {
    public function search(?string $q = null): Collection {
        $q = trim((string) $q);

        $mentors = User::query()
            ->with('expertise')
            ->where('role', 'mentor')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('name', 'like', $like)
                        ->orWhere('username', 'like', $like)
                        ->orWhere('email', 'like', $like)
                        ->orWhere('company', 'like', $like)
                        ->orWhere('position', 'like', $like);
                });
            })
            ->orderBy('name')
            ->get();

        return $mentors->map(function (User $u) {
            return [
                'id'        => $u->id,
                'name'      => $u->name,
                'username'  => $u->username,
                'position'  => $u->position
                    ? ($u->company ? "{$u->position} at {$u->company}" : $u->position)
                    : ($u->company ?? ''),
                'company'   => $u->company,
                'expertise' => $u->expertise->pluck('name')->values(),
                'avatar'    => $u->avatar_url,
                'isOnline'  => $u->isOnline(),
                'lastSeen'  => optional($u->last_seen)?->toISOString(),
            ];
        });
    }
}
