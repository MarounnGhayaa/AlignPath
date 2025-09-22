<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserInfoRequest;
use App\Services\Users\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller {
    protected ProfileService $profiles;

    public function __construct(ProfileService $profiles) {
        $this->profiles = $profiles;
    }

    public function getUserInfo($id) {
        $user = $this->profiles->getUser((int) $id);

        return $this->responseJSON($user);
    }

    public function updateUserInfo(UpdateUserInfoRequest $request, $id) {
        $data = $request->validated();
        $updatedUser = $this->profiles->updateUser((int) $id, $data);

        return $this->responseJSON($updatedUser);
    }

    public function getUserPaths(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userPaths = $this->profiles->listUserPaths($user->id);

        return response()->json($userPaths);
    }
}
