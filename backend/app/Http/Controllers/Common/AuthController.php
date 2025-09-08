<?php

namespace App\Http\Controllers\Common;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use  App\Services\Common\AuthService;

class AuthController extends Controller {

    /**
     * @OA\Post(
     *      path="/api/v0.1/guest/login",
     *      summary="User login",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email", "password"},
     *              @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful login",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="object"),
     *              @OA\Property(property="token", type="string", example="jwt_token_here")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(@OA\Property(property="error", type="string", example="Unauthorized"))
     *      )
     * )
     */
    public function login(Request $request){
        $user = AuthService::login($request);
        
        if($user)
            return $this->responseJSON($user);
            return $this->responseJSON(null, "error", 401);
    }

    /**
     * @OA\Post(
     *      path="/api/v0.1/guest/register",
     *      summary="User registration",
     *      tags={"Auth"},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "email", "password", "password_confirmation"},
     *              @OA\Property(property="name", type="string", example="John Doe"),
     *              @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *              @OA\Property(property="password", type="string", format="password", example="password"),
     *              @OA\Property(property="password_confirmation", type="string", format="password", example="password")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful registration",
     *          @OA\JsonContent(
     *              @OA\Property(property="user", type="object"),
     *              @OA\Property(property="token", type="string", example="jwt_token_here")
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *      )
     * )
     */
    public function register(Request $request){
        $user = AuthService::register($request);
        return $this->responseJSON($user);
    }
}
