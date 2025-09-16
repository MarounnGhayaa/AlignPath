<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller {
    public function destroy(User $user) {
        $auth = Auth::user();
        if (!$auth || $auth->role !== 'admin') {
            abort(403, 'Forbidden');
        }

        if ($auth->id === $user->id) {
            return response()->json(['message' => 'Cannot delete yourself'], 422);
        }

        $user->delete();
        return response()->json(['status' => 'ok']);
    }
}
