<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDirectoryController extends Controller {
    public function index(Request $request) {
        $me = Auth::user();
        abort_unless($me !== null, 401);
        abort_unless(strtolower((string) $me->role) === 'mentor', 403);

        $q     = trim((string) $request->query('search', ''));
        $limit = (int) $request->query('limit', 100);
        $limit = $limit > 0 ? min($limit, 200) : 100;

        $users = User::query()
            ->with('expertise')
            ->where('role', 'student')
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($qq) use ($q) {
                    $qq->where('username', 'like', "%{$q}%")
                       ->orWhere('company', 'like', "%{$q}%")
                       ->orWhere('position', 'like', "%{$q}%")
                       ->orWhereHas('expertise', fn ($e) => $e->where('name', 'like', "%{$q}%"));
                });
            })
            ->orderBy('username')
            ->limit($limit)
            ->get();

        $payload = $users->map(function (User $u) {
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
