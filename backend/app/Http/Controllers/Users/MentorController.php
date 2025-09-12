<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MentorController extends Controller {
    public function index(Request $request) {
        $q = trim((string) $request->query('search', ''));

        $mentors = User::query()
            ->with('expertise')
            ->where('role', 'mentor')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('username', 'like', "%{$q}%")
                       ->orWhereHas('expertise', fn($e) => $e->where('name', 'like', "%{$q}%"));
                });
            })
            ->orderBy('username')
            ->limit(100)
            ->get();

        $payload = $mentors->map(function (User $u) {
            return [
                'id'        => $u->id,
                'name'      => $u->username,
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

        return response()->json($payload);
    }
}
