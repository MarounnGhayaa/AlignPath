<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Users\UserService;
use App\Models\User;
use App\Models\UserPath;
use App\Models\Path;

class ProfileController extends Controller {
    public function getUserInfo($id) {
        $user = User::find($id);

        return $this->responseJSON($user);  
    }

    public function updateUserInfo(Request $request, $id) {
        $data = $request->all();
        $updatedUser = UserService::update($data, $id);    

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
