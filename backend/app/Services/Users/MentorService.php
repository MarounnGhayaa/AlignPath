<?php

namespace App\Services\Users;

use App\Models\Recommendation;
use App\Models\User;
use Illuminate\Support\Str;

class MentorService {
    public function search(?User $student, string $query, int $limit = 100) {
        $search = trim($query);

        $mentorQuery = User::query()
            ->with('expertise')
            ->where('role', 'mentor');

        $terms = collect();

        if ($student && Str::lower((string) $student->role) === 'student') {
            $terms = Recommendation::query()
                ->where('user_id', $student->id)
                ->pluck('career_name')
                ->filter()
                ->flatMap(function ($name) {
                    $normalized = Str::lower(trim((string) $name));

                    if ($normalized === '') {
                        return [];
                    }

                    $parts = preg_split('/[\s,\/,&-]+/', $normalized) ?: [];

                    return collect([$normalized])
                        ->merge($parts)
                        ->map(fn($value) => trim($value))
                        ->filter(fn($value) => Str::length($value) >= 3);
                })
                ->unique()
                ->values();
        }

        if ($terms->isNotEmpty()) {
            $mentorQuery->where(function ($builder) use ($terms) {
                foreach ($terms as $term) {
                    $like = '%' . addcslashes($term, '%_') . '%';

                    $builder->orWhereRaw('LOWER(username) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(position) LIKE ?', [$like])
                        ->orWhereRaw('LOWER(company) LIKE ?', [$like])
                        ->orWhereHas('expertise', function ($q) use ($like) {
                            $q->whereRaw('LOWER(name) LIKE ?', [$like]);
                        });
                }
            });
        }

        $mentorQuery->when($search !== '', function ($builder) use ($search) {
            $term = '%' . addcslashes(Str::lower($search), '%_') . '%';

            $builder->where(function ($nested) use ($term) {
                $nested->orWhereRaw('LOWER(username) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(position) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(company) LIKE ?', [$term])
                    ->orWhereHas('expertise', function ($q) use ($term) {
                        $q->whereRaw('LOWER(name) LIKE ?', [$term]);
                    });
            });
        });

        return $mentorQuery
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
