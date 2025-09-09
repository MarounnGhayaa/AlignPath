<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserInfoRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Users\UserService;
use App\Models\User;
use App\Models\UserPath;

class ProfileController extends Controller
{
    public function getUserInfo($id)
    {
        $user = User::find($id);

        return $this->responseJSON($user);
    }

    public function updateUserInfo(UpdateUserInfoRequest $request, $id)
    {
        $data = $request->validated();
        $updatedUser = UserService::update($data, (int) $id);

        return $this->responseJSON($updatedUser);
    }

    public function getUserPaths(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userPaths = UserPath::where('user_id', $user->id)
            ->join('paths', 'users_paths.path_id', '=', 'paths.id')
            ->select(
                'paths.id as id',
                'paths.name as title',
                'paths.tag as tag',
                'users_paths.progress_percentage',
                'users_paths.date_saved'
            )
            ->get();

        return response()->json($userPaths);
    }
}
