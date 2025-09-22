<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Admins\UsersService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class UsersController extends Controller {
    public function __construct(private UsersService $users) {}

    public function destroy(User $user) {
        try {
            $this->users->deleteUser($user, Auth::user());
            return response()->json(['status' => 'ok']);
        } catch (AuthorizationException $e) {
            abort(403, 'Forbidden');
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
