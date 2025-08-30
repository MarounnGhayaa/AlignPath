<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Users\UserService;
use App\Models\User;
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
}
