<?php

namespace App\Services\Users;

use App\Models\User; 
use Illuminate\Support\Facades\Hash; 

class UserService {
    public static function update( $data, $id){
        $user = User::find($id);

        if (!$user) {
            return null;
        }
        if (isset($data['username'])) {
            $user->username = $data['username'];
        }
        if (isset($data['email'])) {
            $user->email = $data['email'];
        }
        if (isset($data['password']) && !empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        if (isset($data['role'])) {
            $user->role = $data['role'];
        }
        if (isset($data['location'])) {
            $user->location = $data['location'];
        }
        if (isset($data['position'])) {
            $user->position = $data['position'];
        }
        if (isset($data['company'])) {
            $user->company = $data['company'];
        }
        $user->save();
        return $user; 
    }
}