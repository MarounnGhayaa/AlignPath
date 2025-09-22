<?php

namespace App\Http\Controllers\Common;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Controllers\Controller;
use  App\Services\Common\AuthService;

class AuthController extends Controller {
    public function login(LoginRequest $request){
        $user = AuthService::login($request);
        
        if($user)
            return $this->responseJSON($user);
            return $this->responseJSON(null, "error", 401);
    }

    public function register(RegisterRequest $request){
        $user = AuthService::register($request);
        return $this->responseJSON($user);
    }
}
